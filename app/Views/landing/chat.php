<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= esc($campaign['contact_name'] ?? 'Contato') ?></title>

    <!-- Open Graph -->
    <meta property="og:title" content="<?= esc($campaign['og_title'] ?? $campaign['name']) ?>">
    <meta property="og:description" content="<?= esc($campaign['og_description'] ?? '') ?>">
    <meta property="og:image" content="<?= $campaign['og_image'] ? base_url($campaign['og_image']) : '' ?>">
    <meta property="og:url" content="<?= current_url() ?>">
    <meta property="og:type" content="website">

    <style>
        /* ── Reset & Base ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            height: 100%;
            overflow: hidden;
            font-family: 'Segoe UI', Helvetica, Arial, 'Apple Color Emoji', sans-serif;
            font-size: 15px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ── App Shell ── */
        .wa-app {
            display: flex;
            flex-direction: column;
            height: 100%;
            max-width: 100%;
            margin: 0 auto;
            background: #0b141a;
        }

        /* ── Header ── */
        .wa-header {
            background: #202c33;
            display: flex;
            align-items: center;
            padding: 8px 16px;
            gap: 12px;
            min-height: 56px;
            z-index: 10;
        }
        .wa-header-right {
            margin-left: auto;
            display: flex;
            align-items: center;
        }
        .wa-header-right a {
            color: #aebac1;
            text-decoration: none;
            font-size: 20px;
            padding: 8px;
        }
        .wa-header .back-btn {
            color: #aebac1;
            font-size: 20px;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        .wa-header .avatar {
            width: 40px; height: 40px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }
        .wa-header .avatar-placeholder {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: #00a884;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 600; font-size: 16px;
            flex-shrink: 0;
        }
        .wa-header .contact-info { flex: 1; min-width: 0; }
        .wa-header .contact-name {
            font-size: 16px; font-weight: 500; color: #e9edef;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .wa-header .contact-status {
            font-size: 13px; color: #8696a0;
        }
        .wa-header .header-actions { display: flex; gap: 16px; }
        .wa-header .header-actions svg {
            width: 22px; height: 22px; fill: #aebac1; cursor: pointer;
        }

        /* ── Chat Body ── */
        .wa-body {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 8px 6%;
            position: relative;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,.15) transparent;
        }
        .wa-body::-webkit-scrollbar { width: 6px; }
        .wa-body::-webkit-scrollbar-track { background: transparent; }
        .wa-body::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 3px; }

        /* WhatsApp doodle wallpaper */
        .wa-body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #0b141a;
            background-image:
                radial-gradient(circle at 20% 30%, rgba(255,255,255,.012) 1px, transparent 1px),
                radial-gradient(circle at 60% 70%, rgba(255,255,255,.012) 1px, transparent 1px),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,.012) 1px, transparent 1px),
                radial-gradient(circle at 40% 80%, rgba(255,255,255,.012) 1px, transparent 1px);
            background-size: 80px 80px, 120px 120px, 60px 60px, 100px 100px;
            pointer-events: none;
            z-index: 0;
            opacity: .6;
        }

        /* Date divider */
        .wa-date {
            text-align: center;
            margin: 12px 0;
            position: relative;
            z-index: 1;
        }
        .wa-date span {
            background: #182229;
            color: #8696a0;
            font-size: 12px;
            padding: 5px 12px;
            border-radius: 8px;
            display: inline-block;
            box-shadow: 0 1px 1px rgba(0,0,0,.13);
        }

        /* ── Message Bubbles ── */
        .wa-msg {
            display: flex;
            margin-bottom: 2px;
            position: relative;
            z-index: 1;
            opacity: 0;
            transform: translateY(10px);
            animation: msgIn .3s ease forwards;
        }
        @keyframes msgIn {
            to { opacity: 1; transform: translateY(0); }
        }
        .wa-bubble {
            background: #202c33;
            border-radius: 0 8px 8px 8px;
            padding: 6px 8px 6px 9px;
            max-width: 85%;
            min-width: 100px;
            position: relative;
            box-shadow: 0 1px 1px rgba(0,0,0,.13);
            color: #e9edef;
            font-size: 14.5px;
            line-height: 1.4;
            word-wrap: break-word;
        }
        .wa-msg:first-child .wa-bubble,
        .wa-msg.has-tail .wa-bubble {
            border-radius: 0 8px 8px 8px;
        }
        .wa-msg.has-tail .wa-bubble::before {
            content: '';
            position: absolute;
            top: 0; left: -8px;
            width: 0; height: 0;
            border-style: solid;
            border-width: 0 8px 8px 0;
            border-color: transparent #202c33 transparent transparent;
        }
        .wa-bubble .msg-text { white-space: pre-wrap; }
        .wa-bubble .msg-media {
            border-radius: 6px;
            overflow: hidden;
            margin: -2px -2px 4px -3px;
        }
        .wa-bubble .msg-media img,
        .wa-bubble .msg-media video {
            display: block;
            width: 100%;
            max-width: 330px;
            border-radius: 6px;
        }
        .wa-bubble .msg-footer {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 4px;
            margin-top: 2px;
        }
        .wa-bubble .msg-time {
            font-size: 11px;
            color: rgba(255,255,255,.45);
            line-height: 1;
        }

        /* ── Typing Indicator ── */
        .typing-indicator {
            display: flex;
            position: relative;
            z-index: 1;
            margin-bottom: 2px;
        }
        .typing-bubble {
            background: #202c33;
            border-radius: 0 8px 8px 8px;
            padding: 10px 14px;
            display: flex;
            gap: 4px;
            align-items: center;
            box-shadow: 0 1px 1px rgba(0,0,0,.13);
        }
        .typing-bubble::before {
            content: '';
            position: absolute;
            top: 0; left: -8px;
            width: 0; height: 0;
            border-style: solid;
            border-width: 0 8px 8px 0;
            border-color: transparent #202c33 transparent transparent;
        }
        .typing-dot {
            width: 7px; height: 7px;
            background: #8696a0;
            border-radius: 50%;
            animation: typingBounce 1.4s infinite ease-in-out;
        }
        .typing-dot:nth-child(2) { animation-delay: .2s; }
        .typing-dot:nth-child(3) { animation-delay: .4s; }
        @keyframes typingBounce {
            0%, 60%, 100% { transform: translateY(0); opacity: .5; }
            30% { transform: translateY(-5px); opacity: 1; }
        }

        /* ── Input Bar ── */
        .wa-footer {
            background: #202c33;
            display: flex;
            align-items: flex-end;
            padding: 6px 10px;
            gap: 8px;
            min-height: 52px;
            z-index: 10;
        }
        .wa-footer .footer-icon {
            width: 26px; height: 26px; fill: #8696a0;
            cursor: pointer; flex-shrink: 0;
            margin-bottom: 8px;
        }
        .wa-footer .input-wrapper {
            flex: 1;
            background: #2a3942;
            border-radius: 24px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            min-height: 40px;
        }
        .wa-footer .input-text {
            flex: 1;
            color: #8696a0;
            font-size: 15px;
        }
        .wa-footer .mic-btn {
            width: 42px; height: 42px;
            background: #00a884;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            cursor: pointer;
            margin-bottom: 2px;
        }
        .wa-footer .mic-btn svg { width: 20px; height: 20px; fill: #fff; }

                /* ── Offer Card (inline no chat) ── */
        .wa-offer {
            margin: 12px 0 8px;
            position: relative;
            z-index: 1;
            opacity: 0;
            transform: translateY(12px);
            animation: msgIn .4s ease forwards;
        }
        .wa-offer-card {
            background: #1f2c34;
            border-radius: 12px;
            padding: 20px 16px 24px;
            border: 1px solid rgba(255,255,255,.06);
            box-shadow: 0 2px 8px rgba(0,0,0,.2);
            touch-action: none;
        }
        .wa-offer-card .drag-handle {
            width: 40px; height: 5px;
            background: rgba(255,255,255,.15);
            border-radius: 3px;
            margin: 0 auto 16px;
            cursor: grab;
            touch-action: none;
        }
        .wa-offer-card .drag-handle:active { cursor: grabbing; }
        .wa-offer-card .offer-title {
            font-size: 20px; font-weight: 700;
            color: #e9edef;
            margin-bottom: 8px;
            text-align: center;
        }
        .wa-offer-card .offer-body {
            font-size: 14px; color: #8696a0;
            text-align: center;
            line-height: 1.5;
            margin-bottom: 16px;
        }
        .wa-offer-card .offer-img {
            width: 100%;
            border-radius: 10px;
            margin-bottom: 16px;
        }
        .wa-offer-card .offer-link {
            display: block;
            text-align: center;
            color: #00a884;
            text-decoration: underline;
            margin-bottom: 16px;
            font-size: 14px;
        }
        .wa-offer-card .offer-cta {
            display: block;
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #00a884 0%, #00d45d 100%);
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            text-align: center;
            transition: transform .15s, box-shadow .15s;
        }
        .wa-offer-card .offer-cta:active {
            transform: scale(.97);
        }

        /* ── Share Panel (dentro do card) ── */
        .share-panel {
            display: none;
            text-align: center;
        }
        .share-panel.visible { display: block; }
        .share-panel .share-title {
            color: #e9edef; font-size: 18px; font-weight: 700;
            margin-bottom: 16px;
        }
        .share-panel .share-link-box {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }
        .share-panel .share-link-box input {
            flex: 1;
            background: #2a3942;
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 10px;
            padding: 10px 14px;
            color: #e9edef;
            font-size: 13px;
            outline: none;
        }
        .share-panel .share-link-box button {
            background: #2a3942;
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 10px;
            padding: 10px 14px;
            color: #00a884;
            font-size: 14px; font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
        }
        .share-wa-btn {
            display: inline-flex;
            align-items: center; justify-content: center;
            gap: 10px;
            background: #25d366;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 14px 28px;
            font-size: 16px; font-weight: 700;
            cursor: pointer;
            width: 100%;
        }
        .share-wa-btn svg { width: 24px; height: 24px; fill: #fff; }

        /* ── Utility ── */
        .hidden { display: none !important; }
    </style>
</head>
<body>

<div class="wa-app">
    <!-- Header -->
    <div class="wa-header">
        <a class="back-btn" href="javascript:void(0)">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M12 4l-8 8 8 8 1.4-1.4L7.8 13H20v-2H7.8l5.6-5.6z"/></svg>
        </a>
        <?php if (!empty($campaign['contact_avatar']) && $campaign['contact_avatar'] !== '/assets/image/default-avatar.png'): ?>
            <img class="avatar" src="<?= esc($campaign['contact_avatar']) ?>" alt="avatar">
        <?php else: ?>
            <div class="avatar-placeholder"><?= mb_strtoupper(mb_substr(esc($campaign['contact_name'] ?? 'C'), 0, 1)) ?></div>
        <?php endif; ?>
        <div class="contact-info">
            <div class="contact-name"><?= esc($campaign['contact_name'] ?? 'Contato') ?></div>
            <div class="contact-status">online</div>
        </div>
        <div class="header-actions">
            <svg viewBox="0 0 24 24"><path d="M15.9 14.3H15l-.3-.3c1-1.1 1.6-2.7 1.6-4.3 0-3.7-3-6.7-6.7-6.7S3 6 3 9.7s3 6.7 6.7 6.7c1.6 0 3.2-.6 4.3-1.6l.3.3v.8l5.1 5.1 1.5-1.5-5-5.2zm-6.2 0c-2.6 0-4.6-2.1-4.6-4.6s2.1-4.6 4.6-4.6 4.6 2.1 4.6 4.6-2 4.6-4.6 4.6z"/></svg>
            <svg viewBox="0 0 24 24"><path d="M12 7a2 2 0 100-4 2 2 0 000 4zm0 2a2 2 0 100 4 2 2 0 000-4zm0 6a2 2 0 100 4 2 2 0 000-4z"/></svg>
        </div>
        <div class="wa-header-right">
            <a href="<?= url_to('login') ?>" title="Acessar meu painel">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
            </a>
        </div>
    </div>

    <!-- Chat Body -->
    <div class="wa-body" id="chatBody">
        <div class="wa-date"><span>Hoje</span></div>
    </div>

    <!-- Input Bar (decorative) -->
    <div class="wa-footer">
        <svg class="footer-icon" viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zM8.9 7.1c.5-.5 1.4-.5 1.9 0 .5.5.5 1.4 0 1.9-.5.5-1.4.5-1.9 0-.5-.5-.5-1.4 0-1.9zm-1.5 8.4c-.1-.1-.1-.3 0-.4 1.3-2.7 3.4-4.1 4.7-4.1 1.3 0 3.4 1.4 4.7 4.1.1.1.1.3 0 .4-.1.1-.3.1-.4 0-1.1-2.3-3-3.5-4.3-3.5s-3.1 1.2-4.3 3.5c-.1.1-.3.1-.4 0zm7.7-6.5c-.5-.5-.5-1.4 0-1.9.5-.5 1.4-.5 1.9 0 .5.5.5 1.4 0 1.9-.5.5-1.4.5-1.9 0z"/></svg>
        <div class="input-wrapper">
            <div class="input-text">Mensagem</div>
        </div>
        <svg class="footer-icon" viewBox="0 0 24 24"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm-2 6a1 1 0 112 0v4a1 1 0 11-2 0V8zm5 0a1 1 0 112 0v4a1 1 0 11-2 0V8z"/></svg>
        <div class="mic-btn">
            <svg viewBox="0 0 24 24"><path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm-1-9c0-.55.45-1 1-1s1 .45 1 1v6c0 .55-.45 1-1 1s-1-.45-1-1V5zm6 6c0 2.76-2.24 5-5 5s-5-2.24-5-5H5c0 3.53 2.61 6.43 6 6.92V21h2v-3.08c3.39-.49 6-3.39 6-6.92h-2z"/></svg>
        </div>
    </div>
</div>

<!-- Offer & Share panels are injected inline into chatBody by JS -->

<!-- Config -->
<script>
const VIRAL_CONFIG = {
    campaignSlug: <?= json_encode($campaign['slug']) ?>,
    parentToken: <?= json_encode($parentToken) ?>,
    chatMessages: <?= json_encode($chatMessages) ?>,
    contactName: <?= json_encode($campaign['contact_name'] ?? 'Contato') ?>,
    contactAvatar: <?= json_encode($campaign['contact_avatar'] ?? '/assets/image/default-avatar.png') ?>,
    configGeoloc: <?= $campaign['config_geoloc'] ? 'true' : 'false' ?>,
    configGeolocMode: <?= json_encode($campaign['config_geoloc_mode'] ?? 'explicit') ?>,
    offerType: <?= json_encode($campaign['offer_type'] ?? 'none') ?>,
    offerTitle: <?= json_encode($campaign['offer_title'] ?? '') ?>,
    offerBody: <?= json_encode($campaign['offer_body'] ?? '') ?>,
    offerImage: <?= json_encode($campaign['offer_image'] ?? '') ?>,
    offerLinkUrl: <?= json_encode($campaign['offer_link_url'] ?? '') ?>,
    offerLinkText: <?= json_encode($campaign['offer_link_text'] ?? '') ?>,
    offerCtaText: <?= json_encode($campaign['offer_cta_text'] ?? 'Compartilhe e ganhe!') ?>,
    successMessage: <?= json_encode($campaign['success_message'] ?? '') ?>,
    successTitle: <?= json_encode($campaign['success_title'] ?? '') ?>,
    ownerMessage: <?= json_encode($campaign['owner_message'] ?? '') ?>,
    csrfName: <?= json_encode($csrfName) ?>,
    csrfHash: <?= json_encode($csrfHash) ?>,
    baseUrl: <?= json_encode(base_url()) ?>,
    isParentViralized: <?= $isParentViralized ? 'true' : 'false' ?>,
    parentName: <?= json_encode($parentName ?? '') ?>,
    parentDiscount: <?= (int)$parentDiscount ?>,
    parentMaxDepth: <?= (int)$parentMaxDepth ?>,
};
</script>

<script>
(() => {
    'use strict';

    const C = VIRAL_CONFIG;
    const chatBody = document.getElementById('chatBody');

    let propagatorId = null;
    let propagatorToken = null;

    // ════════════════════════════════════════
    //  FINGERPRINT
    // ════════════════════════════════════════
    function generateFingerprint() {
        const parts = [];
        // Canvas hash
        try {
            const canvas = document.createElement('canvas');
            canvas.width = 200; canvas.height = 50;
            const ctx = canvas.getContext('2d');
            ctx.textBaseline = 'top';
            ctx.font = '14px Arial';
            ctx.fillText('fingerprint:' + navigator.userAgent, 2, 2);
            ctx.fillStyle = 'rgba(102,204,0,.7)';
            ctx.fillRect(75, 1, 100, 20);
            parts.push(canvas.toDataURL());
        } catch(e) {}
        parts.push(screen.width + 'x' + screen.height);
        parts.push(screen.colorDepth);
        parts.push(Intl.DateTimeFormat().resolvedOptions().timeZone);
        parts.push(navigator.language);
        parts.push(navigator.hardwareConcurrency);
        // Simple hash
        let hash = 0;
        const str = parts.join('|');
        for (let i = 0; i < str.length; i++) {
            hash = ((hash << 5) - hash) + str.charCodeAt(i);
            hash |= 0;
        }
        return Math.abs(hash).toString(36);
    }

    // ════════════════════════════════════════
    //  TRACKING
    // ════════════════════════════════════════
    async function sendTracking() {
        try {
            const resp = await fetch(C.baseUrl + 'api/track', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    [C.csrfName]: C.csrfHash,
                    parent_token: C.parentToken,
                    campaign_slug: C.campaignSlug,
                    fingerprint: generateFingerprint(),
                    referrer: document.referrer || null,
                    language: navigator.language,
                    screen_resolution: screen.width + 'x' + screen.height,
                    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                    platform: navigator.platform,
                }),
            });
            const data = await resp.json();
            if (data.success) {
                propagatorId = data.propagator_id;
                propagatorToken = data.propagator_token;
                if (C.configGeoloc) requestGeo();
            }
        } catch (e) {
            console.error('Tracking error:', e);
        }
    }

    // ════════════════════════════════════════
    //  GEOLOCATION
    // ════════════════════════════════════════
    function requestGeo() {
        if (!propagatorId) return;
        if (C.configGeolocMode === 'explicit') {
            if (!navigator.geolocation) return sendGeoResult(false);
            navigator.geolocation.getCurrentPosition(
                (pos) => sendGeoResult(true, pos.coords.latitude, pos.coords.longitude, pos.coords.accuracy),
                () => sendGeoResult(false)
            );
        }
    }

    async function sendGeoResult(granted, lat, lng, accuracy) {
        try {
            await fetch(C.baseUrl + 'api/track/geo', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    [C.csrfName]: C.csrfHash,
                    propagator_id: propagatorId,
                    granted,
                    latitude: lat || null,
                    longitude: lng || null,
                    accuracy: accuracy || null,
                }),
            });
        } catch (e) {
            console.error('Geo error:', e);
        }
    }

    // ════════════════════════════════════════
    //  CHAT ANIMATION
    // ════════════════════════════════════════
    function formatTime() {
        const d = new Date();
        return d.getHours().toString().padStart(2, '0') + ':' + d.getMinutes().toString().padStart(2, '0');
    }

    function escapeHtml(s) {
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function showTyping() {
        const el = document.createElement('div');
        el.className = 'typing-indicator';
        el.id = 'typingIndicator';
        el.innerHTML = `<div class="typing-bubble">
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
        </div>`;
        chatBody.appendChild(el);
        scrollToBottom();
    }

    function hideTyping() {
        const el = document.getElementById('typingIndicator');
        if (el) el.remove();
    }

    function addMessage(msg, isFirst) {
        const div = document.createElement('div');
        div.className = 'wa-msg' + (isFirst ? ' has-tail' : '');

        let inner = '';
        if (msg.type === 'image' && msg.url) {
            inner = `<div class="msg-media"><img src="${escapeHtml(msg.url)}" alt="img" loading="lazy"></div>`;
        } else if (msg.type === 'video' && msg.url) {
            inner = `<div class="msg-media"><video src="${escapeHtml(msg.url)}" controls playsinline></video></div>`;
        }

        if (msg.type === 'text' || (!msg.url && msg.content)) {
            inner += `<div class="msg-text">${escapeHtml(msg.content || '')}</div>`;
        }

        inner += `<div class="msg-footer"><span class="msg-time">${formatTime()}</span></div>`;
        div.innerHTML = `<div class="wa-bubble">${inner}</div>`;
        chatBody.appendChild(div);
        scrollToBottom();
    }

    function scrollToBottom() {
        requestAnimationFrame(() => {
            chatBody.scrollTop = chatBody.scrollHeight;
        });
    }

    async function playMessages() {
        const msgs = C.chatMessages || [];
        for (let i = 0; i < msgs.length; i++) {
            const msg = msgs[i];
            const delay = parseInt(msg.delay) || 1500;

            // Show typing
            showTyping();
            await sleep(Math.min(delay, 800));
            // Wait remainder
            await sleep(Math.max(delay - 800, 200));
            hideTyping();

            addMessage(msg, i === 0);
        }

        // After all messages, show offer
        await sleep(1200);
        showOffer();
    }

    function sleep(ms) {
        return new Promise(r => setTimeout(r, ms));
    }

        // ════════════════════════════════════════
    //  OFFER (inline no chat)
    // ════════════════════════════════════════
    function showOffer() {
        const wrapper = document.createElement('div');
        wrapper.className = 'wa-offer';
        wrapper.id = 'offerWrapper';

        let bodyHtml = '';
        if (C.offerTitle) bodyHtml += `<div class="offer-title">${escapeHtml(C.offerTitle)}</div>`;
        if (C.offerBody) bodyHtml += `<div class="offer-body">${escapeHtml(C.offerBody)}</div>`;
        if (C.offerType === 'image' && C.offerImage) {
            bodyHtml += `<img class="offer-img" src="${escapeHtml(C.offerImage)}" alt="oferta">`;
        }
        if (C.offerType === 'link' && C.offerLinkUrl) {
            bodyHtml += `<a class="offer-link" href="${escapeHtml(C.offerLinkUrl)}" target="_blank" rel="noopener">${escapeHtml(C.offerLinkText || C.offerLinkUrl)}</a>`;
        }

        // Form fields
        bodyHtml += `
        <div style="margin-bottom: 16px; text-align: left;">
            <label style="display:block; font-size:12px; color:#8696a0; margin-bottom:4px; font-weight:500;">Seu Nome:</label>
            <input type="text" id="leadName" placeholder="Digite seu nome completo" style="width:100%; background:#2a3942; border:1px solid rgba(255,255,255,.1); border-radius:10px; padding:12px 14px; color:#e9edef; font-size:14px; outline:none; margin-bottom:12px;">

            <label style="display:block; font-size:12px; color:#8696a0; margin-bottom:4px; font-weight:500;">Seu E-mail:</label>
            <input type="email" id="leadEmail" placeholder="seuemail@exemplo.com" style="width:100%; background:#2a3942; border:1px solid rgba(255,255,255,.1); border-radius:10px; padding:12px 14px; color:#e9edef; font-size:14px; outline:none; margin-bottom:12px;">

            <label style="display:block; font-size:12px; color:#8696a0; margin-bottom:4px; font-weight:500;">Seu WhatsApp (Telefone):</label>
            <input type="tel" id="leadPhone" placeholder="(00) 00000-0000" style="width:100%; background:#2a3942; border:1px solid rgba(255,255,255,.1); border-radius:10px; padding:12px 14px; color:#e9edef; font-size:14px; outline:none;">
            <div id="formError" style="color:#f25c54; font-size:12px; margin-top:6px; display:none; font-weight:500;"></div>
        </div>`;

        bodyHtml += `<button class="offer-cta" id="btnCta">${escapeHtml(C.offerCtaText)}</button>`;

        wrapper.innerHTML = `
            <div class="wa-offer-card">
                <div class="drag-handle" id="dragHandle"></div>
                <div id="offerContent">${bodyHtml}</div>
                <div id="sharePanel" class="share-panel"></div>
            </div>`;

        chatBody.appendChild(wrapper);
        scrollToBottom();

        // Drag-to-dismiss / drag-to-top
        setupDrag(wrapper);

        // Phone mask
        const phoneInput = document.getElementById('leadPhone');
        if (phoneInput) {
            phoneInput.addEventListener('input', (e) => {
                let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
                e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
            });
        }

        document.getElementById('btnCta').addEventListener('click', handleViralize);
    }

    function setupDrag(wrapper) {
        const handle = wrapper.querySelector('.drag-handle');
        if (!handle) return;

        let startY = 0, currentY = 0, dragging = false;
        const card = wrapper.querySelector('.wa-offer-card');
        const body = chatBody;

        function onStart(e) {
            dragging = true;
            startY = e.type === 'touchstart' ? e.touches[0].clientY : e.clientY;
            currentY = startY;
            card.style.transition = 'none';
            card.style.position = 'relative';
            card.style.zIndex = '10';
        }

        function onMove(e) {
            if (!dragging) return;
            e.preventDefault();
            const y = e.type === 'touchmove' ? e.touches[0].clientY : e.clientY;
            const delta = y - startY;
            currentY = y;

            if (delta < 0) {
                // Dragging up — scroll the chat up instead of moving the card
                body.scrollTop -= delta * 0.3;
                startY = y;
            } else {
                // Dragging down — move card with resistance
                card.style.transform = `translateY(${delta * 0.4}px)`;
                card.style.opacity = Math.max(0.3, 1 - delta / 400);
            }
        }

        function onEnd() {
            if (!dragging) return;
            dragging = false;
            const delta = currentY - startY;
            card.style.transition = 'transform .3s ease, opacity .3s ease';
            card.style.transform = '';
            card.style.opacity = '';

            if (delta > 120) {
                // Swipe down far enough — collapse card into a small bar
                collapseCard(wrapper);
            }
        }

        handle.addEventListener('mousedown', onStart);
        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onEnd);

        handle.addEventListener('touchstart', onStart, { passive: true });
        document.addEventListener('touchmove', onMove, { passive: false });
        document.addEventListener('touchend', onEnd);
    }

    function collapseCard(wrapper) {
        const card = wrapper.querySelector('.wa-offer-card');
        card.style.transition = 'all .3s ease';
        card.style.transform = '';
        card.style.opacity = '';
        card.style.maxHeight = '48px';
        card.style.overflow = 'hidden';
        card.style.padding = '10px 16px';
        card.innerHTML = `
            <div style="display:flex;align-items:center;justify-content:space-between;cursor:pointer;" id="restoreBar">
                <span style="color:#8696a0;font-size:13px;">📋 Formulário recolhido</span>
                <span style="color:#00a884;font-size:13px;font-weight:600;">Expandir ▲</span>
            </div>`;
        card.querySelector('#restoreBar').addEventListener('click', () => {
            // Rebuild the full offer card
            wrapper.remove();
            showOffer();
        });
        scrollToBottom();
    }

    // ════════════════════════════════════════
    //  VIRALIZE
    // ════════════════════════════════════════
    async function handleViralize() {
        const nameInput = document.getElementById('leadName');
        const emailInput = document.getElementById('leadEmail');
        const phoneInput = document.getElementById('leadPhone');
        const errDiv = document.getElementById('formError');
        
        errDiv.style.display = 'none';
        errDiv.textContent = '';

        const name = nameInput.value.trim();
        const email = emailInput.value.trim();
        const phone = phoneInput.value.trim();

        if (!name) {
            errDiv.textContent = 'Por favor, insira o seu nome.';
            errDiv.style.display = 'block';
            nameInput.focus();
            return;
        }

        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            errDiv.textContent = 'Por favor, insira um e-mail válido.';
            errDiv.style.display = 'block';
            emailInput.focus();
            return;
        }

        const numericPhone = phone.replace(/\D/g, '');
        if (numericPhone.length < 10 || numericPhone.length > 11) {
            errDiv.textContent = 'Por favor, insira um WhatsApp válido com DDD.';
            errDiv.style.display = 'block';
            phoneInput.focus();
            return;
        }

        const btn = document.getElementById('btnCta');
        btn.disabled = true;
        btn.textContent = 'Processando...';

        try {
            const resp = await fetch(C.baseUrl + 'api/viralize', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    [C.csrfName]: C.csrfHash,
                    propagator_id: propagatorId,
                    name: name,
                    email: email,
                    phone: phone,
                }),
            });
            const data = await resp.json();
            if (!resp.ok) throw new Error(data.error || 'Erro ao processar.');
            // Save token to local storage so we know we own this link
            localStorage.setItem('my_viral_token_' + C.campaignSlug, data.token);
            showSharePanel(data.share_url);
        } catch (e) {
            btn.disabled = false;
            btn.textContent = C.offerCtaText;
            errDiv.textContent = e.message;
            errDiv.style.display = 'block';
            console.error('Viralize error:', e);
        }
    }

                function showSharePanel(url) {
        const sharePanel = document.getElementById('sharePanel');
        const offerContent = document.getElementById('offerContent');
        if (offerContent) offerContent.classList.add('hidden');

        // Build success message (custom or default)
        let successHtml = '';
        if (C.successMessage) {
            const personalized = C.successMessage.replace(/\{nome\}/g, escapeHtml(C.parentName || ''));
            successHtml = '<div style="font-size:13px; color:#e9edef; margin-bottom:20px; line-height:1.6; text-align:left; white-space:pre-wrap;">' + personalized + '</div>';
        } else {
            successHtml = `
                <div style="font-size:12px; color:#8696a0; margin-bottom:20px; line-height:1.5;">
                    Enviamos um e-mail com as regras e o seu link de acesso exclusivo.<br>
                    <strong>Seu desconto inicial de 10% já está ativo!</strong><br>
                    Indique amigos e aumente seu desconto para até 80%!
                </div>`;
        }

        if (sharePanel) {
            sharePanel.innerHTML = `
                <div class="share-title" style="font-size:1.15rem; margin-bottom:8px;">${escapeHtml(C.successTitle || '🎯 Você entrou na Corrida de Cupons!')}</div>
                ${successHtml}
                <div class="share-link-box">
                    <input type="text" id="shareLinkInput" value="${escapeHtml(url)}" readonly>
                    <button type="button" id="btnCopyLink">Copiar</button>
                </div>
                <button type="button" class="share-wa-btn" id="btnShareWa">
                    <svg viewBox="0 0 24 24"><path d="M17.5 14.4l-2-1c-.3-.1-.5-.1-.7.1l-.9 1.1c-.2.2-.3.2-.6.1-1.7-.9-2.8-2-3.7-3.5-.2-.3-.1-.5.1-.7l.5-.6c.2-.2.2-.3.3-.5s0-.4-.1-.5l-1-2.4c-.3-.6-.5-.6-.7-.6h-.6c-.2 0-.6.1-.9.4-.3.3-1.2 1.2-1.2 2.8 0 1.7 1.2 3.3 1.4 3.5.2.2 2.4 3.6 5.7 5 .8.3 1.4.5 1.9.7.8.3 1.5.2 2.1.1.6-.1 2-.8 2.3-1.6.3-.8.3-1.5.2-1.6-.1-.2-.3-.3-.6-.4zM12 2C6.5 2 2 6.5 2 12c0 1.8.5 3.4 1.3 4.8L2 22l5.3-1.4c1.4.8 3 1.2 4.7 1.2 5.5 0 10-4.5 10-10S17.5 2 12 2z"/></svg>
                    Compartilhar no WhatsApp
                </button>`;
            sharePanel.classList.add('visible');
        }

        document.getElementById('btnCopyLink')?.addEventListener('click', () => {
            const input = document.getElementById('shareLinkInput');
            navigator.clipboard.writeText(input.value).then(() => {
                document.getElementById('btnCopyLink').textContent = 'Copiado! ✓';
                setTimeout(() => {
                    document.getElementById('btnCopyLink').textContent = 'Copiar';
                }, 2000);
            });
        });

        document.getElementById('btnShareWa')?.addEventListener('click', () => {
            const text = C.offerTitle ? C.offerTitle + ' — ' + url : url;
            window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(text), '_blank');
        });

        scrollToBottom();
    }

    function showOwnerDashboard() {
        const wrapper = document.createElement('div');
        wrapper.className = 'wa-offer';
        wrapper.id = 'offerWrapper';

        const nextDepth = C.parentMaxDepth + 1;
        const nextDiscount = Math.min(80, nextDepth * 10);
        const percentText = C.parentDiscount > 0 ? `${C.parentDiscount}% de desconto!` : 'nenhum desconto ainda.';

        let ownerHtml = '';
        if (C.ownerMessage) {
            let msg = C.ownerMessage;
            msg = msg.replace(/\{nome\}/g, C.parentName);
            msg = msg.replace(/\{desconto\}/g, percentText);
            msg = msg.replace(/\{niveis\}/g, C.parentMaxDepth);
            ownerHtml = `<div style="font-size: 13px; color: #8696a0; margin-bottom: 20px; text-align: left; line-height: 1.5; white-space: pre-wrap;">${escapeHtml(msg)}</div>`;
        } else {
            ownerHtml = `
            <div style="font-size: 14px; color: #8696a0; margin-bottom: 20px;">
                Você conquistou acumulado <strong style="color:#22c55e; font-size:16px;">${percentText}</strong>
            </div>`;
        }

        let progressHtml = '';
        if (C.parentDiscount < 80) {
            progressHtml = `
            <div style="background:rgba(255,255,255,.03); border:1px solid rgba(255,255,255,.05); border-radius:12px; padding:16px; margin: 20px 0; text-align:left; font-size:13px; line-height:1.6; color:#8696a0;">
                <div style="color:#22c55e; font-weight:600; font-size:14px; margin-bottom:8px;">🎯 Próxima Meta:</div>
                Se o seu amigo que você indicou compartilhar com outra pessoa, seu desconto sobe para <strong style="color:#e9edef;">${nextDiscount}%</strong>!<br>
                <span style="font-size:11px; color:#64748b;">(Meta atual: nível ${nextDepth} de profundidade na sua rede).</span>
            </div>`;
        } else {
            progressHtml = `
            <div style="background:rgba(34,197,94,.1); border:1px solid rgba(34,197,94,.2); border-radius:12px; padding:16px; margin: 20px 0; text-align:left; font-size:13px; line-height:1.6; color:#22c55e;">
                🏆 <strong>Parabéns!</strong> Você atingiu a profundidade máxima e conquistou o desconto limite de <strong>80%</strong>!
            </div>`;
        }

        const shareUrl = C.baseUrl + 'v/' + C.campaignSlug + '/' + C.parentToken;

        wrapper.innerHTML = `
            <div class="wa-offer-card">
                <div class="drag-handle" id="dragHandle"></div>
                <div class="share-panel visible">
                    <div class="share-title" style="margin-bottom: 8px;">👋 Olá, ${escapeHtml(C.parentName)}!</div>
                    ${ownerHtml}
                    <div style="text-align: left; font-size:13px; color:#8696a0; margin-bottom: 12px;">
                        🔑 Níveis ativos na sua rede: <strong>${C.parentMaxDepth}</strong>
                    </div>
                    ${progressHtml}
                    <div class="share-link-box">
                        <input type="text" id="shareLinkInput" value="${escapeHtml(shareUrl)}" readonly>
                        <button type="button" id="btnCopyLink">Copiar</button>
                    </div>
                    <button type="button" class="share-wa-btn" id="btnShareWa">
                        <svg viewBox="0 0 24 24"><path d="M17.5 14.4l-2-1c-.3-.1-.5-.1-.7.1l-.9 1.1c-.2.2-.3.2-.6.1-1.7-.9-2.8-2-3.7-3.5-.2-.3-.1-.5.1-.7l.5-.6c.2-.2.2-.3.3-.5s0-.4-.1-.5l-1-2.4c-.3-.6-.5-.6-.7-.6h-.6c-.2 0-.6.1-.9.4-.3.3-1.2 1.2-1.2 2.8 0 1.7 1.2 3.3 1.4 3.5.2.2 2.4 3.6 5.7 5 .8.3 1.4.5 1.9.7.8.3 1.5.2 2.1.1.6-.1 2-.8 2.3-1.6.3-.8.3-1.5.2-1.6-.1-.2-.3-.3-.6-.4zM12 2C6.5 2 2 6.5 2 12c0 1.8.5 3.4 1.3 4.8L2 22l5.3-1.4c1.4.8 3 1.2 4.7 1.2 5.5 0 10-4.5 10-10S17.5 2 12 2z"/></svg>
                        Compartilhar no WhatsApp
                    </button>
                </div>
            </div>`;

        chatBody.appendChild(wrapper);
        setupDrag(wrapper);
        scrollToBottom();

        document.getElementById('btnCopyLink')?.addEventListener('click', () => {
            const input = document.getElementById('shareLinkInput');
            navigator.clipboard.writeText(input.value).then(() => {
                document.getElementById('btnCopyLink').textContent = 'Copiado! ✓';
                setTimeout(() => {
                    document.getElementById('btnCopyLink').textContent = 'Copiar';
                }, 2000);
            });
        });

        document.getElementById('btnShareWa')?.addEventListener('click', () => {
            const text = C.offerTitle ? C.offerTitle + ' — ' + shareUrl : shareUrl;
            window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(text), '_blank');
        });
    }

    // ════════════════════════════════════════
    //  INIT
    // ════════════════════════════════════════
    const mySavedToken = localStorage.getItem('my_viral_token_' + C.campaignSlug);
    if (C.isParentViralized && mySavedToken === C.parentToken) {
        showOwnerDashboard();
    } else {
        sendTracking();
        playMessages();
    }

})();
</script>

</body>
</html>
