<?php
/**
 * Top Navbar partial — included by layouts/admin.php
 */
?>

<header class="top-navbar">
    <!-- Left -->
    <div class="navbar-left">
        <button type="button" class="btn-sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="bi bi-list"></i>
        </button>

        <div class="page-title-area">
            <?= $this->renderSection('page_title') ?>
        </div>
    </div>

    <!-- Right -->
    <div class="navbar-right">
        <!-- Notifications -->
        <button type="button" class="navbar-icon-btn" title="Notificações">
            <i class="bi bi-bell"></i>
            <span class="badge-dot"></span>
        </button>

        <!-- User Dropdown -->
        <div class="dropdown">
            <div class="navbar-user" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="avatar-sm">
                    <?= strtoupper(substr(session()->get('user_name') ?? 'A', 0, 1)) ?>
                </div>
                <i class="bi bi-chevron-down" style="font-size:.7rem;color:var(--text-dark);"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <span class="dropdown-item-text" style="color:var(--text);font-weight:600;font-size:.85rem;">
                        <?= esc(session()->get('user_name') ?? 'Admin') ?>
                    </span>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="/admin/profile">
                        <i class="bi bi-person me-2"></i> Perfil
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="/admin/settings">
                        <i class="bi bi-gear me-2"></i> Configurações
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="/admin/logout">
                        <i class="bi bi-box-arrow-right me-2"></i> Sair
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>
