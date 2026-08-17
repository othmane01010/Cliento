<?php

namespace App\Controllers;

use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Traits\DateHelperTrait;
use App\Traits\LoggerTrait;
use App\Traits\SanitizerTrait;
use App\Traits\SecurityTrait;
use App\Traits\ValidationTrait;
use App\Exceptions\ValidationException;
use App\Exceptions\SecurityException;
use App\Exceptions\NotFoundException;

class SubscriptionController extends BaseController
{
    use SanitizerTrait, SecurityTrait, ValidationTrait, DateHelperTrait, LoggerTrait;

    public function index(): void
    {
        $this->requireAuth();

        Subscription::updateExpiredStatuses();

        $statusFilter = $_GET['status'] ?? null;
        $allowedStatuses = ['ACTIVE', 'EXPIRING_SOON', 'EXPIRED', 'CANCELLED'];

        if ($statusFilter !== null && !in_array($statusFilter, $allowedStatuses, true)) {
            $statusFilter = null;
        }

        $subscriptions = Subscription::getAllWithDetails($statusFilter);

        $this->render('subscriptions/index', [
            'subscriptions' => $subscriptions,
            'currentFilter' => $statusFilter,
            'csrfToken'     => $this->generateCsrfToken(),
        ]);
    }

    public function show(int $id): void
    {
        $this->requireAuth();

        $subscription = Subscription::getDetailsById($id);
        if (!$subscription) {
            throw new NotFoundException("Abonnement introuvable.");
        }

        $remainingDays = self::calculateRemainingDays($subscription['end_date']);

        $this->render('subscriptions/show', [
            'subscription'  => $subscription,
            'remainingDays' => $remainingDays,
            'csrfToken'     => $this->generateCsrfToken(),
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();

        $selectedClientId = isset($_GET['client_id']) ? (int) $_GET['client_id'] : null;

        $clients = Client::all();
        $plans   = Plan::getActivePlans();

        $this->render('subscriptions/create', [
            'clients'          => $clients,
            'plans'            => $plans,
            'selectedClientId' => $selectedClientId,
            'todayDate'        => date('Y-m-d'),
            'csrfToken'        => $this->generateCsrfToken(),
            'errors'           => [],
            'old'              => [],
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();

        try {
            $this->checkCsrfOrThrow($_POST['csrf_token'] ?? null);

            $clientId  = (int) ($_POST['client_id'] ?? 0);
            $planId    = (int) ($_POST['plan_id'] ?? 0);
            $startDate = trim($_POST['start_date'] ?? '');

            $errors = $this->validateRequired(
                ['client_id' => $clientId, 'plan_id' => $planId, 'start_date' => $startDate],
                ['client_id', 'plan_id', 'start_date']
            );

            if ($clientId <= 0 || !Client::find($clientId)) {
                $errors['client_id'] = "Veuillez sélectionner un client valide.";
            } elseif (Subscription::hasActiveSubscription($clientId)) {
                $errors['client_id'] = "Ce client possède déjà un abonnement actif en cours.";
            }

            $plan = Plan::find($planId);
            if (!$plan || !$plan['is_active']) {
                $errors['plan_id'] = "Veuillez sélectionner un plan valide et actif.";
            }

            if (empty($startDate) || !strtotime($startDate)) {
                $errors['start_date'] = "Veuillez entrer une date de début valide.";
            }

            $this->checkValidation($errors);

            $endDate = self::calculateEndDate($startDate, (int) $plan['duration_days']);
            $status  = self::determineSubscriptionStatus($endDate);

            $subscriptionId = Subscription::create([
                'client_id'  => $clientId,
                'plan_id'    => $planId,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'status'     => $status,
            ]);

            self::logInfo("Nouvel abonnement créé ID: {$subscriptionId} pour le client ID: {$clientId}");
            $this->redirect('/subscriptions');

        } catch (ValidationException | SecurityException $e) {
            $this->render('subscriptions/create', [
                'clients'          => Client::all(),
                'plans'            => Plan::getActivePlans(),
                'selectedClientId' => (int) ($_POST['client_id'] ?? 0),
                'todayDate'        => date('Y-m-d'),
                'error'            => $e->getMessage(),
                'errors'           => ($e instanceof ValidationException) ? $e->getErrors() : [],
                'old'              => $_POST,
                'csrfToken'        => $this->generateCsrfToken(),
            ]);
        }
    }

    public function cancel(int $id): void
    {
        $this->requireAuth();

        try {
            $this->checkCsrfOrThrow($_POST['csrf_token'] ?? null);

            $subscription = Subscription::find($id);
            if ($subscription) {
                Subscription::cancel($id);
                self::logInfo("Abonnement annulé ID: {$id}");
            }

            $this->redirect('/subscriptions');

        } catch (SecurityException $e) {
            $this->redirect('/subscriptions');
        }
    }

    public function delete(int $id): void
    {
        $this->requireAuth();

        try {
            $this->checkCsrfOrThrow($_POST['csrf_token'] ?? null);

            Subscription::delete($id);
            self::logInfo("Abonnement supprimé définitivement ID: {$id}");

            $this->redirect('/subscriptions');

        } catch (SecurityException $e) {
            $this->redirect('/subscriptions');
        }
    }


    public function receipt(int $id): void{
    $this->requireAuth();

    $subscription = Subscription::getDetailsById($id);
    if (!$subscription) {
        throw new NotFoundException("Abonnement introuvable pour générer le reçu.");
    }

    $this->render('subscriptions/receipt', [
        'subscription' => $subscription,
    ]);
}
}