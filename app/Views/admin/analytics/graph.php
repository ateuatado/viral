<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Grafo — <?= esc($campaign['name'] ?? '') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .graph-container {
        position: relative;
        width: 100%;
        min-height: 600px;
        background: var(--card-bg, #1e293b);
        border-radius: .75rem;
        overflow: hidden;
        border: 1px solid var(--border-color, #334155);
    }
    .graph-container svg {
        width: 100%;
        height: 100%;
        display: block;
    }
    .graph-legend {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(15, 23, 42, .85);
        border: 1px solid var(--border-color, #334155);
        border-radius: .5rem;
        padding: .75rem 1rem;
        font-size: .8rem;
        z-index: 10;
    }
    .graph-legend-item {
        display: flex;
        align-items: center;
        gap: .5rem;
        margin-bottom: .35rem;
        color: var(--text-muted, #94a3b8);
    }
    .graph-legend-item:last-child { margin-bottom: 0; }
    .graph-legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        flex-shrink: 0;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- ── Header ─────────────────────────────────────────────────────── -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="mb-1"><i class="bi bi-diagram-2 me-2"></i>Grafo de Propagação</h1>
        <p class="text-muted-custom mb-0"><?= esc($campaign['name'] ?? '') ?></p>
    </div>
    <a href="/admin/campaigns/<?= esc($campaign['id'] ?? '') ?>/analytics"
       class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Voltar ao Overview
    </a>
</div>

<!-- ── Graph Container ────────────────────────────────────────────── -->
<div class="graph-container" id="graphContainer">
    <!-- Legend -->
    <div class="graph-legend">
        <div class="graph-legend-item">
            <span class="graph-legend-dot" style="background:#f59e0b;"></span> Semente
        </div>
        <div class="graph-legend-item">
            <span class="graph-legend-dot" style="background:#22c55e;"></span> Viralizado
        </div>
        <div class="graph-legend-item">
            <span class="graph-legend-dot" style="background:#6366f1;"></span> Não viralizado
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="/assets/vendor/js/d3.v7.min.js"></script>
<script src="/assets/js/graph.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        initGraph('graphContainer', '/admin/api/campaigns/<?= esc($campaign['id'] ?? '', 'js') ?>/propagators');
    });
</script>
<?= $this->endSection() ?>
