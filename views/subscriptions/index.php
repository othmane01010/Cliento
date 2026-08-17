<?php
$pageTitle = 'Gestion des Abonnements';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="card">
    <div class="card-header flex-between flex-wrap gap-2">
        <div class="filter-tabs">
            <a href="/subscriptions" class="btn-tab <?= empty($currentFilter) || $currentFilter === 'ALL' ? 'active' : '' ?>">
                Tous
            </a>
            <a href="/subscriptions?status=ACTIVE" class="btn-tab <?= ($currentFilter ?? '') === 'ACTIVE' ? 'active' : '' ?>">
                Actifs
            </a>
            <a href="/subscriptions?status=EXPIRING_SOON" class="btn-tab <?= ($currentFilter ?? '') === 'EXPIRING_SOON' ? 'active' : '' ?>">
                Expire Bientôt
            </a>
            <a href="/subscriptions?status=EXPIRED" class="btn-tab <?= ($currentFilter ?? '') === 'EXPIRED' ? 'active' : '' ?>">
                Expirés
            </a>
            <a href="/subscriptions?status=CANCELLED" class="btn-tab <?= ($currentFilter ?? '') === 'CANCELLED' ? 'active' : '' ?>">
                Annulés
            </a>
        </div>

        <a href="/subscriptions/create" class="btn btn-primary">
            <span>+</span> Nouvel Abonnement
        </a>
    </div>

    <div class="table-responsive">
        <?php if (empty($subscriptions)): ?>
            <div class="empty-state">
                <p>Aucun abonnement trouvé pour cette sélection.</p>
                <a href="/subscriptions/create" class="btn btn-primary btn-sm mt-2">Créer un abonnement</a>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Plan</th>
                        <th>Prix</th>
                        <th>Date Début</th>
                        <th>Date Fin</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subscriptions as $sub): ?>
                        <tr>
                            <td>#<?= (int) $sub['id'] ?></td>
                            <td>
                                <div class="user-avatar-group">
                                    <img 
                                        src="/uploads/clients/<?= htmlspecialchars($sub['client_photo'] ?? 'default.png') ?>" 
                                        alt="Photo" 
                                        class="avatar-sm"
                                    >
                                    <div>
                                        <strong><?= htmlspecialchars($sub['client_name']) ?></strong>
                                        <div class="text-muted"><small><?= htmlspecialchars($sub['client_phone']) ?></small></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($sub['plan_name']) ?></td>
                            <td><strong><?= number_format($sub['plan_price'], 2) ?> <small>DH</small></strong></td>
                            <td><small><?= htmlspecialchars($sub['start_date']) ?></small></td>
                            <td>
                                <strong class="<?= $sub['status'] === 'EXPIRING_SOON' ? 'text-warning font-bold' : ($sub['status'] === 'EXPIRED' ? 'text-danger' : '') ?>">
                                    <?= htmlspecialchars($sub['end_date']) ?>
                                </strong>
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
                                <div class="action-buttons">
                                    <a href="/subscriptions/show/<?= (int) $sub['id'] ?>" class="btn btn-sm btn-outline-primary" title="Détails">
                                        Détails
                                    </a>

                                    <?php if ($sub['status'] === 'ACTIVE' || $sub['status'] === 'EXPIRING_SOON'): ?>
                                        <form action="/subscriptions/cancel/<?= (int) $sub['id'] ?>" method="POST" class="inline-form" onsubmit="return confirm('Voulez-vous vraiment annuler cet abonnement ?');">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Annuler">
                                                Annuler
                                            </button>
                                        </form>
                                    <?php endif; ?>
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