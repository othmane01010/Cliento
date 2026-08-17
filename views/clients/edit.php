<?php
$pageTitle = 'Modifier le client';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="form-container">
    <div class="card">
        <div class="card-header flex-between">
            <h3 class="card-title">Modifier : <?= htmlspecialchars($client['full_name']) ?></h3>
            <a href="/clients" class="btn btn-outline-secondary btn-sm">Retour à la liste</a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="/clients/update/<?= (int) $client['id'] ?>" method="POST" enctype="multipart/form-data" class="form-body">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

            <div class="form-group">
                <label for="full_name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                <input 
                    type="text" 
                    id="full_name" 
                    name="full_name" 
                    class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" 
                    value="<?= htmlspecialchars($client['full_name'] ?? '') ?>" 
                    required
                >
                <?php if (isset($errors['full_name'])): ?>
                    <span class="invalid-feedback"><?= htmlspecialchars($errors['full_name']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="cin" class="form-label">N° CIN <small class="text-muted">(Optionnel)</small></label>
                <input 
                    type="text" 
                    id="cin" 
                    name="cin" 
                    class="form-control <?= isset($errors['cin']) ? 'is-invalid' : '' ?>" 
                    value="<?= htmlspecialchars($client['cin'] ?? '') ?>" 
                    placeholder="ex: EE123456"
                    style="text-transform: uppercase;"
                >
                <?php if (isset($errors['cin'])): ?>
                    <span class="invalid-feedback"><?= htmlspecialchars($errors['cin']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="phone" class="form-label">Numéro de téléphone <span class="text-danger">*</span></label>
                <input 
                    type="text" 
                    id="phone" 
                    name="phone" 
                    class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
                    value="<?= htmlspecialchars($client['phone'] ?? '') ?>" 
                    required
                >
                <small class="form-hint">Format marocain valide (ex: 06XXXXXXXX, 07XXXXXXXX, +212...)</small>
                <?php if (isset($errors['phone'])): ?>
                    <span class="invalid-feedback"><?= htmlspecialchars($errors['phone']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Adresse Email <small class="text-muted">(Optionnel)</small></label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                    value="<?= htmlspecialchars($client['email'] ?? '') ?>"
                >
                <?php if (isset($errors['email'])): ?>
                    <span class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label">Photo actuelle</label>
                <div class="current-photo-preview mb-2">
                    <img 
                        src="/uploads/clients/<?= htmlspecialchars($client['photo'] ?? 'default.png') ?>" 
                        alt="Photo actuelle" 
                        class="avatar-md"
                    >
                </div>
                <label for="photo" class="form-label">Changer la photo <small class="text-muted">(Laisser vide pour conserver l'actuelle)</small></label>
                <input 
                    type="file" 
                    id="photo" 
                    name="photo" 
                    class="form-control-file <?= isset($errors['photo']) ? 'is-invalid' : '' ?>" 
                    accept="image/png, image/jpeg, image/jpg, image/webp"
                >
                <?php if (isset($errors['photo'])): ?>
                    <span class="invalid-feedback"><?= htmlspecialchars($errors['photo']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Mettre à jour
                </button>
                <a href="/clients" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>