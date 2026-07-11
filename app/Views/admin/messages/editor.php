<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Editor de Mensagens: <?= esc($campaign['name']) ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* ── Editor Layout ── */
    .editor-container { display: flex; gap: 1.5rem; min-height: calc(100vh - 200px); }
    .editor-panel { flex: 1; min-width: 0; }
    .preview-panel { width: 380px; flex-shrink: 0; }

    /* ── Message Blocks ── */
    .msg-block {
        background: var(--bs-dark);
        border: 1px solid rgba(255,255,255,.08);
        border-radius: .75rem;
        padding: 1rem;
        margin-bottom: .75rem;
        transition: border-color .2s, box-shadow .2s;
    }
    .msg-block:hover { border-color: rgba(255,255,255,.15); }
    .msg-block.sortable-ghost {
        opacity: .4;
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 2px rgba(var(--bs-primary-rgb), .25);
    }
    .msg-block .drag-handle {
        cursor: grab;
        font-size: 1.25rem;
        color: rgba(255,255,255,.35);
        user-select: none;
    }
    .msg-block .drag-handle:active { cursor: grabbing; }
    .msg-block .block-header {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin-bottom: .75rem;
    }
    .msg-block .block-header .block-number {
        width: 28px; height: 28px;
        background: rgba(255,255,255,.06);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: .8rem; font-weight: 600; color: rgba(255,255,255,.5);
    }
    .msg-block textarea {
        resize: vertical;
        min-height: 60px;
        font-size: .9rem;
    }
    .msg-block .upload-zone {
        border: 2px dashed rgba(255,255,255,.15);
        border-radius: .5rem;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: border-color .2s, background .2s;
        color: rgba(255,255,255,.45);
    }
    .msg-block .upload-zone:hover {
        border-color: var(--bs-primary);
        background: rgba(var(--bs-primary-rgb), .05);
    }
    .msg-block .upload-zone.has-file {
        border-style: solid;
        border-color: rgba(255,255,255,.15);
    }
    .msg-block .media-preview {
        max-width: 100%;
        max-height: 150px;
        border-radius: .375rem;
    }
    .delay-input { width: 100px; }

    /* ── WhatsApp Preview ── */
    .wa-preview {
        background: #0b141a;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0,0,0,.35);
        display: flex;
        flex-direction: column;
        height: 620px;
        position: sticky;
        top: 1rem;
    }
    .wa-preview-header {
        background: #202c33;
        padding: .75rem 1rem;
        display: flex;
        align-items: center;
        gap: .75rem;
        border-bottom: 1px solid rgba(255,255,255,.05);
    }
    .wa-preview-header .avatar {
        width: 36px; height: 36px;
        border-radius: 50%;
        background: #00a884;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-weight: 600; font-size: .85rem;
    }
    .wa-preview-header .contact-info { flex: 1; }
    .wa-preview-header .contact-name {
        font-size: .9rem;
        font-weight: 500;
        color: #e9edef;
    }
    .wa-preview-header .contact-status {
        font-size: .7rem;
        color: #8696a0;
    }
    .wa-preview-body {
        flex: 1;
        overflow-y: auto;
        padding: .75rem;
        background-image: url("data:image/svg+xml,%3Csvg width='200' height='200' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Cpattern id='p' width='40' height='40' patternUnits='userSpaceOnUse'%3E%3Ccircle cx='20' cy='20' r='1' fill='%23ffffff08'/%3E%3C/pattern%3E%3C/defs%3E%3Crect width='200' height='200' fill='%230b141a'/%3E%3Crect width='200' height='200' fill='url(%23p)'/%3E%3C/svg%3E");
    }
    .wa-bubble {
        background: #202c33;
        border-radius: 0 .5rem .5rem .5rem;
        padding: .5rem .625rem;
        margin-bottom: .375rem;
        max-width: 85%;
        position: relative;
        color: #e9edef;
        font-size: .85rem;
        line-height: 1.35;
        word-wrap: break-word;
    }
    .wa-bubble::before {
        content: '';
        position: absolute;
        top: 0; left: -8px;
        border-width: 0 8px 8px 0;
        border-style: solid;
        border-color: transparent #202c33 transparent transparent;
    }
    .wa-bubble:not(:first-child)::before { display: none; }
    .wa-bubble .wa-time {
        font-size: .65rem;
        color: rgba(255,255,255,.45);
        text-align: right;
        margin-top: .25rem;
    }
    .wa-bubble img, .wa-bubble video {
        max-width: 100%;
        border-radius: .375rem;
        margin-bottom: .25rem;
    }
    .wa-preview-input {
        background: #202c33;
        padding: .5rem .75rem;
        display: flex;
        align-items: center;
        gap: .5rem;
        border-top: 1px solid rgba(255,255,255,.05);
    }
    .wa-preview-input .fake-input {
        flex: 1;
        background: #2a3942;
        border-radius: 1.5rem;
        padding: .5rem 1rem;
        font-size: .8rem;
        color: #8696a0;
    }
    .wa-preview-input i { color: #8696a0; font-size: 1.1rem; }

    /* ── Add button ── */
    .btn-add-msg {
        border: 2px dashed rgba(255,255,255,.12);
        border-radius: .75rem;
        padding: 1rem;
        width: 100%;
        background: transparent;
        color: rgba(255,255,255,.5);
        font-weight: 500;
        transition: all .2s;
    }
    .btn-add-msg:hover {
        border-color: var(--bs-primary);
        color: var(--bs-primary);
        background: rgba(var(--bs-primary-rgb), .05);
    }

    @media (max-width: 991.98px) {
        .editor-container { flex-direction: column; }
        .preview-panel { width: 100%; }
        .wa-preview { position: static; height: 500px; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="bi bi-chat-dots me-2"></i>Editor de Mensagens
        </h2>
        <small class="text-secondary"><?= esc($campaign['name']) ?></small>
    </div>
    <div class="d-flex gap-2">
        <a href="/admin/campaigns/<?= esc($campaign['id']) ?>/edit" class="btn btn-outline-light btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Voltar
        </a>
        <button type="button" class="btn btn-primary btn-sm" id="btnSaveMessages">
            <i class="bi bi-check-lg me-1"></i> Salvar Mensagens
        </button>
    </div>
</div>

<div class="editor-container">
    <!-- Left: Message Blocks -->
    <div class="editor-panel">
        <!-- Tabs -->
        <ul class="nav nav-pills mb-3" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="flow-tab" data-bs-toggle="tab" data-bs-target="#flow-pane" type="button" role="tab">
                    <i class="bi bi-chat-dots me-1"></i> Fluxo de Conversa
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="system-msgs-tab" data-bs-toggle="tab" data-bs-target="#system-msgs-pane" type="button" role="tab">
                    <i class="bi bi-gear-fill me-1"></i> E-mail e Sucesso
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Pane 1: Flow -->
            <div class="tab-pane fade show active" id="flow-pane" role="tabpanel">
                <div id="messageList">
                    <!-- Blocks rendered by JS -->
                </div>
                <button type="button" class="btn-add-msg" id="btnAddMessage">
                    <i class="bi bi-plus-lg me-2"></i> Adicionar Mensagem
                </button>
            </div>

            <!-- Pane 2: System Messages -->
            <div class="tab-pane fade" id="system-msgs-pane" role="tabpanel">
                <div class="card bg-dark border-secondary p-4 mb-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <h6 class="text-primary mb-3"><i class="bi bi-chat-left-text me-1"></i> Telas do Lead (Landing Page)</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="success_title" class="form-label text-white">Título da Mensagem de Sucesso (Chat)</label>
                            <input type="text" class="form-control bg-dark text-white border-secondary" id="success_title" name="success_title"
                                   placeholder="🎯 Você entrou na Corrida de Cupons!" value="<?= esc($campaign['success_title'] ?? '') ?>">
                            <div class="form-text text-muted">Título em destaque exibido no card final do chat após cadastro.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="success_message" class="form-label text-white">Mensagem exibida após compartilhamento</label>
                            <textarea class="form-control bg-dark text-white border-secondary" id="success_message" name="success_message"
                                      rows="3" placeholder="🎯 Você entrou na Corrida de Cupons!..."><?= esc($campaign['success_message'] ?? '') ?></textarea>
                            <div class="form-text text-muted">
                                Texto detalhado do card final. Use <code>{nome}</code> para personalizar.
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="owner_message" class="form-label text-white">Mensagem do Dono do Link (Dashboard do Lead)</label>
                            <textarea class="form-control bg-dark text-white border-secondary" id="owner_message" name="owner_message" rows="3"
                                      placeholder="Você conquistou acumulado {desconto} com {niveis} níveis ativos."><?= esc($campaign['owner_message'] ?? '') ?></textarea>
                            <div class="form-text text-muted">Mensagem de status/boas-vindas para o dono do link. Use <code>{nome}</code>, <code>{desconto}</code>, <code>{niveis}</code>.</div>
                        </div>

                        <div class="col-12">
                            <hr class="border-secondary">
                            <h6 class="text-primary mb-3"><i class="bi bi-envelope me-1"></i> E-mail de Boas-Vindas Transacional</h6>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="email_subject" class="form-label text-white">Assunto do E-mail</label>
                            <input type="text" class="form-control bg-dark text-white border-secondary" id="email_subject" name="email_subject"
                                   placeholder="🎯 Corrida de Cupons: Seu link de desconto está ativo!" value="<?= esc($campaign['email_subject'] ?? '') ?>">
                            <div class="form-text text-muted">Placeholders: <code>{nome}</code>, <code>{campanha}</code>.</div>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="email_body" class="form-label text-white">Corpo do E-mail (HTML/Texto)</label>
                            <textarea class="form-control bg-dark text-white border-secondary" id="email_body" name="email_body" rows="6"
                                      placeholder="Escreva a mensagem HTML..."><?= esc($campaign['email_body'] ?? '') ?></textarea>
                            <div class="form-text text-muted">
                                Placeholders: <code>{nome}</code>, <code>{campanha}</code>, <code>{senha_temporaria}</code>, <code>{link_acesso}</code>, <code>{link_compartilhamento}</code>.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: WhatsApp Preview -->
    <div class="preview-panel">
        <div class="wa-preview">
            <div class="wa-preview-header">
                <div class="avatar"><?= mb_strtoupper(mb_substr(esc($campaign['contact_name'] ?? 'C'), 0, 1)) ?></div>
                <div class="contact-info">
                    <div class="contact-name"><?= esc($campaign['contact_name'] ?? 'Contato') ?></div>
                    <div class="contact-status">online</div>
                </div>
                <i class="bi bi-three-dots-vertical" style="color:#aebac1"></i>
            </div>
            <div class="wa-preview-body" id="previewBody">
                <div class="text-center py-5" style="color:#8696a0;font-size:.8rem">
                    Adicione mensagens para visualizar
                </div>
            </div>
            <div class="wa-preview-input">
                <i class="bi bi-emoji-smile"></i>
                <div class="fake-input">Mensagem</div>
                <i class="bi bi-camera"></i>
                <i class="bi bi-mic"></i>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="/assets/vendor/js/sortable.min.js"></script>
<script>
(() => {
    'use strict';

    const CAMPAIGN_ID = '<?= esc($campaign['id']) ?>';
    const CSRF_NAME  = '<?= csrf_token() ?>';
    const CSRF_HASH  = '<?= csrf_hash() ?>';

    let messages = <?= json_encode($chatMessages ?: []) ?>;
    let blockCounter = 0;

    const listEl    = document.getElementById('messageList');
    const previewEl = document.getElementById('previewBody');

    // ── SortableJS ──
    const sortable = new Sortable(listEl, {
        handle: '.drag-handle',
        animation: 200,
        ghostClass: 'sortable-ghost',
        onEnd() { syncOrderFromDOM(); renderPreview(); },
    });

    // ── Render All ──
    function renderAll() {
        listEl.innerHTML = '';
        messages.forEach((msg, i) => {
            listEl.appendChild(createBlock(msg, i));
        });
        renderPreview();
    }

    // ── Create Block ──
    function createBlock(msg, index) {
        const id = 'block-' + (blockCounter++);
        const div = document.createElement('div');
        div.className = 'msg-block';
        div.dataset.index = index;
        div.id = id;

        const isMedia = msg.type === 'image' || msg.type === 'video';

        div.innerHTML = `
            <div class="block-header">
                <span class="drag-handle" title="Arrastar">☰</span>
                <span class="block-number">${index + 1}</span>
                <select class="form-select form-select-sm" style="width:130px" data-role="type">
                    <option value="text" ${msg.type === 'text' ? 'selected' : ''}>💬 Texto</option>
                    <option value="image" ${msg.type === 'image' ? 'selected' : ''}>🖼️ Imagem</option>
                    <option value="video" ${msg.type === 'video' ? 'selected' : ''}>🎬 Vídeo</option>
                </select>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <label class="text-secondary" style="font-size:.75rem;white-space:nowrap">
                        <i class="bi bi-clock"></i> Delay
                    </label>
                    <input type="number" class="form-control form-control-sm delay-input"
                           data-role="delay" value="${msg.delay || 1000}" min="0" step="100"
                           placeholder="ms">
                    <span class="text-secondary" style="font-size:.7rem">ms</span>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-role="delete" title="Remover">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <div class="block-content">
                ${isMedia ? buildMediaZone(msg) : buildTextArea(msg)}
            </div>
        `;

        // Events
        div.querySelector('[data-role="type"]').addEventListener('change', (e) => {
            const newType = e.target.value;
            const idx = getBlockIndex(div);
            messages[idx].type = newType;
            if (newType === 'text') {
                messages[idx].content = messages[idx].content || '';
                messages[idx].url = '';
            } else {
                messages[idx].url = messages[idx].url || '';
            }
            const contentEl = div.querySelector('.block-content');
            contentEl.innerHTML = (newType === 'image' || newType === 'video')
                ? buildMediaZone(messages[idx])
                : buildTextArea(messages[idx]);
            bindContentEvents(div, idx);
            renderPreview();
        });

        div.querySelector('[data-role="delay"]').addEventListener('input', (e) => {
            const idx = getBlockIndex(div);
            messages[idx].delay = parseInt(e.target.value) || 0;
        });

        div.querySelector('[data-role="delete"]').addEventListener('click', () => {
            const idx = getBlockIndex(div);
            messages.splice(idx, 1);
            renderAll();
        });

        bindContentEvents(div, index);
        return div;
    }

    function buildTextArea(msg) {
        return `<textarea class="form-control" data-role="content"
                    placeholder="Digite a mensagem..." rows="3">${escapeHtml(msg.content || '')}</textarea>`;
    }

    function buildMediaZone(msg) {
        if (msg.url) {
            const preview = msg.type === 'video'
                ? `<video src="${escapeHtml(msg.url)}" class="media-preview" controls></video>`
                : `<img src="${escapeHtml(msg.url)}" class="media-preview" alt="preview">`;
            return `
                <div class="upload-zone has-file" data-role="upload-zone">
                    ${preview}
                    <div class="mt-2 small text-secondary">${escapeHtml(msg.name || 'arquivo')}</div>
                    <div class="mt-2">
                        <span class="btn btn-outline-primary btn-sm">Trocar arquivo</span>
                    </div>
                    <input type="file" data-role="file-input" class="d-none"
                           accept="${msg.type === 'video' ? 'video/mp4' : 'image/jpeg,image/png,image/webp'}">
                </div>`;
        }
        return `
            <div class="upload-zone" data-role="upload-zone">
                <i class="bi bi-cloud-arrow-up fs-3"></i>
                <div class="mt-1">Clique ou arraste para enviar</div>
                <div class="small text-secondary mt-1">${msg.type === 'video' ? 'MP4 até 10MB' : 'JPG, PNG, WebP até 10MB'}</div>
                <input type="file" data-role="file-input" class="d-none"
                       accept="${msg.type === 'video' ? 'video/mp4' : 'image/jpeg,image/png,image/webp'}">
            </div>`;
    }

    function bindContentEvents(blockEl, idx) {
        const textarea = blockEl.querySelector('[data-role="content"]');
        if (textarea) {
            textarea.addEventListener('input', () => {
                const i = getBlockIndex(blockEl);
                messages[i].content = textarea.value;
                renderPreview();
            });
        }

        const uploadZone = blockEl.querySelector('[data-role="upload-zone"]');
        const fileInput  = blockEl.querySelector('[data-role="file-input"]');
        if (uploadZone && fileInput) {
            uploadZone.addEventListener('click', () => fileInput.click());
            fileInput.addEventListener('change', () => {
                if (fileInput.files.length > 0) {
                    uploadFile(fileInput.files[0], blockEl);
                }
            });
        }
    }

    // ── Upload ──
    async function uploadFile(file, blockEl) {
        const idx = getBlockIndex(blockEl);
        const fd = new FormData();
        fd.append('file', file);
        fd.append(CSRF_NAME, CSRF_HASH);

        const zone = blockEl.querySelector('[data-role="upload-zone"]');
        zone.innerHTML = `<div class="spinner-border spinner-border-sm text-primary"></div>
                          <div class="mt-2 small">Enviando...</div>`;

        try {
            const resp = await fetch(`/admin/campaigns/${CAMPAIGN_ID}/upload`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd,
            });
            const data = await resp.json();
            if (!resp.ok) throw new Error(data.error || 'Erro no upload');

            messages[idx].url = data.url;
            messages[idx].name = data.name;
            messages[idx].type = data.type;

            // Re-render this block's content
            const contentEl = blockEl.querySelector('.block-content');
            contentEl.innerHTML = buildMediaZone(messages[idx]);
            bindContentEvents(blockEl, idx);
            // Update type selector
            blockEl.querySelector('[data-role="type"]').value = data.type;
            renderPreview();
        } catch (err) {
            zone.innerHTML = `<div class="text-danger"><i class="bi bi-exclamation-triangle"></i> ${escapeHtml(err.message)}</div>
                              <div class="mt-2"><span class="btn btn-outline-primary btn-sm">Tentar novamente</span></div>
                              <input type="file" data-role="file-input" class="d-none" accept="image/*,video/mp4">`;
            bindContentEvents(blockEl, idx);
        }
    }

    // ── Add Message ──
    document.getElementById('btnAddMessage').addEventListener('click', () => {
        const msg = { type: 'text', content: '', url: '', name: '', delay: 1500 };
        messages.push(msg);
        const block = createBlock(msg, messages.length - 1);
        listEl.appendChild(block);
        renumberBlocks();
        renderPreview();
        block.querySelector('textarea')?.focus();
    });

    // ── Save ──
    document.getElementById('btnSaveMessages').addEventListener('click', async () => {
        const btn = document.getElementById('btnSaveMessages');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Salvando...';

        const payload = {
            chat_messages: messages,
            success_title: document.getElementById('success_title').value,
            success_message: document.getElementById('success_message').value,
            owner_message: document.getElementById('owner_message').value,
            email_subject: document.getElementById('email_subject').value,
            email_body: document.getElementById('email_body').value,
        };

        try {
            const resp = await fetch(`/admin/campaigns/${CAMPAIGN_ID}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    [`${CSRF_NAME}`]: CSRF_HASH,
                },
                body: JSON.stringify(payload),
            });
            const data = await resp.json();
            if (!resp.ok) throw new Error(data.error || 'Erro ao salvar');
            showToast('success', data.message || 'Mensagens salvas!');
        } catch (err) {
            showToast('danger', err.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Salvar Mensagens';
        }
    });

    // ── Sync order from DOM after drag ──
    function syncOrderFromDOM() {
        const blocks = listEl.querySelectorAll('.msg-block');
        const reordered = [];
        blocks.forEach((b) => {
            const idx = parseInt(b.dataset.index);
            reordered.push(messages[idx]);
        });
        messages = reordered;
        // Re-index
        blocks.forEach((b, i) => { b.dataset.index = i; });
        renumberBlocks();
    }

    function getBlockIndex(blockEl) {
        const blocks = [...listEl.querySelectorAll('.msg-block')];
        return blocks.indexOf(blockEl);
    }

    function renumberBlocks() {
        listEl.querySelectorAll('.msg-block').forEach((b, i) => {
            b.dataset.index = i;
            const num = b.querySelector('.block-number');
            if (num) num.textContent = i + 1;
        });
    }

    // ── Preview ──
    function renderPreview() {
        if (messages.length === 0) {
            previewEl.innerHTML = `<div class="text-center py-5" style="color:#8696a0;font-size:.8rem">
                Adicione mensagens para visualizar
            </div>`;
            return;
        }
        let html = '';
        const now = new Date();
        messages.forEach((msg, i) => {
            const time = new Date(now.getTime() + (i * 30000));
            const timeStr = time.getHours().toString().padStart(2,'0') + ':' + time.getMinutes().toString().padStart(2,'0');

            let content = '';
            if (msg.type === 'image' && msg.url) {
                content = `<img src="${escapeHtml(msg.url)}" alt="img">`;
            } else if (msg.type === 'video' && msg.url) {
                content = `<video src="${escapeHtml(msg.url)}" controls style="max-width:100%"></video>`;
            } else if (msg.type === 'image' || msg.type === 'video') {
                content = `<div style="background:rgba(255,255,255,.05);padding:1.5rem;border-radius:.375rem;text-align:center;color:#8696a0;font-size:.75rem">
                    <i class="bi bi-${msg.type === 'image' ? 'image' : 'camera-video'}" style="font-size:1.5rem"></i>
                    <div class="mt-1">${msg.type === 'image' ? 'Imagem' : 'Vídeo'}</div>
                </div>`;
            } else {
                content = `<div style="white-space:pre-wrap">${escapeHtml(msg.content || '...')}</div>`;
            }

            html += `<div class="wa-bubble">${content}<div class="wa-time">${timeStr}</div></div>`;
        });
        previewEl.innerHTML = html;
        previewEl.scrollTop = previewEl.scrollHeight;
    }

    // ── Helpers ──
    function escapeHtml(text) {
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    }

    function showToast(type, message) {
        const container = document.getElementById('toastContainer');
        if (!container) return alert(message);
        const id = 'toast-' + Date.now();
        const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
        container.insertAdjacentHTML('beforeend', `
            <div id="${id}" class="toast align-items-center text-bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body"><i class="bi ${icon} me-1"></i> ${escapeHtml(message)}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`);
        const toastEl = document.getElementById(id);
        const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
        toast.show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    }

    // ── Init ──
    renderAll();
})();
</script>
<?= $this->endSection() ?>
