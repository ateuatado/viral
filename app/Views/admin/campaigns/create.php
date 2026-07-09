<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Nova Campanha<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Nova Campanha</h2>
    <a href="/admin/campaigns" class="btn btn-outline-light btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Voltar
    </a>
</div>

<form action="/admin/campaigns" method="post" enctype="multipart/form-data">
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
                                   value="<?= old('name') ?>" required minlength="3" maxlength="255"
                                   placeholder="Ex: Promoção de Verão 2026">
                        </div>
                        <div class="col-md-4">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control" id="slug" name="slug"
                                   value="<?= old('slug') ?>" maxlength="100"
                                   placeholder="gerado-automaticamente">
                            <div class="form-text">Deixe vazio para gerar automaticamente.</div>
                        </div>
                        <div class="col-12">
                            <label for="objective" class="form-label">Objetivo</label>
                            <textarea class="form-control" id="objective" name="objective"
                                      rows="2" placeholder="Qual o objetivo desta campanha?"><?= old('objective') ?></textarea>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control" id="description" name="description"
                                      rows="3" placeholder="Descrição detalhada..."><?= old('description') ?></textarea>
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
                                   value="<?= old('contact_name') ?>"
                                   placeholder="Nome exibido no chat">
                        </div>
                        <div class="col-md-6">
                            <label for="contact_avatar" class="form-label">Avatar do Contato</label>
                            <input type="file" class="form-control" id="contact_avatar" name="contact_avatar"
                                   accept="image/*">
                        </div>
                        <div class="col-12">
                            <hr class="border-secondary">
                            <h6 class="text-secondary mb-3">Open Graph (prévia no WhatsApp)</h6>
                        </div>
                        <div class="col-md-6">
                            <label for="og_title" class="form-label">OG Título</label>
                            <input type="text" class="form-control" id="og_title" name="og_title"
                                   value="<?= old('og_title') ?>"
                                   placeholder="Título da prévia">
                        </div>
                        <div class="col-md-6">
                            <label for="og_image" class="form-label">OG Imagem</label>
                            <input type="file" class="form-control" id="og_image" name="og_image"
                                   accept="image/*">
                        </div>
                        <div class="col-12">
                            <label for="og_description" class="form-label">OG Descrição</label>
                            <textarea class="form-control" id="og_description" name="og_description"
                                      rows="2" placeholder="Descrição da prévia"><?= old('og_description') ?></textarea>
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
                            <select class="form-select" id="offer_type" name="offer_type">
                                <option value="text" <?= old('offer_type') === 'text' ? 'selected' : '' ?>>Texto</option>
                                <option value="image" <?= old('offer_type') === 'image' ? 'selected' : '' ?>>Imagem</option>
                                <option value="video" <?= old('offer_type') === 'video' ? 'selected' : '' ?>>Vídeo</option>
                                <option value="link" <?= old('offer_type') === 'link' ? 'selected' : '' ?>>Link</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label for="offer_title" class="form-label">Título da Oferta</label>
                            <input type="text" class="form-control" id="offer_title" name="offer_title"
                                   value="<?= old('offer_title') ?>"
                                   placeholder="Título exibido ao usuário">
                        </div>
                        <div class="col-12">
                            <label for="offer_body" class="form-label">Corpo da Oferta</label>
                            <textarea class="form-control" id="offer_body" name="offer_body"
                                      rows="4" placeholder="Conteúdo detalhado da oferta..."><?= old('offer_body') ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="offer_link_url" class="form-label">URL do Link</label>
                            <input type="url" class="form-control" id="offer_link_url" name="offer_link_url"
                                   value="<?= old('offer_link_url') ?>"
                                   placeholder="https://...">
                        </div>
                        <div class="col-md-6">
                            <label for="offer_link_text" class="form-label">Texto do Link</label>
                            <input type="text" class="form-control" id="offer_link_text" name="offer_link_text"
                                   value="<?= old('offer_link_text') ?>"
                                   placeholder="Saiba mais">
                        </div>
                        <div class="col-md-6">
                            <label for="offer_cta_text" class="form-label">Texto do CTA</label>
                            <input type="text" class="form-control" id="offer_cta_text" name="offer_cta_text"
                                   value="<?= old('offer_cta_text', 'Compartilhe e ganhe!') ?>"
                                   placeholder="Compartilhe e ganhe!">
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
                                       <?= old('config_geoloc') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="config_geoloc">
                                    Ativar geolocalização
                                </label>
                            </div>
                            <div class="form-text">Coleta a localização dos participantes.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="config_geoloc_mode" class="form-label">Modo de Geolocalização</label>
                            <select class="form-select" id="config_geoloc_mode" name="config_geoloc_mode">
                                <option value="explicit" <?= old('config_geoloc_mode', 'explicit') === 'explicit' ? 'selected' : '' ?>>
                                    Explícito (pede permissão)
                                </option>
                                <option value="silent" <?= old('config_geoloc_mode') === 'silent' ? 'selected' : '' ?>>
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
            <i class="bi bi-check-lg me-1"></i> Criar Campanha
        </button>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Auto-generate slug from name
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    let slugManuallyEdited = false;

    slugInput.addEventListener('input', () => { slugManuallyEdited = slugInput.value.length > 0; });

    nameInput.addEventListener('input', () => {
        if (slugManuallyEdited) return;
        let slug = nameInput.value.toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/[\s-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        slugInput.value = slug;
    });
</script>
<?= $this->endSection() ?>
