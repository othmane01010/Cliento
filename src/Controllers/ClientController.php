<?php

namespace App\Controllers;

use App\Models\Client;
use App\Traits\FileUploadTrait;
use App\Traits\LoggerTrait;
use App\Traits\SanitizerTrait;
use App\Traits\SecurityTrait;
use App\Traits\ValidationTrait;
use App\Exceptions\ValidationException;
use App\Exceptions\SecurityException;
use App\Exceptions\NotFoundException;

class ClientController extends BaseController
{
    use SanitizerTrait, SecurityTrait, ValidationTrait, FileUploadTrait, LoggerTrait;

    public function index(): void
    {
        $this->requireAuth();

        $searchTerm = $_GET['search'] ?? null;
        $clients = !empty($searchTerm) 
            ? Client::search($searchTerm) 
            : Client::all();

        $this->render('clients/index', [
            'clients'    => $clients,
            'searchTerm' => $searchTerm,
            'csrfToken'  => $this->generateCsrfToken(),
        ]);
    }

    public function show(int $id): void
    {
        $this->requireAuth();

        $client = Client::find($id);
        if (!$client) {
            throw new NotFoundException("Client introuvable.");
        }

        $subscriptions = Client::getSubscriptions($id);

        $this->render('clients/show', [
            'client'        => $client,
            'subscriptions' => $subscriptions,
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();

        $this->render('clients/create', [
            'csrfToken' => $this->generateCsrfToken(),
            'errors'    => [],
            'old'       => [],
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();

        try {
            $this->checkCsrfOrThrow($_POST['csrf_token'] ?? null);

            $fullName = $this->sanitizeString($_POST['full_name'] ?? '');
            $cin      = !empty($_POST['cin']) ? strtoupper($this->sanitizeString($_POST['cin'])) : null;
            $email    = $this->sanitizeString($_POST['email'] ?? '');
            $phone    = $this->sanitizeString($_POST['phone'] ?? '');

            $errors = $this->validateRequired(['full_name' => $fullName, 'phone' => $phone], ['full_name', 'phone']);

            if ($nameErr = $this->validateName($fullName)) {
                $errors['full_name'] = $nameErr;
            }

            if (!empty($cin) && Client::cinExists($cin)) {
                $errors['cin'] = "Ce numéro de CIN est déjà enregistré pour un autre client.";
            }

            if ($phoneErr = $this->validateMoroccanPhone($phone)) {
                $errors['phone'] = $phoneErr;
            } elseif (Client::phoneExists($phone)) {
                $errors['phone'] = "Ce numéro de téléphone est déjà utilisé.";
            }

            if (!empty($email)) {
                if ($emailErr = $this->validateEmail($email)) {
                    $errors['email'] = $emailErr;
                } elseif (Client::emailExists($email)) {
                    $errors['email'] = "Cette adresse email est déjà utilisée.";
                }
            }

            $this->checkValidation($errors);

            $photoName = $this->uploadImage($_FILES['photo'] ?? []) ?? 'default.png';

            $clientId = Client::create([
                'full_name' => $fullName,
                'cin'       => $cin,
                'photo'     => $photoName,
                'email'     => !empty($email) ? $email : null,
                'phone'     => $phone,
            ]);

            self::logInfo("Nouveau client créé avec succès ID: {$clientId}");
            $this->redirect('/clients');

        } catch (ValidationException | SecurityException $e) {
            $this->render('clients/create', [
                'error'     => $e->getMessage(),
                'errors'    => ($e instanceof ValidationException) ? $e->getErrors() : [],
                'old'       => $_POST,
                'csrfToken' => $this->generateCsrfToken(),
            ]);
        }
    }

    public function edit(int $id): void
    {
        $this->requireAuth();

        $client = Client::find($id);
        if (!$client) {
            throw new NotFoundException("Client introuvable.");
        }

        $this->render('clients/edit', [
            'client'    => $client,
            'csrfToken' => $this->generateCsrfToken(),
            'errors'    => [],
        ]);
    }

    public function update(int $id): void
    {
        $this->requireAuth();

        $client = Client::find($id);
        if (!$client) {
            throw new NotFoundException("Client introuvable.");
        }

        try {
            $this->checkCsrfOrThrow($_POST['csrf_token'] ?? null);

            $fullName = $this->sanitizeString($_POST['full_name'] ?? '');
            $cin      = !empty($_POST['cin']) ? strtoupper($this->sanitizeString($_POST['cin'])) : null;
            $email    = $this->sanitizeString($_POST['email'] ?? '');
            $phone    = $this->sanitizeString($_POST['phone'] ?? '');

            $errors = $this->validateRequired(['full_name' => $fullName, 'phone' => $phone], ['full_name', 'phone']);

            if ($nameErr = $this->validateName($fullName)) {
                $errors['full_name'] = $nameErr;
            }

            if (!empty($cin) && Client::cinExists($cin, $id)) {
                $errors['cin'] = "Ce numéro de CIN est déjà enregistré pour un autre client.";
            }

            if ($phoneErr = $this->validateMoroccanPhone($phone)) {
                $errors['phone'] = $phoneErr;
            } elseif (Client::phoneExists($phone, $id)) {
                $errors['phone'] = "Ce numéro de téléphone est déjà utilisé.";
            }

            if (!empty($email)) {
                if ($emailErr = $this->validateEmail($email)) {
                    $errors['email'] = $emailErr;
                } elseif (Client::emailExists($email, $id)) {
                    $errors['email'] = "Cette adresse email est déjà utilisée.";
                }
            }

            $this->checkValidation($errors);

            $photoName = $client['photo'];
            $uploadedPhoto = $this->uploadImage($_FILES['photo'] ?? []);
            if ($uploadedPhoto !== null) {
                if ($client['photo'] !== 'default.png') {
                    $this->deleteImage($client['photo']);
                }
                $photoName = $uploadedPhoto;
            }

            Client::update($id, [
                'full_name' => $fullName,
                'cin'       => $cin,
                'photo'     => $photoName,
                'email'     => !empty($email) ? $email : null,
                'phone'     => $phone,
            ]);

            self::logInfo("Client mis à jour avec succès ID: {$id}");
            $this->redirect('/clients');

        } catch (ValidationException | SecurityException $e) {
            $this->render('clients/edit', [
                'client'    => array_merge($client, $_POST),
                'error'     => $e->getMessage(),
                'errors'    => ($e instanceof ValidationException) ? $e->getErrors() : [],
                'csrfToken' => $this->generateCsrfToken(),
            ]);
        }
    }

    public function delete(int $id): void
    {
        $this->requireAuth();

        try {
            $this->checkCsrfOrThrow($_POST['csrf_token'] ?? null);

            $client = Client::find($id);
            if ($client) {
                if ($client['photo'] !== 'default.png') {
                    $this->deleteImage($client['photo']);
                }
                Client::delete($id);
                self::logInfo("Client supprimé avec succès ID: {$id}");
            }

            $this->redirect('/clients');

        } catch (SecurityException $e) {
            $this->redirect('/clients');
        }
    }

    public function search(): void
    {
        $this->requireAuth();

        $term = trim($_GET['q'] ?? '');
        $results = !empty($term) ? Client::search($term) : [];

        $this->jsonResponse($results);
    }
}