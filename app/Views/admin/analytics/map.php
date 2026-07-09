<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Mapa — <?= esc($campaign['name'] ?? '') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="/assets/vendor/css/leaflet.min.css">
<style>
    .map-wrapper {
        position: relative;
        width: 100%;
        min-height: 500px;
        border-radius: .75rem;
        overflow: hidden;
        border: 1px solid var(--border-color, #334155);
    }
    #mapContainer {
        width: 100%;
        height: 500px;
    }
    .map-no-data {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: .75rem;
        height: 300px;
        color: var(--text-muted, #94a3b8);
        font-size: 1rem;
    }
    .map-no-data i { font-size: 2.5rem; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- ── Header ─────────────────────────────────────────────────────── -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="mb-1"><i class="bi bi-map me-2"></i>Mapa de Geolocalização</h1>
        <p class="text-muted-custom mb-0"><?= esc($campaign['name'] ?? '') ?></p>
    </div>
    <a href="/admin/campaigns/<?= esc($campaign['id'] ?? '') ?>/analytics"
       class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Voltar ao Overview
    </a>
</div>

<!-- ── Map Container ──────────────────────────────────────────────── -->
<div class="map-wrapper">
    <div id="mapContainer"></div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="/assets/vendor/js/leaflet.min.js"></script>
<script src="/assets/js/map.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        initMap('mapContainer', '/admin/api/campaigns/<?= esc($campaign['id'] ?? '', 'js') ?>/propagators');
    });
</script>
<?= $this->endSection() ?>
