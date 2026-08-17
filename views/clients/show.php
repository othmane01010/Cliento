<?php
$pageTitle = 'Profil Client : ' . htmlspecialchars($client['full_name']);
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="client-profile-grid">
    <div class="card client-info-card">
        <div class="card-body text-center">
            <div class="profile-avatar-wrapper mb-3">
                <img 
                    src="/uploads/clients/<?= htmlspecialchars($client['photo'] ?? 'default.png') ?>" 
                    alt="<?= htmlspecialchars($client['full_name']) ?>" 
                    class="avatar-lg"
                >
            </div>
            <h2 class="client-name"><?= htmlspecialchars($client['full_name']) ?></h2>
            <p class="text-muted">Client depuis le <?= htmlspecialchars(date('d/m/Y', strtotime($client['created_at']))) ?></p>

            <div class="client-contact-details mt-4 text-left">
                <div class="info-item">
                    <span class="info-label">🪪 CIN :</span>
                    <span class="info-value font-bold"><?= !empty($client['cin']) ? htmlspecialchars($client['cin']) : '<span class="text-muted">Non renseigné</span>' ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">📞 Téléphone :</span>
                    <span class="info-value font-bold"><?= htmlspecialchars($client['phone']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">✉️ Email :</span>
                    <span class="info-value"><?= !empty($client['email']) ? htmlspecialchars($client['email']) : '<span class="text-muted">Non renseigné</span>' ?></span>
                </div>
            </div>

            <div class="profile-actions mt-4">
                <a href="/subscriptions/create?client_id=<?= (int) $client['id'] ?>" class="btn btn-success btn-block mb-2">
                    + Nouvel Abonnement
                </a>
                <a href="/clients/edit/<?= (int) $client['id'] ?>" class="btn btn-outline-secondary btn-block">
                    Modifier les informations
                </a>
            </div>
        </div>
    </div>

    <div class="card client-history-card">
        <div class="card-header flex-between">
            <h3 class="card-title">Historique des Abonnements</h3>
            <span class="badge badge-secondary"><?= count($subscriptions) ?> au total</span>
        </div>

        <div class="table-responsive">
            <?php if (empty($subscriptions)): ?>
                <div class="empty-state">
                    <p>Ce client n'a aucun historique d'abonnement.</p>
                    <a href="/subscriptions/create?client_id=<?= (int) $client['id'] ?>" class="btn btn-primary btn-sm mt-2">
                        Créer le premier abonnement
                    </a>
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Plan</th>
                            <th>Prix</th>
                            <th>Période</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subscriptions as $sub): ?>
                            <tr>
                                <td>#<?= (int) $sub['id'] ?></td>
                                <td><strong><?= htmlspecialchars($sub['plan_name']) ?></strong></td>
                                <td><?= number_format($sub['plan_price'], 2) ?> DH</td>
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
                                    <a href="/subscriptions/show/<?= (int) $sub['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        Voir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>