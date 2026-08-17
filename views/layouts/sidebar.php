<aside class="sidebar">
    <div class="sidebar-brand">
        <h2>Cliento<span>.</span></h2>
    </div>

    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="/dashboard" class="<?= (strpos($_SERVER['REQUEST_URI'], '/dashboard') === 0) ? 'active' : '' ?>">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Tableau de bord</span>
                </a>
            </li>
            <li>
                <a href="/clients" class="<?= (strpos($_SERVER['REQUEST_URI'], '/clients') === 0) ? 'active' : '' ?>">
                    <span class="nav-icon">👥</span>
                    <span class="nav-text">Clients</span>
                </a>
            </li>
            <li>
                <a href="/plans" class="<?= (strpos($_SERVER['REQUEST_URI'], '/plans') === 0) ? 'active' : '' ?>">
                    <span class="nav-icon">📋</span>
                    <span class="nav-text">Plans d'abonnement</span>
                </a>
            </li>
            <li>
                <a href="/subscriptions" class="<?= (strpos($_SERVER['REQUEST_URI'], '/subscriptions') === 0) ? 'active' : '' ?>">
                    <span class="nav-icon">💳</span>
                    <span class="nav-text">Abonnements</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>