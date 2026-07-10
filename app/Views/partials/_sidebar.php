<?php
/**
 * Sidebar partial — included by layouts/admin.php
 */
$currentUrl = current_url();

$isActive = static function (string $path) use ($currentUrl): string {
    return str_contains($currentUrl, $path) ? 'active' : '';
};
$isExact = static function (string $path) use ($currentUrl): string {
    return rtrim($currentUrl, '/') === rtrim(site_url($path), '/') ? 'active' : '';
};
?>

<aside class="sidebar" id="sidebar">
    <!-- Brand -->
    <a href="/admin" class="sidebar-brand">
        <span class="brand-emoji">🔗</span> Viral
    </a>

    <!-- Navigation -->
    <ul class="sidebar-nav">
        <li class="sidebar-heading">Menu</li>

        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link <?= $isExact('admin') ?>" href="/admin">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Campanhas -->
        <li class="nav-item">
            <a class="nav-link <?= $isActive('/admin/campaigns') ?>" href="/admin/campaigns">
                <i class="bi bi-megaphone"></i>
                <span>Campanhas</span>
            </a>
            <ul class="nav-sub">
                <li class="nav-item">
                    <a class="nav-link <?= $isActive('/admin/campaigns/create') ?>" href="/admin/campaigns/create">
                        <i class="bi bi-plus-circle"></i>
                        <span>Criar Campanha</span>
                    </a>
                </li>
            </ul>
        </li>

        <?php if (isset($campaign)): ?>
        <!-- Analytics (visible only when viewing a campaign) -->
        <li class="nav-item">
            <a class="nav-link <?= $isActive('/analytics') ?>" href="/admin/campaigns/<?= esc($campaign->id ?? '') ?>/analytics">
                <i class="bi bi-graph-up"></i>
                <span>Analytics</span>
            </a>
        </li>
        <?php endif; ?>
    </ul>

    <div class="sidebar-divider"></div>

    <!-- Footer / User -->
    <div class="sidebar-footer">
        <div class="user-avatar">
            <?= strtoupper(substr(session()->get('user_name') ?? 'A', 0, 1)) ?>
        </div>
        <div class="user-info">
            <div class="user-name"><?= esc(session()->get('user_name') ?? 'Admin') ?></div>
            <div class="user-role">Administrador</div>
        </div>
        <a href="/logout" class="btn-logout" title="Sair">
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>
</aside>
