<?php
$pageTitle = 'Gestion des Clients';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="card">
    <div class="card-header flex-between">
        <div class="search-form-wrapper">
            <form action="/clients" method="GET" class="search-form">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control" 
                    placeholder="Rechercher par nom, CIN, téléphone..." 
                    value="<?= htmlspecialchars($searchTerm ?? '') ?>"
                >
                <button type="submit" class="btn btn-secondary">Rechercher</button>
                <?php if (!empty($searchTerm)): ?>
                    <a href="/clients" class="btn btn-outline-secondary">Réinitialiser</a>
                <?php endif; ?>
            </form>
        </div>
        <a href="/clients/create" class="btn btn-primary">
            <span>+</span> Nouveau Client
        </a>
    </div>

    <div class="table-responsive">
        <?php if (empty($clients)): ?>
            <div class="empty-state">
                <p>Aucun client trouvé.</p>
                <?php if (!empty($searchTerm)): ?>
                    <small class="text-muted">Essayez un autre mot-clé de recherche.</small>
                <?php else: ?>
                    <a href="/clients/create" class="btn btn-primary btn-sm mt-2">Ajouter votre premier client</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Téléphone</th>
                        <th>Email</th>
                        <th>Date d'ajout</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $client): ?>
                        <tr>
                            <td>
                                <div class="user-avatar-group">
                                    <img 
                                        src="/uploads/clients/<?= htmlspecialchars($client['photo'] ?? 'default.png') ?>" 
                                        alt="Photo" 
                                        class="avatar-sm"
                                    >
                                    <div>
                                        <span class="font-medium d-block"><?= htmlspecialchars($client['full_name']) ?></span>
                                        <?php if (!empty($client['cin'])): ?>
                                            <small class="text-muted font-bold" style="font-size: 11px;">CIN: <?= htmlspecialchars($client['cin']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code><?= htmlspecialchars($client['phone']) ?></code>
                            </td>
                            <td>
                                <?= !empty($client['email']) ? htmlspecialchars($client['email']) : '<span class="text-muted">—</span>' ?>
                            </td>
                            <td>
                                <small class="text-muted"><?= htmlspecialchars(date('d/m/Y', strtotime($client['created_at']))) ?></small>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="/clients/show/<?= (int) $client['id'] ?>" class="btn btn-sm btn-outline-primary" title="Détails & Abonnements">
                                        Profil
                                    </a>
                                    <a href="/subscriptions/create?client_id=<?= (int) $client['id'] ?>" class="btn btn-sm btn-success" title="Nouvel abonnement">
                                        + Abonnement
                                    </a>
                                    <a href="/clients/edit/<?= (int) $client['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Modifier">
                                        Modifier
                                    </a>
                                    <form action="/clients/delete/<?= (int) $client['id'] ?>" method="POST" class="inline-form" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?');">
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