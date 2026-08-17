<?php
$pageTitle = 'Détails de l\'abonnement #' . (int) $subscription['id'];
require_once __DIR__ . '/../layouts/header.php';

// تجهيز رابط الواتساب
$cleanPhone = preg_replace('/[^0-9]/', '', $subscription['client_phone'] ?? '');
if (str_starts_with($cleanPhone, '0')) {
    $cleanPhone = '212' . substr($cleanPhone, 1);
}
$msg = "Bonjour {$subscription['client_name']},\n\nNous vous contactons concernant votre abonnement ({$subscription['plan_name']}) qui est actuellement {$subscription['status']} (Date de fin : {$subscription['end_date']}).\n\nCordialement, Cliento.";
$waUrl = !empty($cleanPhone) ? "https://wa.me/{$cleanPhone}?text=" . rawurlencode($msg) : '#';
?>

<div class="subscription-detail-container">
    <div class="card">
        <div class="card-header flex-between">
            <h3 class="card-title">Abonnement #<?= (int) $subscription['id'] ?></h3>
            <div class="header-actions" style="display: flex; gap: 8px; align-items: center;">
                <?php if (!empty($cleanPhone)): ?>
                    <a href="<?= $waUrl ?>" target="_blank" class="btn btn-sm btn-success" style="background-color: #25D366; border-color: #25D366; color: #fff; text-decoration: none;">
                        💬 Rappel WhatsApp
                    </a>
                <?php endif; ?>
                <a href="/subscriptions/receipt/<?= (int) $subscription['id'] ?>" target="_blank" class="btn btn-outline-secondary btn-sm">
                    🖨️ Imprimer le reçu
                </a>
                <a href="/subscriptions" class="btn btn-outline-secondary btn-sm">Retour à la liste</a>
            </div>
        </div>

        <div class="card-body">
          
            <div class="subscription-status-banner mb-4">
                <div class="status-indicator">
                    <?php
                    $badgeClass = match($subscription['status']) {
                        'ACTIVE'        => 'badge-success',
                        'EXPIRING_SOON' => 'badge-warning',
                        'EXPIRED'       => 'badge-danger',
                        'CANCELLED'     => 'badge-secondary',
                        default         => 'badge-secondary',
                    };
                    ?>
                    <span class="badge <?= $badgeClass ?> badge-lg"><?= htmlspecialchars($subscription['status']) ?></span>
                </div>
                <div class="days-remaining">
                    <?php if ($subscription['status'] === 'ACTIVE' || $subscription['status'] === 'EXPIRING_SOON'): ?>
                        <strong><?= (int) $remainingDays ?></strong> jour(s) restant(s)
                    <?php elseif ($subscription['status'] === 'EXPIRED'): ?>
                        <span class="text-danger font-bold">Abonnement expiré</span>
                    <?php else: ?>
                        <span class="text-muted">Abonnement annulé</span>
                    <?php endif; ?>
                </div>
            </div>

         
            <div class="details-grid">
              
                <div class="detail-box">
                    <h4 class="detail-box-title">👤 Informations Client</h4>
                    <div class="user-avatar-group mb-3">
                        <img 
                            src="/uploads/clients/<?= htmlspecialchars($subscription['client_photo'] ?? 'default.png') ?>" 
                            alt="Photo" 
                            class="avatar-md"
                        >
                        <div>
                            <strong><?= htmlspecialchars($subscription['client_name']) ?></strong>
                            <div class="text-muted"><small>Inscrit dans le système</small></div>
                        </div>
                    </div>
                    <ul class="detail-list">
                        <li><strong>CIN :</strong> <?= !empty($subscription['client_cin']) ? htmlspecialchars($subscription['client_cin']) : '<span class="text-muted">—</span>' ?></li>
                        <li><strong>Téléphone :</strong> <?= htmlspecialchars($subscription['client_phone']) ?></li>
                        <li><strong>Email :</strong> <?= !empty($subscription['client_email']) ? htmlspecialchars($subscription['client_email']) : '<span class="text-muted">—</span>' ?></li>
                    </ul>
                    <a href="/clients/show/<?= (int) $subscription['client_id'] ?>" class="btn btn-outline-primary btn-sm mt-3">
                        Voir profil complet
                    </a>
                </div>

           
                <div class="detail-box">
                    <h4 class="detail-box-title">📋 Détails du Contrat</h4>
                    <ul class="detail-list">
                        <li><strong>Plan souscrit :</strong> <span class="badge badge-secondary"><?= htmlspecialchars($subscription['plan_name']) ?></span></li>
                        <li><strong>Durée du plan :</strong> <?= (int) ($subscription['plan_duration'] ?? 0) ?> jours</li>
                        <li><strong>Montant payé :</strong> <span class="text-primary font-bold"><?= number_format($subscription['plan_price'], 2) ?> DH</span></li>
                        <li><strong>Date de début :</strong> <?= htmlspecialchars($subscription['start_date']) ?></li>
                        <li><strong>Date d'expiration :</strong> <strong class="text-danger"><?= htmlspecialchars($subscription['end_date']) ?></strong></li>
                        <li><strong>Enregistré le :</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($subscription['created_at']))) ?></li>
                    </ul>
                </div>
            </div>

            <div class="detail-actions mt-4">
                <a href="/subscriptions/create?client_id=<?= (int) $subscription['client_id'] ?>" class="btn btn-success">
                    + Renouveler / Nouvel abonnement
                </a>

                <?php if ($subscription['status'] === 'ACTIVE' || $subscription['status'] === 'EXPIRING_SOON'): ?>
                    <form action="/subscriptions/cancel/<?= (int) $subscription['id'] ?>" method="POST" class="inline-form" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cet abonnement immédiatement ?');">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                        <button type="submit" class="btn btn-outline-danger">
                            Annuler l'abonnement
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>