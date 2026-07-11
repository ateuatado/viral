<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Editar: <?= esc($campaign['name']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

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
?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="bi bi-pencil-square me-2"></i><?= esc($campaign['name']) ?>
            <span class="badge bg-<?= $statusBadge ?> ms-2 fs-6"><?= $statusLabel ?></span>
        </h2>
        <small class="text-secondary">ID: <?= esc($campaign['id']) ?></small>
    </div>
    <div class="d-flex gap-2">
        <a href="/admin/campaigns" class="btn btn-outline-light btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Voltar
        </a>
        <form action="/admin/campaigns/<?= esc($campaign['id']) ?>/toggle-status" method="post" class="d-inline">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-<?= $campaign['status'] === 'active' ? 'warning' : 'success' ?> btn-sm">
                <i class="bi <?= $campaign['status'] === 'active' ? 'bi-pause-circle' : 'bi-play-circle' ?> me-1"></i>
                <?= $toggleLabel ?>
            </button>
        </form>
        <form action="/admin/campaigns/<?= esc($campaign['id']) ?>/delete" method="post" class="d-inline"
              onsubmit="return confirm('Tem certeza? Esta ação é irreversível.')">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash me-1"></i> Excluir
            </button>
        </form>
    </div>
</div>

<!-- Info cards -->
<div class="row g-3 mb-4">
    <?php if ($campaign['status'] === 'active' && $seedToken): ?>
        <div class="col-lg-8">
            <div class="card border-success">
                <div class="card-body">
                    <h6 class="card-title text-success mb-2"><i class="bi bi-link-45deg me-1"></i> Link Semente</h6>
                    <?php $seedUrl = base_url('v/' . esc($campaign['slug']) . '/' . esc($seedToken)); ?>
                    <div class="input-group">
                        <input type="text" class="form-control" id="seedLink" value="<?= $seedUrl ?>" readonly>
                        <button class="btn btn-outline-success" type="button" id="copySeedLink" title="Copiar">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="display-6 text-primary"><?= number_format($totalPropagators) ?></div>
                <small class="text-secondary">Propagadores</small>
            </div>
        </div>
    </div>
</div>

<!-- Edit Form -->
<form action="/admin/campaigns/<?= esc($campaign['id']) ?>/update" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="dados-tab" data-bs-toggle="tab"
                    data-bs-target="#dados" type="button" role="tab">
                <i class="bi bi-card-text me-1"></i> Dados
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="whatsapp-tab" data-bs-toggle="tab"
                    data-bs-target="#whatsapp" type="button" role="tab">
                <i class="bi bi-whatsapp me-1"></i> WhatsApp
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="oferta-tab" data-bs-toggle="tab"
                    data-bs-target="#oferta" type="button" role="tab">
                <i class="bi bi-gift me-1"></i> Oferta
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="config-tab" data-bs-toggle="tab"
                    data-bs-target="#config" type="button" role="tab">
                <i class="bi bi-gear me-1"></i> Configurações
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">

        <!-- Tab 1: Dados -->
        <div class="tab-pane fade show active" id="dados" role="tabpanel">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="name" class="form-label">Nome da Campanha <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                   value="<?= old('name', esc($campaign['name'])) ?>"
                                   required minlength="3" maxlength="255">
                        </div>
                        <div class="col-md-4">
                            <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="slug" name="slug"
                                   value="<?= old('slug', esc($campaign['slug'])) ?>"
                                   required maxlength="100">
                        </div>
                        <div class="col-12">
                            <label for="objective" class="form-label">Objetivo</label>
                            <textarea class="form-control" id="objective" name="objective"
                                      rows="2"><?= old('objective', esc($campaign['objective'] ?? '')) ?></textarea>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control" id="description" name="description"
                                      rows="3"><?= old('description', esc($campaign['description'] ?? '')) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: WhatsApp -->
        <div class="tab-pane fade" id="whatsapp" role="tabpanel">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="contact_name" class="form-label">Nome do Contato</label>
                            <input type="text" class="form-control" id="contact_name" name="contact_name"
                                   value="<?= old('contact_name', esc($campaign['contact_name'] ?? '')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="contact_avatar" class="form-label">Avatar do Contato</label>
                            <input type="file" class="form-control" id="contact_avatar" name="contact_avatar"
                                   accept="image/*">
                            <?php if (!empty($campaign['contact_avatar'])): ?>
                                <div class="form-text">
                                    Atual: <img src="<?= esc($campaign['contact_avatar']) ?>" alt="avatar"
                                               class="rounded-circle" width="24" height="24">
                                    <span class="ms-1"><?= basename($campaign['contact_avatar']) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-12">
                            <hr class="border-secondary">
                            <h6 class="text-secondary mb-3">Open Graph (prévia no WhatsApp)</h6>
                        </div>
                        <div class="col-md-6">
                            <label for="og_title" class="form-label">OG Título</label>
                            <input type="text" class="form-control" id="og_title" name="og_title"
                                   value="<?= old('og_title', esc($campaign['og_title'] ?? '')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="og_image" class="form-label">OG Imagem</label>
                            <input type="file" class="form-control" id="og_image" name="og_image"
                                   accept="image/*">
                            <?php if (!empty($campaign['og_image'])): ?>
                                <div class="form-text">
                                    Atual: <img src="<?= esc($campaign['og_image']) ?>" alt="og"
                                               class="rounded" width="60" height="32" style="object-fit:cover">
                                    <span class="ms-1"><?= basename($campaign['og_image']) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-12">
                            <label for="og_description" class="form-label">OG Descrição</label>
                            <textarea class="form-control" id="og_description" name="og_description"
                                      rows="2"><?= old('og_description', esc($campaign['og_description'] ?? '')) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 3: Oferta -->
        <div class="tab-pane fade" id="oferta" role="tabpanel">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="offer_type" class="form-label">Tipo de Oferta</label>
                            <?php $offerType = old('offer_type', $campaign['offer_type'] ?? 'text'); ?>
                            <select class="form-select" id="offer_type" name="offer_type">
                                <option value="text" <?= $offerType === 'text' ? 'selected' : '' ?>>Texto</option>
                                <option value="image" <?= $offerType === 'image' ? 'selected' : '' ?>>Imagem</option>
                                <option value="video" <?= $offerType === 'video' ? 'selected' : '' ?>>Vídeo</option>
                                <option value="link" <?= $offerType === 'link' ? 'selected' : '' ?>>Link</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label for="offer_title" class="form-label">Título da Oferta</label>
                            <input type="text" class="form-control" id="offer_title" name="offer_title"
                                   value="<?= old('offer_title', esc($campaign['offer_title'] ?? '')) ?>">
                        </div>
                        <div class="col-12">
                            <label for="offer_body" class="form-label">Corpo da Oferta</label>
                            <textarea class="form-control" id="offer_body" name="offer_body"
                                      rows="4"><?= old('offer_body', esc($campaign['offer_body'] ?? '')) ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="offer_link_url" class="form-label">URL do Link</label>
                            <input type="url" class="form-control" id="offer_link_url" name="offer_link_url"
                                   value="<?= old('offer_link_url', esc($campaign['offer_link_url'] ?? '')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="offer_link_text" class="form-label">Texto do Link</label>
                            <input type="text" class="form-control" id="offer_link_text" name="offer_link_text"
                                   value="<?= old('offer_link_text', esc($campaign['offer_link_text'] ?? '')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="offer_cta_text" class="form-label">Texto do CTA</label>
                            <input type="text" class="form-control" id="offer_cta_text" name="offer_cta_text"
                                   value="<?= old('offer_cta_text', esc($campaign['offer_cta_text'] ?? 'Compartilhe e ganhe!')) ?>">
                        </div>
                        <div class="col-12" id="offerImageField" style="display:none;">
                            <label for="offer_image" class="form-label">Imagem da Oferta</label>
                            <input type="file" class="form-control" id="offer_image" name="offer_image"
                                   accept="image/*">
                            <?php if (!empty($campaign['offer_image'])): ?>
                                <div class="form-text">
                                    Atual: <img src="<?= esc($campaign['offer_image']) ?>" alt="oferta"
                                               class="rounded" width="80" height="50" style="object-fit:cover">
                                    <span class="ms-1"><?= basename($campaign['offer_image']) ?></span>
                                    <label class="ms-3 text-warning">
                                        <input type="checkbox" name="remove_offer_image" value="1">
                                        Remover imagem
                                    </label>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">Formatos: JPG, PNG, WebP. Tamanho recomendado: 800×600 px, máx 300 KB.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 4: Configurações -->
        <div class="tab-pane fade" id="config" role="tabpanel">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="config_geoloc"
                                       name="config_geoloc" value="1"
                                       <?= old('config_geoloc', $campaign['config_geoloc'] ?? false) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="config_geoloc">
                                    Ativar geolocalização
                                </label>
                            </div>
                            <div class="form-text">Coleta a localização dos participantes.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="config_geoloc_mode" class="form-label">Modo de Geolocalização</label>
                            <?php $geoMode = old('config_geoloc_mode', $campaign['config_geoloc_mode'] ?? 'explicit'); ?>
                            <select class="form-select" id="config_geoloc_mode" name="config_geoloc_mode">
                                <option value="explicit" <?= $geoMode === 'explicit' ? 'selected' : '' ?>>
                                    Explícito (pede permissão)
                                </option>
                                <option value="silent" <?= $geoMode === 'silent' ? 'selected' : '' ?>>
                                    Silencioso (IP)
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Submit -->
    <div class="d-flex justify-content-end gap-2 mt-3">
        <a href="/admin/campaigns" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-check-lg me-1"></i> Salvar Alterações
        </button>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Copy seed link
    const copyBtn = document.getElementById('copySeedLink');
    if (copyBtn) {
        copyBtn.addEventListener('click', () => {
            const input = document.getElementById('seedLink');
            navigator.clipboard.writeText(input.value).then(() => {
                copyBtn.innerHTML = '<i class="bi bi-check2"></i>';
                setTimeout(() => { copyBtn.innerHTML = '<i class="bi bi-clipboard"></i>'; }, 2000);
            });
        });
    }

    // Show/hide offer image field based on type
    const offerType = document.getElementById('offer_type');
    const offerImageField = document.getElementById('offerImageField');
    function toggleOfferImage() {
        offerImageField.style.display = offerType.value === 'image' ? 'block' : 'none';
    }
    offerType.addEventListener('change', toggleOfferImage);
    toggleOfferImage();
</script>
<?= $this->endSection() ?>
