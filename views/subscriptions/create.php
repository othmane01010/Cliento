<?php
$pageTitle = 'Créer un nouvel abonnement';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="form-container">
    <div class="card">
        <div class="card-header flex-between">
            <h3 class="card-title">Nouveau Contrat d'Abonnement</h3>
            <a href="/subscriptions" class="btn btn-outline-secondary btn-sm">Retour à la liste</a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="/subscriptions/store" method="POST" class="form-body">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

         
            <div class="form-group">
                <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                <select 
                    id="client_id" 
                    name="client_id" 
                    class="form-control <?= isset($errors['client_id']) ? 'is-invalid' : '' ?>" 
                    required
                >
                    <option value="">-- Sélectionner un client --</option>
                    <?php foreach ($clients as $client): ?>
                        <?php 
                            $isSelected = (isset($selectedClientId) && $selectedClientId == $client['id']) 
                                       || (isset($old['client_id']) && $old['client_id'] == $client['id']);
                        ?>
                        <option value="<?= (int) $client['id'] ?>" <?= $isSelected ? 'selected' : '' ?>>
                            <?= htmlspecialchars($client['full_name']) ?> (<?= htmlspecialchars($client['phone']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['client_id'])): ?>
                    <span class="invalid-feedback"><?= htmlspecialchars($errors['client_id']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="plan_id" class="form-label">Plan d'abonnement <span class="text-danger">*</span></label>
                <select 
                    id="plan_id" 
                    name="plan_id" 
                    class="form-control <?= isset($errors['plan_id']) ? 'is-invalid' : '' ?>" 
                    required
                >
                    <option value="">-- Sélectionner un plan --</option>
                    <?php foreach ($plans as $plan): ?>
                        <?php $isSelected = (isset($old['plan_id']) && $old['plan_id'] == $plan['id']); ?>
                        <option value="<?= (int) $plan['id'] ?>" <?= $isSelected ? 'selected' : '' ?>>
                            <?= htmlspecialchars($plan['name']) ?> — <?= (int) $plan['duration_days'] ?> jours (<?= number_format($plan['price'], 2) ?> DH)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['plan_id'])): ?>
                    <span class="invalid-feedback"><?= htmlspecialchars($errors['plan_id']) ?></span>
                <?php endif; ?>
            </div>

           
            <div class="form-group">
                <label for="start_date" class="form-label">Date de début <span class="text-danger">*</span></label>
                <input 
                    type="date" 
                    id="start_date" 
                    name="start_date" 
                    class="form-control <?= isset($errors['start_date']) ? 'is-invalid' : '' ?>" 
                    value="<?= htmlspecialchars($old['start_date'] ?? $todayDate ?? date('Y-m-d')) ?>" 
                    required
                >
                <small class="form-hint">La date de fin sera calculée automatiquement selon la durée du plan choisi.</small>
                <?php if (isset($errors['start_date'])): ?>
                    <span class="invalid-feedback"><?= htmlspecialchars($errors['start_date']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Créer l'abonnement
                </button>
                <a href="/subscriptions" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>