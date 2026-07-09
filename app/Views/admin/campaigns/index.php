<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Campanhas<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-megaphone me-2"></i>Campanhas</h2>
    <a href="/admin/campaigns/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nova Campanha
    </a>
</div>

<?php if (empty($campaigns)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox display-4 text-secondary mb-3 d-block"></i>
            <p class="text-secondary mb-3">Nenhuma campanha cadastrada ainda.</p>
            <a href="/admin/campaigns/create" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Criar primeira campanha
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th class="text-center">Acessos</th>
                        <th>Criado em</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($campaigns as $campaign): ?>
                        <?php
                            $statusBadge = match ($campaign['status']) {
                                'active' => 'success',
                                'paused' => 'warning',
                                'ended'  => 'danger',
                                default  => 'secondary',
                            };
                            $statusLabel = match ($campaign['status']) {
                                'active' => 'Ativa',
                                'paused' => 'Pausada',
                                'ended'  => 'Encerrada',
                                default  => 'Rascunho',
                            };
                            $toggleLabel = match ($campaign['status']) {
                                'draft'  => 'Ativar',
                                'active' => 'Pausar',
                                'paused' => 'Retomar',
                                'ended'  => 'Reabrir',
                                default  => 'Ativar',
                            };
                            $toggleIcon = match ($campaign['status']) {
                                'active' => 'bi-pause-circle',
                                default  => 'bi-play-circle',
                            };
                        ?>
                        <tr>
                            <td>
                                <strong><?= esc($campaign['name']) ?></strong>
                            </td>
                            <td>
                                <code class="text-info"><?= esc($campaign['slug']) ?></code>
                            </td>
                            <td>
                                <span class="badge bg-<?= $statusBadge ?>"><?= $statusLabel ?></span>
                            </td>
                            <td class="text-center">
                                <span class="text-secondary">—</span>
                            </td>
                            <td>
                                <small class="text-secondary"><?= format_datetime_br($campaign['created_at'] ?? null) ?></small>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="/admin/campaigns/<?= esc($campaign['id']) ?>/edit"
                                       class="btn btn-outline-light" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="/admin/campaigns/<?= esc($campaign['id']) ?>/messages"
                                       class="btn btn-outline-light" title="Mensagens">
                                        <i class="bi bi-chat-dots"></i>
                                    </a>
                                    <a href="/admin/campaigns/<?= esc($campaign['id']) ?>/analytics"
                                       class="btn btn-outline-light" title="Analytics">
                                        <i class="bi bi-graph-up"></i>
                                    </a>
                                    <form action="/admin/campaigns/<?= esc($campaign['id']) ?>/toggle-status"
                                          method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-light" title="<?= $toggleLabel ?>">
                                            <i class="bi <?= $toggleIcon ?>"></i>
                                        </button>
                                    </form>
                                    <form action="/admin/campaigns/<?= esc($campaign['id']) ?>/delete"
                                          method="post" class="d-inline"
                                          onsubmit="return confirm('Tem certeza que deseja excluir esta campanha?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-danger" title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
