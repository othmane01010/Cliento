<header class="topbar">
    <div class="topbar-title">
        <h1><?= htmlspecialchars($pageTitle ?? 'Tableau de bord') ?></h1>
    </div>
    <div class="topbar-user">
        <span class="user-greeting">Bonjour, <strong><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></strong></span>
        <a href="/logout" class="btn btn-outline-danger btn-sm">Déconnexion</a>
    </div>
</header>