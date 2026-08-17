<?php

namespace App\Controllers;

use App\Models\Plan;
use App\Traits\LoggerTrait;
use App\Traits\SanitizerTrait;
use App\Traits\SecurityTrait;
use App\Traits\ValidationTrait;
use App\Exceptions\ValidationException;
use App\Exceptions\SecurityException;
use App\Exceptions\NotFoundException;

class PlanController extends BaseController
{
    use SanitizerTrait, SecurityTrait, ValidationTrait, LoggerTrait;

    public function index(): void
    {
        $this->requireAuth();

        $plans = Plan::all();

        $this->render('plans/index', [
            'plans'     => $plans,
            'csrfToken' => $this->generateCsrfToken(),
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();

        $this->render('plans/create', [
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

            $name         = $this->sanitizeString($_POST['name'] ?? '');
            $price        = $_POST['price'] ?? null;
            $durationDays = $_POST['duration_days'] ?? null;

            $errors = $this->validateRequired(
                ['name' => $name, 'price' => $price, 'duration_days' => $durationDays],
                ['name', 'price', 'duration_days']
            );

            if ($priceErr = $this->validatePrice($price)) {
                $errors['price'] = $priceErr;
            }
            if ($durationErr = $this->validateDuration($durationDays)) {
                $errors['duration_days'] = $durationErr;
            }

            $this->checkValidation($errors);

            $planId = Plan::create([
                'name'          => $name,
                'price'         => (float) $price,
                'duration_days' => (int) $durationDays,
                'is_active'     => isset($_POST['is_active']),
            ]);

            self::logInfo("Nouveau plan d'abonnement créé ID: {$planId}");
            $this->redirect('/plans');

        } catch (ValidationException | SecurityException $e) {
            $this->render('plans/create', [
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

        $plan = Plan::find($id);
        if (!$plan) {
            throw new NotFoundException("Plan d'abonnement introuvable.");
        }

        $this->render('plans/edit', [
            'plan'      => $plan,
            'csrfToken' => $this->generateCsrfToken(),
            'errors'    => [],
        ]);
    }

    public function update(int $id): void
    {
        $this->requireAuth();

        $plan = Plan::find($id);
        if (!$plan) {
            throw new NotFoundException("Plan d'abonnement introuvable.");
        }

        try {
            $this->checkCsrfOrThrow($_POST['csrf_token'] ?? null);

            $name         = $this->sanitizeString($_POST['name'] ?? '');
            $price        = $_POST['price'] ?? null;
            $durationDays = $_POST['duration_days'] ?? null;

            $errors = $this->validateRequired(
                ['name' => $name, 'price' => $price, 'duration_days' => $durationDays],
                ['name', 'price', 'duration_days']
            );

            if ($priceErr = $this->validatePrice($price)) {
                $errors['price'] = $priceErr;
            }
            if ($durationErr = $this->validateDuration($durationDays)) {
                $errors['duration_days'] = $durationErr;
            }

            $this->checkValidation($errors);

            Plan::update($id, [
                'name'          => $name,
                'price'         => (float) $price,
                'duration_days' => (int) $durationDays,
                'is_active'     => isset($_POST['is_active']),
            ]);

            self::logInfo("Plan d'abonnement mis à jour ID: {$id}");
            $this->redirect('/plans');

        } catch (ValidationException | SecurityException $e) {
            $this->render('plans/edit', [
                'plan'      => array_merge($plan, $_POST),
                'error'     => $e->getMessage(),
                'errors'    => ($e instanceof ValidationException) ? $e->getErrors() : [],
                'csrfToken' => $this->generateCsrfToken(),
            ]);
        }
    }

    public function toggle(int $id): void
    {
        $this->requireAuth();

        try {
            $this->checkCsrfOrThrow($_POST['csrf_token'] ?? null);

            $plan = Plan::find($id);
            if ($plan) {
                $newStatus = !$plan['is_active'];
                Plan::toggleActive($id, $newStatus);
                self::logInfo("Statut du plan ID {$id} changé en: " . ($newStatus ? 'Actif' : 'Inactif'));
            }

            $this->redirect('/plans');

        } catch (SecurityException $e) {
            $this->redirect('/plans');
        }
    }

    public function delete(int $id): void
    {
        $this->requireAuth();

        try {
            $this->checkCsrfOrThrow($_POST['csrf_token'] ?? null);

            
            if (Plan::hasSubscriptions($id)) {
                self::logWarning("Tentative de suppression échouée du plan ID {$id} (Abonnements existants)");
                $_SESSION['flash_error'] = "Impossible de supprimer ce plan car des abonnements y sont associés. Désactivez-le plutôt.";
                $this->redirect('/plans');
            }

            Plan::delete($id);
            self::logInfo("Plan d'abonnement supprimé avec succès ID: {$id}");
            $this->redirect('/plans');

        } catch (SecurityException $e) {
            $this->redirect('/plans');
        }
    }
}