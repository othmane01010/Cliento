<?php
$pageTitle = 'Tableau de bord';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon icon-primary">👥</div>
        <div class="stat-info">
            <span class="stat-label">Total Clients</span>
            <strong class="stat-value"><?= number_format($totalClients) ?></strong>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon icon-success">💳</div>
        <div class="stat-info">
            <span class="stat-label">Abonnements Actifs</span>
            <strong class="stat-value"><?= number_format($activeSubscriptions) ?></strong>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon icon-warning">⏳</div>
        <div class="stat-info">
            <span class="stat-label">Expire Bientôt (≤ 3j)</span>
            <strong class="stat-value"><?= number_format($expiringCount) ?></strong>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon icon-info">💰</div>
        <div class="stat-info">
            <span class="stat-label">Revenu Estimé (Actif)</span>
            <strong class="stat-value"><?= number_format($totalRevenue, 2) ?> <small>DH</small></strong>
        </div>
    </div>
</div>


<?php if (!empty($expiringSubscriptions)): ?>
    <div class="card alert-card-wrapper mb-4">
        <div class="card-header border-warning">
            <h3 class="card-title text-warning">⚠️ Abonnements expirant dans les 3 prochains jours</h3>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Téléphone</th>
                        <th>Plan</th>
                        <th>Date de fin</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($expiringSubscriptions as $sub): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($sub['client_name']) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($sub['client_phone']) ?></td>
                            <td><span class="badge badge-secondary"><?= htmlspecialchars($sub['plan_name']) ?></span></td>
                            <td><span class="text-danger font-bold"><?= htmlspecialchars($sub['end_date']) ?></span></td>
                            <td>
                                <div class="action-buttons" style="display: flex; gap: 6px; align-items: center;">
                                    <?php
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $sub['client_phone']);
                                    if (str_starts_with($cleanPhone, '0')) {
                                        $cleanPhone = '212' . substr($cleanPhone, 1);
                                    }

                                    $message = "Bonjour {$sub['client_name']},\n\nNous vous rappelons que votre abonnement ({$sub['plan_name']}) arrive à expiration le {$sub['end_date']}.\n\nPensez à le renouveler dès votre prochain passage !\n\nCordialement, Cliento.";
                                    $waUrl = "https://wa.me/{$cleanPhone}?text=" . rawurlencode($message);
                                    ?>

                                    <a href="<?= $waUrl ?>" target="_blank" class="btn btn-sm btn-success" title="Envoyer rappel WhatsApp" style="background-color: #25D366; border-color: #25D366; color: #fff; text-decoration: none;">
                                        💬 WhatsApp
                                    </a>

                                    <a href="/subscriptions/show/<?= (int) $sub['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        Détails
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>


<div class="card">
    <div class="card-header flex-between">
        <h3 class="card-title">Derniers abonnements enregistrés</h3>
        <a href="/subscriptions" class="btn btn-sm btn-outline-secondary">Voir tout</a>
    </div>
    <div class="table-responsive">
        <?php if (empty($recentSubscriptions)): ?>
            <div class="empty-state">
                <p>Aucun abonnement enregistré pour le moment.</p>
                <a href="/subscriptions/create" class="btn btn-primary btn-sm mt-2">Créer un abonnement</a>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Plan</th>
                        <th>Période</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentSubscriptions as $sub): ?>
                        <tr>
                            <td>#<?= (int) $sub['id'] ?></td>
                            <td>
                                <div class="user-avatar-group">
                                    <img src="/uploads/clients/<?= htmlspecialchars($sub['client_photo'] ?? 'default.png') ?>" alt="Photo" class="avatar-sm">
                                    <span><?= htmlspecialchars($sub['client_name']) ?></span>
                                </div>
                            </td>
                            <td><strong><?= htmlspecialchars($sub['plan_name']) ?></strong> (<?= number_format($sub['plan_price'], 2) ?> DH)</td>
                            <td>
                                <small class="text-muted"><?= htmlspecialchars($sub['start_date']) ?> → <?= htmlspecialchars($sub['end_date']) ?></small>
                            </td>
                            <td>
                                <?php
                                $badgeClass = match($sub['status']) {
                                    'ACTIVE'        => 'badge-success',
                                    'EXPIRING_SOON' => 'badge-warning',
                                    'EXPIRED'       => 'badge-danger',
                                    'CANCELLED'     => 'badge-secondary',
                                    default         => 'badge-secondary',
                                };
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($sub['status']) ?></span>
                            </td>
                            <td>
                                <div class="action-buttons" style="display: flex; gap: 6px; align-items: center;">
                                    <?php
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $sub['client_phone'] ?? '');
                                    if (str_starts_with($cleanPhone, '0')) {
                                        $cleanPhone = '212' . substr($cleanPhone, 1);
                                    }

                                    $message = "Bonjour {$sub['client_name']},\n\nVotre abonnement ({$sub['plan_name']}) est actuellement {$sub['status']} (Expire le: {$sub['end_date']}).\n\nCordialement, Cliento.";
                                    $waUrl = !empty($cleanPhone) ? "https://wa.me/{$cleanPhone}?text=" . rawurlencode($message) : '#';
                                    ?>

                                    <?php if (!empty($cleanPhone)): ?>
                                        <a href="<?= $waUrl ?>" target="_blank" class="btn btn-sm btn-success" title="Envoyer message WhatsApp" style="background-color: #25D366; border-color: #25D366; color: #fff; text-decoration: none;">
                                            💬 WhatsApp
                                        </a>
                                    <?php endif; ?>

                                    <a href="/subscriptions/show/<?= (int) $sub['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        Voir
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>