/**
 * graph.js — D3.js force-directed graph for viral propagation.
 *
 * Usage:  initGraph('containerId', '/admin/api/campaigns/{id}/propagators');
 */

// eslint-disable-next-line no-unused-vars
function initGraph(containerId, dataUrl) {
    'use strict';

    const COLORS = {
        seed:       '#f59e0b', // amber
        viralized:  '#22c55e', // green
        normal:     '#6366f1', // indigo
        link:       '#475569', // slate
    };

    const container = document.getElementById(containerId);
    if (!container) return;

    const width  = container.clientWidth;
    const height = Math.max(container.clientHeight, 600);

    // ── SVG + zoom ──────────────────────────────────────────────────
    const svg = d3.select(container)
        .append('svg')
        .attr('width', width)
        .attr('height', height);

    const g = svg.append('g');

    const zoom = d3.zoom()
        .scaleExtent([0.1, 6])
        .on('zoom', (event) => g.attr('transform', event.transform));

    svg.call(zoom);

    // ── Tooltip ─────────────────────────────────────────────────────
    const tooltip = d3.select(container)
        .append('div')
        .style('position', 'absolute')
        .style('pointer-events', 'none')
        .style('background', 'rgba(15,23,42,.92)')
        .style('border', '1px solid #334155')
        .style('border-radius', '.5rem')
        .style('padding', '.6rem .85rem')
        .style('font-size', '.78rem')
        .style('color', '#e2e8f0')
        .style('line-height', '1.5')
        .style('opacity', 0)
        .style('z-index', 20);

    // ── Fetch data ──────────────────────────────────────────────────
    fetch(dataUrl)
        .then(r => r.json())
        .then(data => render(data))
        .catch(err => {
            container.insertAdjacentHTML('beforeend',
                '<div style="text-align:center;padding:3rem;color:#94a3b8;">' +
                '<i class="bi bi-exclamation-triangle" style="font-size:2rem;"></i>' +
                '<p class="mt-2 mb-0">Erro ao carregar dados do grafo.</p></div>');
            console.error('[graph]', err);
        });

    function render({ nodes, links }) {
        if (!nodes.length) {
            container.insertAdjacentHTML('beforeend',
                '<div style="text-align:center;padding:3rem;color:#94a3b8;">' +
                '<i class="bi bi-diagram-2" style="font-size:2.5rem;"></i>' +
                '<p class="mt-2 mb-0">Nenhum propagador encontrado.</p></div>');
            return;
        }

        // Build id-based lookup for D3 force
        const nodeById = new Map(nodes.map(n => [n.id, n]));
        const validLinks = links.filter(l => nodeById.has(l.source) && nodeById.has(l.target));

        // ── Simulation ──────────────────────────────────────────────
        const simulation = d3.forceSimulation(nodes)
            .force('link', d3.forceLink(validLinks).id(d => d.id).distance(80))
            .force('charge', d3.forceManyBody().strength(-200))
            .force('center', d3.forceCenter(width / 2, height / 2))
            .force('collide', d3.forceCollide().radius(d => nodeRadius(d) + 4));

        // ── Links ───────────────────────────────────────────────────
        const link = g.append('g')
            .attr('stroke', COLORS.link)
            .attr('stroke-opacity', 0.5)
            .selectAll('line')
            .data(validLinks)
            .join('line')
            .attr('stroke-width', 1.5);

        // ── Nodes ───────────────────────────────────────────────────
        const node = g.append('g')
            .selectAll('circle')
            .data(nodes)
            .join('circle')
            .attr('r', d => nodeRadius(d))
            .attr('fill', d => nodeColor(d))
            .attr('stroke', '#0f172a')
            .attr('stroke-width', 1.5)
            .attr('cursor', 'grab')
            .call(drag(simulation));

        // ── Labels (truncated token) ────────────────────────────────
        const label = g.append('g')
            .selectAll('text')
            .data(nodes)
            .join('text')
            .text(d => {
                if (d.name) {
                    const firstWord = d.name.trim().split(' ')[0];
                    const displayName = firstWord.length > 12 ? firstWord.substring(0, 10) + '..' : firstWord;
                    return d.discount > 0 ? `${displayName} (${d.discount}%)` : displayName;
                }
                return d.token ? d.token.substring(0, 6) : '';
            })
            .attr('font-size', '9px')
            .attr('fill', '#94a3b8')
            .attr('text-anchor', 'middle')
            .attr('dy', d => -(nodeRadius(d) + 5))
            .attr('pointer-events', 'none');

        // ── Hover ───────────────────────────────────────────────────
        node.on('mouseover', function (event, d) {
                d3.select(this).attr('stroke', '#f8fafc').attr('stroke-width', 2.5);
                tooltip.style('opacity', 1).html(tooltipHtml(d));
            })
            .on('mousemove', function (event) {
                const rect = container.getBoundingClientRect();
                tooltip
                    .style('left', (event.clientX - rect.left + 14) + 'px')
                    .style('top',  (event.clientY - rect.top  - 10) + 'px');
            })
            .on('mouseout', function () {
                d3.select(this).attr('stroke', '#0f172a').attr('stroke-width', 1.5);
                tooltip.style('opacity', 0);
            });

        // ── Tick ────────────────────────────────────────────────────
        simulation.on('tick', () => {
            link
                .attr('x1', d => d.source.x)
                .attr('y1', d => d.source.y)
                .attr('x2', d => d.target.x)
                .attr('y2', d => d.target.y);

            node
                .attr('cx', d => d.x)
                .attr('cy', d => d.y);

            label
                .attr('x', d => d.x)
                .attr('y', d => d.y);
        });
    }

    // ── Helpers ──────────────────────────────────────────────────────
    function nodeColor(d) {
        if (d.is_seed) return COLORS.seed;
        if (d.viralized) return COLORS.viralized;
        return COLORS.normal;
    }

    function nodeRadius(d) {
        if (d.is_seed) return 14;
        return Math.max(6, 10 - d.depth);
    }

    function tooltipHtml(d) {
        const type = d.is_seed ? 'Semente' : (d.viralized ? 'Viralizado' : 'Normal');
        let html = `<strong>${d.token}</strong><br>`;
        if (d.name) {
            html += `Nome: ${d.name}<br>`;
        }
        if (d.phone) {
            html += `WhatsApp: ${d.phone}<br>`;
        }
        if (d.discount !== undefined) {
            html += `Desconto Acumulado: ${d.discount}%<br>`;
        }
        html += `Tipo: ${type}<br>` +
               `Profundidade: ${d.depth}<br>` +
               `Plataforma: ${d.platform || '—'}<br>` +
               `Criado em: ${d.created_at || '—'}`;
        return html;
    }

    function drag(simulation) {
        return d3.drag()
            .on('start', (event, d) => {
                if (!event.active) simulation.alphaTarget(0.3).restart();
                d.fx = d.x;
                d.fy = d.y;
            })
            .on('drag', (event, d) => {
                d.fx = event.x;
                d.fy = event.y;
            })
            .on('end', (event, d) => {
                if (!event.active) simulation.alphaTarget(0);
                d.fx = null;
                d.fy = null;
            });
    }

    // ── Resize ──────────────────────────────────────────────────────
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            const w = container.clientWidth;
            const h = Math.max(container.clientHeight, 600);
            svg.attr('width', w).attr('height', h);
        }, 200);
    });
}
