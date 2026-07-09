<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
<h1>Dashboard</h1>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- ── Stat Cards ────────────────────────────────────────────────────── -->
<div class="row g-4 mb-5">
    <!-- Total de Campanhas -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-primary animate-fade-in-up animate-delay-1">
            <div class="stat-card-icon">
                <i class="bi bi-megaphone"></i>
            </div>
            <div class="stat-card-value"><?= number_format($totalCampaigns) ?></div>
            <div class="stat-card-label">Total de Campanhas</div>
        </div>
    </div>

    <!-- Campanhas Ativas -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-success animate-fade-in-up animate-delay-2">
            <div class="stat-card-icon">
                <i class="bi bi-lightning"></i>
            </div>
            <div class="stat-card-value"><?= number_format($activeCampaigns) ?></div>
            <div class="stat-card-label">Campanhas Ativas</div>
        </div>
    </div>

    <!-- Total de Acessos -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-info animate-fade-in-up animate-delay-3">
            <div class="stat-card-icon">
                <i class="bi bi-eye"></i>
            </div>
            <div class="stat-card-value"><?= number_format($totalAccesses) ?></div>
            <div class="stat-card-label">Total de Acessos</div>
        </div>
    </div>

    <!-- Taxa de Viralização -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-warning animate-fade-in-up animate-delay-4">
            <div class="stat-card-icon">
                <i class="bi bi-share"></i>
            </div>
            <div class="stat-card-value"><?= $viralizationRate ?>%</div>
            <div class="stat-card-label">Taxa de Viralização</div>
        </div>
    </div>
</div>

<!-- ── Recent Campaigns ─────────────────────────────────────────────── -->
<div class="card animate-fade-in-up">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-clock-history me-2"></i>Campanhas Recentes</span>
        <a href="/admin/campaigns" class="btn btn-sm btn-outline-secondary">
            Ver todas <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($recentCampaigns)): ?>
            <div class="text-center py-5 text-muted-custom">
                <i class="bi bi-inbox" style="font-size:2.5rem;"></i>
                <p class="mt-3 mb-0">Nenhuma campanha criada ainda.</p>
                <a href="/admin/campaigns/create" class="btn btn-primary btn-sm mt-3">
                    <i class="bi bi-plus-circle me-1"></i> Criar Campanha
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table-dark-custom">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Status</th>
                            <th>Acessos</th>
                            <th>Criada em</th>
                            <th class="text-end">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentCampaigns as $c): ?>
                        <tr>
                            <td>
                                <a href="/admin/campaigns/<?= esc($c->id ?? $c['id'] ?? '') ?>/edit" style="color:var(--text);font-weight:500;">
                                    <?= esc($c->name ?? $c['name'] ?? '') ?>
                                </a>
                            </td>
                            <td>
                                <?php
                                    $status = $c->status ?? $c['status'] ?? 'draft';
                                    $labels = [
                                        'draft'  => 'Rascunho',
                                        'active' => 'Ativa',
                                        'paused' => 'Pausada',
                                        'ended'  => 'Encerrada',
                                    ];
                                ?>
                                <span class="badge-status badge-<?= esc($status) ?>">
                                    <?= $labels[$status] ?? ucfirst($status) ?>
                                </span>
                            </td>
                            <td><?= number_format($c->access_count ?? $c['access_count'] ?? 0) ?></td>
                            <td>
                                <?php
                                    $date = $c->created_at ?? $c['created_at'] ?? '';
                                    echo $date ? date('d/m/Y H:i', strtotime((string) $date)) : '—';
                                ?>
                            </td>
                            <td class="text-end">
                                <a href="/admin/campaigns/<?= esc($c->id ?? $c['id'] ?? '') ?>/edit"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
