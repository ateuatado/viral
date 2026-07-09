<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Analytics — <?= esc($campaign['name'] ?? '') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- ── Header ─────────────────────────────────────────────────────── -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="mb-1"><i class="bi bi-bar-chart-line me-2"></i>Analytics</h1>
        <p class="text-muted-custom mb-0"><?= esc($campaign['name'] ?? '') ?></p>
    </div>
    <a href="/admin/campaigns/<?= esc($campaign['id'] ?? '') ?>/edit" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Voltar à Campanha
    </a>
</div>

<!-- ── Navigation tabs ────────────────────────────────────────────── -->
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="/admin/campaigns/<?= esc($campaign['id'] ?? '') ?>/analytics" class="btn btn-primary">
        <i class="bi bi-bar-chart-line me-1"></i> Visão Geral
    </a>
    <a href="/admin/campaigns/<?= esc($campaign['id'] ?? '') ?>/analytics/graph" class="btn btn-outline-secondary">
        <i class="bi bi-diagram-2 me-1"></i> Grafo Visual
    </a>
    <a href="/admin/campaigns/<?= esc($campaign['id'] ?? '') ?>/analytics/map" class="btn btn-outline-secondary">
        <i class="bi bi-map me-1"></i> Mapa de Geolocation
    </a>
    <a href="/admin/campaigns/<?= esc($campaign['id'] ?? '') ?>/analytics/leads" class="btn btn-outline-secondary">
        <i class="bi bi-people me-1"></i> Lista de Leads
    </a>
    <a href="/admin/campaigns/<?= esc($campaign['id'] ?? '') ?>/analytics/export" class="btn btn-outline-secondary ms-auto">
        <i class="bi bi-download me-1"></i> Exportar CSV
    </a>
</div>

<!-- ── Stat Cards — Row 1 ─────────────────────────────────────────── -->
<div class="row g-4 mb-4">
    <!-- Total de Acessos -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card stat-info animate-fade-in-up animate-delay-1">
            <div class="stat-card-icon">
                <i class="bi bi-eye"></i>
            </div>
            <div class="stat-card-value"><?= number_format($pageViews) ?></div>
            <div class="stat-card-label">Total de Acessos</div>
        </div>
    </div>

    <!-- Propagadores -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card stat-primary animate-fade-in-up animate-delay-2">
            <div class="stat-card-icon">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-card-value"><?= number_format($totalPropagators) ?></div>
            <div class="stat-card-label">Propagadores</div>
        </div>
    </div>

    <!-- Viralizados -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card stat-success animate-fade-in-up animate-delay-3">
            <div class="stat-card-icon">
                <i class="bi bi-share"></i>
            </div>
            <div class="stat-card-value"><?= number_format($totalViralized) ?></div>
            <div class="stat-card-label">Viralizados</div>
        </div>
    </div>
</div>

<!-- ── Stat Cards — Row 2 ─────────────────────────────────────────── -->
<div class="row g-4 mb-5">
    <!-- Taxa de Viralização -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card stat-warning animate-fade-in-up animate-delay-1">
            <div class="stat-card-icon">
                <i class="bi bi-percent"></i>
            </div>
            <div class="stat-card-value"><?= $viralizationRate ?>%</div>
            <div class="stat-card-label">Taxa de Viralização</div>
        </div>
    </div>

    <!-- Profundidade Máx -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card stat-info animate-fade-in-up animate-delay-2">
            <div class="stat-card-icon">
                <i class="bi bi-diagram-3"></i>
            </div>
            <div class="stat-card-value"><?= number_format($maxDepth) ?></div>
            <div class="stat-card-label">Profundidade Máx</div>
        </div>
    </div>

    <!-- Geoloc Permitida -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card stat-primary animate-fade-in-up animate-delay-3">
            <div class="stat-card-icon">
                <i class="bi bi-geo-alt"></i>
            </div>
            <div class="stat-card-value"><?= number_format($geolocGranted) ?></div>
            <div class="stat-card-label">Geoloc Permitida</div>
        </div>
    </div>
</div>



<?= $this->endSection() ?>
