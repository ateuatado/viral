/**
 * map.js — Leaflet map for propagator geolocation.
 *
 * Usage:  initMap('containerId', '/admin/api/campaigns/{id}/propagators');
 */

// eslint-disable-next-line no-unused-vars
function initMap(containerId, dataUrl) {
    'use strict';

    const container = document.getElementById(containerId);
    if (!container) return;

    // Depth-based color palette (deeper = warmer)
    const DEPTH_COLORS = [
        '#f59e0b', // 0 — amber  (seed)
        '#22c55e', // 1 — green
        '#3b82f6', // 2 — blue
        '#8b5cf6', // 3 — violet
        '#ec4899', // 4 — pink
        '#ef4444', // 5+ — red
    ];

    function depthColor(depth) {
        const idx = Math.min(depth, DEPTH_COLORS.length - 1);
        return DEPTH_COLORS[idx];
    }

    // ── Initialize Leaflet map ──────────────────────────────────────
    const map = L.map(containerId, {
        center: [-14.2350, -51.9253],
        zoom: 4,
        zoomControl: true,
    });

    // CartoDB Dark Matter tiles
    L.tileLayer('https://basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://carto.com/">CARTO</a> · OpenStreetMap',
        subdomains: 'abcd',
        maxZoom: 19,
    }).addTo(map);

    // ── Legend control ──────────────────────────────────────────────
    const legend = L.control({ position: 'bottomright' });
    legend.onAdd = function () {
        const div = L.DomUtil.create('div', 'info legend');
        div.style.background = '#0f172a';
        div.style.padding = '8px 12px';
        div.style.borderRadius = '6px';
        div.style.border = '1px solid #334155';
        div.style.color = '#e9edef';
        div.style.fontSize = '12px';
        div.style.lineHeight = '1.6';
        let html = '<div style="font-weight:600;margin-bottom:4px;">Profundidade</div>';
        for (let i = 0; i < DEPTH_COLORS.length; i++) {
            html += '<div><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:' +
                DEPTH_COLORS[i] + ';margin-right:6px;"></span>' +
                (i === 0 ? 'Semente (0)' : i === DEPTH_COLORS.length - 1 ? i + '+' : i) + '</div>';
        }
        html += '<hr style="border-color:#334155;margin:6px 0;">';
        html += '<div><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#f59e0b;margin-right:6px;border:2px solid #0f172a;width:14px;height:14px;"></span> Semente (10px)</div>';
        html += '<div><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#22c55e;margin-right:6px;"></span> Visitante (6px)</div>';
        div.innerHTML = html;
        return div;
    };
    legend.addTo(map);

    // ── Fetch propagator data ───────────────────────────────────────
    fetch(dataUrl)
        .then(r => r.json())
        .then(data => renderMarkers(data.nodes || [], data.meta || {}))
        .catch(err => {
            showNoData('Erro ao carregar dados do mapa.');
            console.error('[map]', err);
        });

    function renderMarkers(nodes, meta) {
        // Remove existing no-data messages
        const existingMsg = container.parentNode.querySelector('.map-no-data-msg');
        if (existingMsg) existingMsg.remove();

        // Filter propagators with valid lat/lng
        const geoNodes = nodes.filter(n =>
            n.latitude !== null && n.longitude !== null &&
            n.latitude !== 0   && n.longitude !== 0
        );

        if (!geoNodes.length) {
            showNoData('Nenhum dado de geolocalização disponível para esta campanha.');
            return;
        }

        // Show geo stats
        showGeoStats(meta, geoNodes.length);

        const markers = [];

        geoNodes.forEach(n => {
            const color = depthColor(n.depth);

            const marker = L.circleMarker([n.latitude, n.longitude], {
                radius: n.is_seed ? 10 : 6,
                fillColor: color,
                color: '#0f172a',
                weight: 1.5,
                opacity: 1,
                fillOpacity: 0.85,
            }).addTo(map);

            const type = n.is_seed ? 'Semente' : (n.viralized ? 'Viralizado' : 'Normal');
            let popupContent = 
                '<div style="font-size:.85rem;line-height:1.6;">';
            if (n.campaign_name) {
                popupContent += 'Campanha: <strong>' + escHtml(n.campaign_name) + '</strong><br>';
            }
            popupContent += 'Token: <strong>' + escHtml(n.token) + '</strong><br>';
            if (n.name) {
                popupContent += 'Nome: ' + escHtml(n.name) + '<br>';
            }
            if (n.email) {
                popupContent += 'E-mail: ' + escHtml(n.email) + '<br>';
            }
            if (n.phone) {
                popupContent += 'WhatsApp: ' + escHtml(n.phone) + '<br>';
            }
            if (n.discount !== undefined) {
                popupContent += 'Desconto Acumulado: ' + n.discount + '%<br>';
            }
            popupContent +=
                'Tipo: ' + type + '<br>' +
                'Profundidade: ' + n.depth + '<br>' +
                'Plataforma: ' + (n.platform || '—') + '<br>' +
                'Criado em: ' + (n.created_at || '—') +
                '</div>';

            marker.bindPopup(popupContent, { className: 'dark-popup' });

            markers.push(marker);
        });

        // Fit map bounds to markers
        if (markers.length) {
            const group = L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.15));
        }
    }

    function showGeoStats(meta, shownCount) {
        const total = meta.total_propagators || 0;
        const geo = meta.geo_propagators || shownCount;

        const existingStats = container.parentNode.querySelector('.map-geo-stats');
        if (existingStats) existingStats.remove();

        const statsEl = document.createElement('div');
        statsEl.className = 'map-geo-stats';
        statsEl.style.cssText = 'display:flex;flex-wrap:wrap;gap:12px;padding:12px 16px;margin-bottom:12px;background:#1e293b;border-radius:8px;border:1px solid #334155;color:#e9edef;font-size:13px;';
        statsEl.innerHTML =
            '<div><span style="color:#94a3b8;">Total de propagadores:</span> <strong>' + total + '</strong></div>' +
            '<div><span style="color:#94a3b8;">Com geolocalização:</span> <strong style="color:#22c55e;">' + geo + '</strong></div>' +
            '<div><span style="color:#94a3b8;">Exibidos no mapa:</span> <strong>' + shownCount + '</strong></div>';

        container.parentNode.insertBefore(statsEl, container);
    }

    function showNoData(message) {
        const existingMsg = container.parentNode.querySelector('.map-no-data-msg');
        if (existingMsg) existingMsg.remove();

        const msgEl = document.createElement('div');
        msgEl.className = 'map-no-data-msg';
        msgEl.style.cssText = 'text-align:center;padding:4rem 2rem;color:#94a3b8;';
        msgEl.innerHTML =
            '<i class="bi bi-geo-alt" style="font-size:2.5rem;display:block;margin-bottom:1rem;"></i>' +
            '<p class="mb-0">' + escHtml(message) + '</p>';
        container.parentNode.insertBefore(msgEl, container.nextSibling);
    }

    function escHtml(str) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(str || ''));
        return div.innerHTML;
    }
}
