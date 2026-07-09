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

    // ── Fetch propagator data ───────────────────────────────────────
    fetch(dataUrl)
        .then(r => r.json())
        .then(data => renderMarkers(data.nodes || []))
        .catch(err => {
            showNoData('Erro ao carregar dados do mapa.');
            console.error('[map]', err);
        });

    function renderMarkers(nodes) {
        // Filter propagators with valid lat/lng
        const geoNodes = nodes.filter(n =>
            n.latitude !== null && n.longitude !== null &&
            n.latitude !== 0   && n.longitude !== 0
        );

        if (!geoNodes.length) {
            showNoData('Nenhum dado de geolocalização disponível para esta campanha.');
            return;
        }

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
                '<div style="font-size:.85rem;line-height:1.6;">' +
                '<strong>' + escHtml(n.token) + '</strong><br>';
            if (n.name) {
                popupContent += 'Nome: ' + escHtml(n.name) + '<br>';
            }
            if (n.phone) {
                popupContent += 'WhatsApp: ' + escHtml(n.phone) + '<br>';
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

    function showNoData(message) {
        container.insertAdjacentHTML('afterend',
            '<div style="text-align:center;padding:2.5rem;color:#94a3b8;">' +
            '<i class="bi bi-geo-alt" style="font-size:2.5rem;"></i>' +
            '<p class="mt-2 mb-0">' + escHtml(message) + '</p>' +
            '</div>');
    }

    function escHtml(str) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(str || ''));
        return div.innerHTML;
    }
}
