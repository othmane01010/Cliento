<?php
$pageTitle = 'Modifier le plan : ' . htmlspecialchars($plan['name']);
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="form-container">
    <div class="card">
        <div class="card-header flex-between">
            <h3 class="card-title">Modifier le Plan</h3>
            <a href="/plans" class="btn btn-outline-secondary btn-sm">Retour à la liste</a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="/plans/update/<?= (int) $plan['id'] ?>" method="POST" class="form-body">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

            <div class="form-group">
                <label for="name" class="form-label">Nom du plan <span class="text-danger">*</span></label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                    value="<?= htmlspecialchars($plan['name'] ?? '') ?>" 
                    required
                >
                <?php if (isset($errors['name'])): ?>
                    <span class="invalid-feedback"><?= htmlspecialchars($errors['name']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="duration_days" class="form-label">Durée (en jours) <span class="text-danger">*</span></label>
                <input 
                    type="number" 
                    id="duration_days" 
                    name="duration_days" 
                    min="1" 
                    step="1" 
                    class="form-control <?= isset($errors['duration_days']) ? 'is-invalid' : '' ?>" 
                    value="<?= htmlspecialchars($plan['duration_days'] ?? '') ?>" 
                    required
                >
                <small class="form-hint">Exemples : 30 pour 1 mois, 90 pour 3 mois, 365 pour 1 an.</small>
                <?php if (isset($errors['duration_days'])): ?>
                    <span class="invalid-feedback"><?= htmlspecialchars($errors['duration_days']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="price" class="form-label">Prix (DH) <span class="text-danger">*</span></label>
                <input 
                    type="number" 
                    id="price" 
                    name="price" 
                    min="0" 
                    step="0.01" 
                    class="form-control <?= isset($errors['price']) ? 'is-invalid' : '' ?>" 
                    value="<?= htmlspecialchars($plan['price'] ?? '') ?>" 
                    required
                >
                <?php if (isset($errors['price'])): ?>
                    <span class="invalid-feedback"><?= htmlspecialchars($errors['price']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Mettre à jour le plan
                </button>
                <a href="/plans" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>