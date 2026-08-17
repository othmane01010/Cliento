<?php
$pageTitle = "Plans d'abonnement";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="card">
    <div class="card-header flex-between">
        <h3 class="card-title">Liste des Plans Disponibles</h3>
        <a href="/plans/create" class="btn btn-primary">
            <span>+</span> Nouveau Plan
        </a>
    </div>

    <div class="table-responsive">
        <?php if (empty($plans)): ?>
            <div class="empty-state">
                <p>Aucun plan d'abonnement configuré.</p>
                <a href="/plans/create" class="btn btn-primary btn-sm mt-2">Créer le premier plan</a>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nom du Plan</th>
                        <th>Durée</th>
                        <th>Prix</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($plans as $plan): ?>
                        <tr>
                            <td>#<?= (int) $plan['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($plan['name']) ?></strong>
                            </td>
                            <td>
                                <span><?= (int) $plan['duration_days'] ?> jours</span>
                            </td>
                            <td>
                                <strong class="text-primary"><?= number_format($plan['price'], 2) ?> <small>DH</small></strong>
                            </td>
                            <td>
                                <?php if (!empty($plan['is_active'])): ?>
                                    <span class="badge badge-success">Actif</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Inactif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                  
                                    <form action="/plans/toggle/<?= (int) $plan['id'] ?>" method="POST" class="inline-form">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                                        <button type="submit" class="btn btn-sm <?= !empty($plan['is_active']) ? 'btn-outline-warning' : 'btn-outline-success' ?>" title="<?= !empty($plan['is_active']) ? 'Désactiver' : 'Activer' ?>">
                                            <?= !empty($plan['is_active']) ? 'Désactiver' : 'Activer' ?>
                                        </button>
                                    </form>

                                    <a href="/plans/edit/<?= (int) $plan['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Modifier">
                                        Modifier
                                    </a>

                                    <form action="/plans/delete/<?= (int) $plan['id'] ?>" method="POST" class="inline-form" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce plan ?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                            Supprimer
                                        </button>
                                    </form>
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

