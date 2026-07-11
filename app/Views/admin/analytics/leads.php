<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Lista de Leads — <?= esc($campaign['name'] ?? '') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* ── Treeview Styling ───────────────────────────────────────────────── */
    .tree-container {
        background: var(--card-bg, #1e293b);
        border: 1px solid var(--border-color, #334155);
        border-radius: .75rem;
        padding: 2rem;
        min-height: 500px;
        overflow-x: auto;
    }

    ul.tree-root, ul.tree-branch {
        list-style: none;
        padding-left: 0;
        margin: 0;
    }

    /* Linhas de conexão vertical */
    ul.tree-branch {
        padding-left: 2.25rem;
        position: relative;
        margin-top: 0.5rem;
    }
    ul.tree-branch::before {
        content: '';
        position: absolute;
        left: 1.125rem;
        top: 0;
        bottom: 1.5rem;
        width: 2px;
        background: var(--border-color, #334155);
    }

    li.tree-item {
        position: relative;
        margin-bottom: 1rem;
    }
    li.tree-item:last-child {
        margin-bottom: 0;
    }

    /* Linha horizontal para o nó filho */
    li.tree-item::before {
        content: '';
        position: absolute;
        left: -1.125rem;
        top: 1.5rem;
        width: 1.125rem;
        height: 2px;
        background: var(--border-color, #334155);
    }
    ul.tree-root > li.tree-item::before {
        display: none; /* Semente raiz não tem linha horizontal para a esquerda */
    }

    /* Card do Nó */
    .tree-node-card {
        background: rgba(30, 41, 59, 0.4);
        border: 1px solid var(--border-color, #334155);
        border-radius: 0.6rem;
        padding: 0.85rem 1.25rem;
        transition: all 0.2s ease-in-out;
        position: relative;
        z-index: 2;
    }
    .tree-node-card:hover {
        background: rgba(30, 41, 59, 0.7);
        border-color: rgba(255, 255, 255, 0.15);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    /* Card Especial para Semente */
    .tree-node-card.node-seed {
        border-left: 4px solid var(--bs-warning, #f59e0b);
    }
    .tree-node-card:not(.node-seed) {
        border-left: 4px solid var(--bs-success, #22c55e);
    }

    .node-icon-wrapper {
        width: 38px;
        height: 38px;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .node-name {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--text-color, #f8fafc);
    }

    .node-details {
        font-size: 0.78rem;
    }

    .bg-dark-custom {
        background: rgba(15, 23, 42, 0.6);
    }

    .text-muted-custom {
        color: #94a3b8 !important;
    }

    /* Efeitos de colapso */
    .tree-branch.collapsed {
        display: none;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- ── Header ─────────────────────────────────────────────────────── -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="mb-1"><i class="bi bi-people me-2"></i>Árvore de Leads</h1>
        <p class="text-muted-custom mb-0"><?= esc($campaign['name'] ?? '') ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="/admin/campaigns/<?= esc($campaign['id'] ?? '') ?>/analytics" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Voltar ao Overview
        </a>
    </div>
</div>

<!-- ── Navigation tabs ────────────────────────────────────────────── -->
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="/admin/campaigns/<?= esc($campaign['id'] ?? '') ?>/analytics" class="btn btn-outline-secondary">
        <i class="bi bi-bar-chart-line me-1"></i> Visão Geral
    </a>
    <a href="/admin/campaigns/<?= esc($campaign['id'] ?? '') ?>/analytics/graph" class="btn btn-outline-secondary">
        <i class="bi bi-diagram-2 me-1"></i> Grafo Visual
    </a>
    <a href="/admin/campaigns/<?= esc($campaign['id'] ?? '') ?>/analytics/map" class="btn btn-outline-secondary">
        <i class="bi bi-map me-1"></i> Mapa de Geolocation
    </a>
    <a href="/admin/campaigns/<?= esc($campaign['id'] ?? '') ?>/analytics/leads" class="btn btn-primary">
        <i class="bi bi-people me-1"></i> Lista de Leads
    </a>
</div>

<!-- ── Tree Container ─────────────────────────────────────────────── -->
<div class="tree-container">
    <?php if (empty($tree)): ?>
        <div class="text-center py-5 text-muted-custom">
            <i class="bi bi-people fs-1 d-block mb-3"></i>
            <p class="mb-0">Nenhum lead ou semente encontrado para esta campanha.</p>
        </div>
    <?php else: ?>
        <ul class="tree-root">
            <?php
            // Helper recursivo para desenhar os ramos
            function renderBranch($branch) {
                $hasChildren = !empty($branch['children']);
                $icon = $branch['is_seed'] ? 'bi-flower1 text-warning' : 'bi-person-fill text-success';
                $name = $branch['is_seed'] ? 'Nó Semente (Raiz)' : esc($branch['name']);
                
                $discountBadge = '';
                if (!$branch['is_seed'] && $branch['discount'] > 0) {
                    $discountBadge = '<span class="badge bg-success-subtle text-success border border-success-subtle ms-2 px-2 py-1" style="font-size: 11px;">' . $branch['discount'] . '% OFF</span>';
                }
                
                $visualizersBadge = '';
                if ($branch['visualizers'] > 0) {
                    $visualizersBadge = '<span class="badge bg-info-subtle text-info border border-info-subtle ms-2 px-2 py-1" style="font-size: 11px;"><i class="bi bi-eye me-1"></i>' . $branch['visualizers'] . ' visualizador(es)</span>';
                }
                
                $html = '
                <li class="tree-item">
                    <div class="tree-node-card' . ($branch['is_seed'] ? ' node-seed' : '') . '">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="node-icon-wrapper">
                                    <i class="bi ' . $icon . ' fs-5"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center flex-wrap">
                                        <h5 class="node-name mb-0">' . $name . '</h5>
                                        ' . $discountBadge . '
                                        ' . $visualizersBadge . '
                                    </div>
                                    <div class="node-details text-muted-custom mt-1">';
                                    
                                    if (!$branch['is_seed']) {
                                        $html .= '<span class="me-3"><i class="bi bi-whatsapp me-1 text-success"></i>' . esc($branch['phone']) . '</span>';
                                        if (!empty($branch['email'])) {
                                            $html .= '<span class="me-3"><i class="bi bi-envelope me-1 text-primary"></i>' . esc($branch['email']) . '</span>';
                                        }
                                        if (!empty($branch['instagram'])) {
                                            $html .= '<span class="me-3"><i class="bi bi-instagram me-1 text-danger"></i>' . esc($branch['instagram']) . '</span>';
                                        }
                                        if (!empty($branch['cpf'])) {
                                            $html .= '<span class="me-3"><i class="bi bi-card-text me-1 text-info"></i>' . esc($branch['cpf']) . '</span>';
                                        }
                                    }
                                    
                                    $html .= '<span><i class="bi bi-key me-1 text-secondary"></i>Token: <code>' . esc($branch['token']) . '</code></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-dark-custom text-muted border border-secondary-subtle" style="font-size: 11px;">Profundidade: ' . $branch['depth'] . '</span>';
                                
                                if ($hasChildren) {
                                    $html .= '<button class="btn btn-sm btn-outline-secondary py-0 px-2 btn-toggle-branch" style="font-size: 11px; height: 22px;">
                                        <i class="bi bi-dash-lg me-1"></i> Recolher
                                    </button>';
                                }
                                
                            $html .= '
                            </div>
                        </div>
                    </div>';
                    
                if ($hasChildren) {
                    $html .= '<ul class="tree-branch">';
                    foreach ($branch['children'] as $child) {
                        $html .= renderBranch($child);
                    }
                    $html .= '</ul>';
                }
                
                $html .= '</li>';
                return $html;
            }

            foreach ($tree as $branch) {
                echo renderBranch($branch);
            }
            ?>
        </ul>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Lógica de colapsar/expandir ramos da árvore
        const toggleButtons = document.querySelectorAll('.btn-toggle-branch');
        
        toggleButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const item = e.target.closest('.tree-item');
                const subBranch = item.querySelector('.tree-branch');
                
                if (subBranch) {
                    subBranch.classList.toggle('collapsed');
                    
                    if (subBranch.classList.contains('collapsed')) {
                        btn.innerHTML = '<i class="bi bi-plus-lg me-1"></i> Expandir';
                        btn.classList.remove('btn-outline-secondary');
                        btn.classList.add('btn-outline-primary');
                    } else {
                        btn.innerHTML = '<i class="bi bi-dash-lg me-1"></i> Recolher';
                        btn.classList.remove('btn-outline-primary');
                        btn.classList.add('btn-outline-secondary');
                    }
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
