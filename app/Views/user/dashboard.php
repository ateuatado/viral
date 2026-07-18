<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Painel de Descontos — <?= esc($campaign['name'] ?? 'Campanha') ?></title>
    
    <!-- Bootstrap 5.3.3 CSS -->
    <link rel="stylesheet" href="/assets/vendor/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="/assets/vendor/css/bootstrap-icons.min.css">
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-color: #0b0f19;
            --card-bg: rgba(17, 24, 39, 0.7);
            --border-color: rgba(255, 255, 255, 0.08);
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --primary-glow: rgba(99, 102, 241, 0.15);
            --accent-color: #6366f1;
            --success-color: #22c55e;
            --whatsapp-color: #25d366;
        }

        body {
            background-color: var(--bg-color);
            background-image: radial-gradient(circle at 50% 0%, var(--primary-glow) 0%, transparent 60%);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Glassmorphism Cards */
        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .stat-card {
            padding: 1.5rem;
            text-align: center;
        }

        .stat-number {
            font-size: 2.25rem;
            font-weight: 800;
            margin-bottom: 0.25rem;
            background: linear-gradient(135deg, #fff 0%, #a5b4fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Coupon Progress */
        .coupon-box {
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .coupon-discount {
            font-size: 4rem;
            font-weight: 900;
            line-height: 1;
            color: var(--success-color);
            text-shadow: 0 0 30px rgba(34, 197, 94, 0.3);
            margin-bottom: 0.5rem;
        }

        .progress-bar-custom {
            height: 10px;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 5px;
            overflow: hidden;
            margin: 1.5rem 0;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--accent-color) 0%, var(--success-color) 100%);
            border-radius: 5px;
            transition: width 0.8s ease;
        }

        /* Copy & Share */
        .share-input-group {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            border-radius: .5rem;
            padding: .5rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .share-input-group input {
            background: transparent;
            border: none;
            color: var(--text-main);
            outline: none;
            width: 100%;
            font-size: .9rem;
        }

        .btn-whatsapp {
            background-color: var(--whatsapp-color);
            color: #fff;
            font-weight: 700;
            border: none;
            padding: .75rem 1.5rem;
            border-radius: .5rem;
            transition: all 0.2s ease;
        }
        .btn-whatsapp:hover {
            background-color: #20ba5a;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 211, 102, 0.3);
        }

        /* Graph container */
        .graph-wrapper {
            position: relative;
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            overflow: hidden;
            background: rgba(10, 15, 25, 0.8);
            height: 450px;
            width: 100%;
        }

        #graphContainer {
            width: 100%;
            height: 100%;
        }

        .graph-legend {
            position: absolute;
            bottom: 15px;
            left: 15px;
            background: rgba(15, 23, 42, 0.9);
            border: 1px solid var(--border-color);
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 11px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            z-index: 10;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        /* D3 Elements */
        .node circle {
            cursor: pointer;
            stroke-width: 2px;
            transition: r 0.2s ease;
        }
        .link {
            stroke: rgba(255, 255, 255, 0.08);
            stroke-width: 1.5px;
        }
        .node text {
            font-size: 11px;
            fill: #e2e8f0;
            font-weight: 500;
            text-anchor: middle;
            pointer-events: none;
        }

        /* Tooltip */
        .graph-tooltip {
            position: absolute;
            background: rgba(15, 23, 42, 0.95);
            border: 1px solid var(--border-color);
            color: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 11px;
            pointer-events: none;
            display: none;
            z-index: 100;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        /* Navbar Custom */
        .navbar-custom {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 0;
        }

        .toast-container-custom {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
        }

        /* Custom Form Controls for Profile */
        .form-control:focus {
            background-color: rgba(30, 41, 59, 0.9) !important;
            border-color: var(--accent-color) !important;
            box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25) !important;
            color: #fff !important;
        }
        .form-control {
            background-color: rgba(15, 23, 42, 0.6) !important;
            border: 1px solid var(--border-color) !important;
            color: #fff !important;
            border-radius: 0.5rem;
        }
        .form-control::placeholder {
            color: rgba(255,255,255,0.3) !important;
        }
        .text-muted {
            color: #94a3b8 !important;
        }
        .form-label {
            color: #cbd5e1 !important;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <!-- ── Navbar ─────────────────────────────────────────────────────── -->
    <nav class="navbar navbar-expand navbar-dark navbar-custom mb-5">
        <div class="container">
            <span class="navbar-brand d-flex align-items-center gap-2">
                <i class="bi bi-link-45deg text-primary fs-3"></i>
                <span class="fw-bold">Corrida de Cupons</span>
            </span>
            <div class="ms-auto d-flex align-items-center gap-3">
                <span class="text-muted d-none d-sm-inline">Olá, <strong class="text-white"><?= esc($propagator['name'] ?? 'Participante') ?></strong></span>
                <a href="/logout" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-box-arrow-right me-1"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <!-- ── Main Content ────────────────────────────────────────────────── -->
    <main class="container mb-5 flex-grow-1">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 bg-success text-white mb-4" role="alert" style="border-radius: 0.5rem;">
                <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 bg-danger text-white mb-4" role="alert" style="border-radius: 0.5rem;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Banner Informativo -->
        <div class="glass-card p-4 mb-4 d-flex flex-column flex-md-row align-items-center justify-content-between border border-primary border-opacity-25" style="background: linear-gradient(145deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.95) 100%);">
            <div>
                <h4 class="mb-2 text-white"><i class="bi bi-camera-fill me-2 text-primary"></i>Conheça o James Webb Studio</h4>
                <p class="text-muted mb-0" style="font-size: 14px;">Você está acumulando descontos exclusivos para usar em nossos ensaios e serviços fotográficos de alto padrão. Explore nosso portfólio e planeje seu próximo ensaio!</p>
            </div>
            <div class="mt-3 mt-md-0 ms-md-4 flex-shrink-0">
                <a href="https://jameswebbstudio.com.br" target="_blank" class="btn btn-primary px-4 py-2 fw-bold text-nowrap" style="border-radius: 50px; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);">
                    <i class="bi bi-globe me-2"></i> Acessar Site Oficial
                </a>
            </div>
        </div>

        <div class="row g-4">
            
            <!-- Coluna Esquerda: Estatísticas e Desconto -->
            <div class="col-lg-5">
                <div class="d-flex flex-column gap-4">
                    
                    <!-- Box de Desconto -->
                    <div class="glass-card coupon-box">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 mb-3">Seu Cupom</span>
                        <div class="coupon-discount"><?= $discount ?>% OFF</div>
                        <p class="text-muted mb-2">desconto acumulado na sua rede</p>
                        
                        <p class="text-secondary small mb-3" style="font-size: 11px; line-height: 1.4;">
                            ℹ️ <strong>Como funciona:</strong> Você inicia com 10% OFF. A cada amigo indicado que convidar outra pessoa (gerando profundidade na rede), o seu desconto sobe mais 10% (limite de 80%).
                        </p>
                        
                        <!-- Barra de progresso -->
                        <div class="progress-bar-custom">
                            <div class="progress-fill" style="width: <?= max($discount, 5) ?>%;"></div>
                        </div>
                        
                        <?php if ($discount < 80): ?>
                            <small class="text-muted d-block mt-2">
                                <i class="bi bi-info-circle me-1"></i> Indique mais amigos para subir para <strong><?= $discount + 10 ?>% OFF</strong>!
                            </small>
                        <?php else: ?>
                            <small class="text-success d-block mt-2 fw-semibold">
                                <i class="bi bi-patch-check-fill me-1"></i> Você atingiu o desconto máximo de 80% OFF! Parabéns!
                            </small>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Grid de Estatísticas -->
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="glass-card stat-card d-flex flex-column justify-content-between h-100 py-3">
                                <div>
                                    <div class="stat-number"><?= $clicks ?></div>
                                    <div class="stat-label">Cliques</div>
                                </div>
                                <p class="text-muted small mb-0 mt-2" style="font-size: 10px; line-height: 1.2;">
                                    Total de vezes que novos visitantes abriram o seu link.
                                </p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="glass-card stat-card d-flex flex-column justify-content-between h-100 py-3">
                                <div>
                                    <div class="stat-number text-success"><?= $conversions ?></div>
                                    <div class="stat-label">Indicações</div>
                                </div>
                                <p class="text-muted small mb-0 mt-2" style="font-size: 10px; line-height: 1.2;">
                                    Amigos que se cadastraram e ativaram a própria divulgação.
                                </p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="glass-card stat-card py-3">
                                <span class="text-muted fs-7 d-block">
                                    👁️ <strong><?= $visualizers ?></strong> amigo(s) visualizaram mas não se cadastraram ainda
                                </span>
                                <p class="text-muted small mb-0 mt-1" style="font-size: 10px; line-height: 1.2;">
                                    Visitantes que acessaram o link, mas ainda não concluíram o cadastro inicial no chat.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Caixa de Compartilhamento -->
                    <div class="glass-card p-4">
                        <h5 class="mb-3"><i class="bi bi-share me-2 text-primary"></i>Compartilhe e Ganhe</h5>
                        <p class="text-muted small mb-3">Envie seu link exclusivo para seus contatos. Cada nova indicação qualificada na sua cadeia de compartilhamento gera +10% de desconto para você!</p>
                        
                        <div class="share-input-group mb-3">
                            <input type="text" id="shareUrlInput" value="<?= esc($share_url) ?>" readonly>
                            <button class="btn btn-sm btn-outline-secondary" onclick="copyLink()">
                                <i class="bi bi-copy"></i> Copiar
                            </button>
                        </div>
                        
                        <?php
                            $whatsappMessage = "Olha que legal! Estou participando da Corrida de Cupons do Marco Santo e já ganhei {$discount}% de desconto! Cadastre-se pelo meu link para ganhar desconto você também e me ajudar a subir o meu: " . $share_url;
                            $whatsappUrl = "https://api.whatsapp.com/send?text=" . urlencode($whatsappMessage);
                        ?>
                        <a href="<?= $whatsappUrl ?>" target="_blank" class="btn btn-whatsapp w-100 text-center mb-2">
                            <i class="bi bi-whatsapp me-2"></i> Compartilhar no WhatsApp
                        </a>
                        <div class="text-center">
                            <span class="text-muted" style="font-size: 10px;">💡 <strong>Dica:</strong> Compartilhe em grupos de amigos ou redes sociais para acelerar seu ganho!</span>
                        </div>
                    </div>

                    <!-- Caixa de Perfil / Minha Conta -->
                    <div class="glass-card p-4">
                        <h5 class="mb-3"><i class="bi bi-person-bounding-box me-2 text-primary"></i>Meus Dados e Acesso</h5>
                        <p class="text-muted small">Mantenha seu cadastro atualizado para garantir o recebimento dos cupons e gerenciar seu acesso ao painel.</p>
                        
                        <form action="/user/profile/update" method="POST" id="profileForm">
                            <?= csrf_field() ?>
                            
                            <div class="mb-3">
                                <label for="profileName" class="form-label small text-muted mb-1">Nome Completo</label>
                                <input type="text" class="form-control" id="profileName" name="name" value="<?= esc($propagator['name'] ?? '') ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="profileEmail" class="form-label small text-muted mb-1">E-mail (Usado para Login)</label>
                                <input type="email" class="form-control" id="profileEmail" name="email" value="<?= esc($propagator['email'] ?? '') ?>" required>
                            </div>
                            
                            <div class="row mb-3 g-2">
                                <div class="col-6">
                                    <label for="profilePhone" class="form-label small text-muted mb-1">WhatsApp</label>
                                    <input type="text" class="form-control" id="profilePhone" name="phone" value="<?= esc($propagator['phone'] ?? '') ?>" placeholder="(99) 99999-9999" required>
                                </div>
                                <div class="col-6">
                                    <label for="profileInstagram" class="form-label small text-muted mb-1">Instagram (@usuario)</label>
                                    <input type="text" class="form-control" id="profileInstagram" name="instagram" value="<?= esc($propagator['instagram'] ?? '') ?>" placeholder="@seuinsta">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="profileCpf" class="form-label small text-muted mb-1">CPF (Para resgate de prêmios)</label>
                                <input type="text" class="form-control" id="profileCpf" name="cpf" value="<?= esc($propagator['cpf'] ?? '') ?>" placeholder="000.000.000-00">
                            </div>
                            
                            <hr class="border-secondary my-3">
                            
                            <div class="mb-3">
                                <label for="profilePassword" class="form-label small text-muted mb-1">Nova Senha (deixe em branco para manter a atual)</label>
                                <input type="password" class="form-control" id="profilePassword" name="password" placeholder="Mínimo 8 caracteres">
                            </div>
                            
                            <div class="mb-3">
                                <label for="profilePasswordConfirm" class="form-label small text-muted mb-1">Confirmar Nova Senha</label>
                                <input type="password" class="form-control" id="profilePasswordConfirm" name="password_confirm" placeholder="Repita a nova senha">
                            </div>
                            
                            <button type="submit" class="btn btn-outline-primary btn-sm w-100 fw-bold">
                                <i class="bi bi-save me-1"></i> Salvar Alterações
                            </button>
                        </form>
                    </div>
                    
                </div>
            </div>
            
            <!-- Coluna Direita: O Grafo de Rede do Usuário -->
            <div class="col-lg-7">
                <div class="glass-card p-4 h-100 d-flex flex-column">
                    <div class="mb-3">
                        <h4 class="mb-1"><i class="bi bi-diagram-2 me-2 text-primary"></i>Sua Rede de Propagação</h4>
                        <p class="text-muted mb-0 small">Este mapa interativo mostra em tempo real como o seu link está se espalhando. Você pode dar zoom, arrastar os círculos (nós) e posicionar o mouse/dedo sobre eles para ver detalhes.</p>
                    </div>
                    
                    <!-- Grafo Wrapper -->
                    <div class="graph-wrapper flex-grow-1">
                        <div id="graphContainer"></div>
                        <div class="graph-legend">
                            <div class="legend-item">
                                <div class="legend-dot" style="background-color: #6366f1;"></div>
                                <span><strong>Você:</strong> O início de toda a sua rede de descontos.</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-dot" style="background-color: #22c55e;"></div>
                                <span><strong>Indicação Ativa:</strong> Amigos que se cadastraram pelo seu convite.</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-dot" style="background-color: #3b82f6;"></div>
                                <span><strong>Apenas Visualizou:</strong> Amigos que abriram o link mas ainda não se cadastraram.</span>
                            </div>
                            <hr class="border-secondary my-1">
                            <div class="text-muted" style="font-size: 9px; line-height: 1.2;">
                                🔒 <strong>Privacidade Garantida:</strong> Por segurança, os nomes reais dos seus convidados são anonimizados.
                            </div>
                        </div>
                        <div class="graph-tooltip" id="graphTooltip"></div>
                    </div>
                </div>
            </div>
            
    </main>

    <!-- Footer de Transparência -->
    <footer class="container text-center py-4 mt-auto border-top border-secondary-subtle" style="max-width:800px; border-color: rgba(255,255,255,0.05) !important;">
        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.5;">
            Esta aplicação faz parte do <strong>2gotas</strong>, uma plataforma de pesquisa aberta destinada a analisar modelos inovadores de compartilhamento e espalhamento orgânico de conteúdo, com foco na distribuição amigável, sem técnicas insistentes, inconvenientes ou invasivas.
        </p>
    </footer>

    <!-- ── Toast de Feedback ───────────────────────────────────────────── -->
    <div class="toast-container-custom">
        <div id="clipboardToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-check-circle-fill me-2"></i> Link copiado para a área de transferência!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="/assets/vendor/js/bootstrap.bundle.min.js"></script>
    <!-- D3.js v7 -->
    <script src="/assets/vendor/js/d3.v7.min.js"></script>

    <script>
        // Copiar Link
        function copyLink() {
            const input = document.getElementById('shareUrlInput');
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value);
            
            const toastEl = document.getElementById('clipboardToast');
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }

        // Renderizador do Grafo D3
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('graphContainer');
            const width = container.clientWidth;
            const height = container.clientHeight;

            const svg = d3.select("#graphContainer")
                .append("svg")
                .attr("width", "100%")
                .attr("height", "100%")
                .attr("viewBox", `0 0 ${width} ${height}`)
                .call(d3.zoom().on("zoom", (event) => {
                    g.attr("transform", event.transform);
                }))
                .append("g");

            const g = svg.append("g");
            const tooltip = d3.select("#graphTooltip");

            // Carrega os dados da rede anonimizada do usuário logado
            d3.json('/user/api/network').then(data => {
                if (!data.nodes || data.nodes.length === 0) {
                    d3.select("#graphContainer")
                        .append("div")
                        .attr("class", "h-100 d-flex align-items-center justify-content-center text-muted")
                        .html("<div><i class='bi bi-diagram-2 fs-1 d-block text-center mb-2'></i>Nenhum disparo na sua rede ainda.</div>");
                    return;
                }

                // Cria simulação física
                const simulation = d3.forceSimulation(data.nodes)
                    .force("link", d3.forceLink(data.links).id(d => d.id).distance(80))
                    .force("charge", d3.forceManyBody().strength(-120))
                    .force("center", d3.forceCenter(width / 2, height / 2));

                // Desenha links
                const link = g.append("g")
                    .attr("class", "links")
                    .selectAll("line")
                    .data(data.links)
                    .enter().append("line")
                    .attr("class", "link");

                // Desenha nós
                const node = g.append("g")
                    .attr("class", "nodes")
                    .selectAll("g")
                    .data(data.nodes)
                    .enter().append("g")
                    .call(d3.drag()
                        .on("start", dragstarted)
                        .on("drag", dragged)
                        .on("end", dragended));

                // Círculos do nó
                node.append("circle")
                    .attr("r", d => d.is_me ? 20 : 12)
                    .attr("fill", d => {
                        if (d.is_me) return "#6366f1"; // Roxo (Você)
                        return d.viralized ? "#22c55e" : "#3b82f6"; // Verde (Ativo) ou Azul (Visualizou)
                    })
                    .attr("stroke", d => d.is_me ? "#a5b4fc" : "rgba(255,255,255,0.1)")
                    .attr("filter", d => d.is_me ? "drop-shadow(0 0 10px rgba(99, 102, 241, 0.5))" : "none");

                // Ícones ou letras simplificadas nos nós
                node.append("text")
                    .attr("dy", ".3em")
                    .text(d => d.is_me ? "Você" : "")
                    .style("font-size", "10px")
                    .style("font-weight", "bold");

                // Tooltips em D3
                node.on("mouseover", (event, d) => {
                    tooltip.style("display", "block")
                        .html(`
                            <div class="fw-bold">${d.name}</div>
                            ${!d.is_me ? `<div class="text-muted-custom">Status: ${d.viralized ? 'Cadastrou (Ativo)' : 'Apenas Visualizou'}</div>` : ''}
                            <div class="text-muted-custom">Desconto Calculado: ${d.discount}% OFF</div>
                            <div class="text-muted-custom">Visualizadores diretos: 👁️ ${d.visualizers}</div>
                        `);
                })
                .on("mousemove", (event) => {
                    const containerRect = container.getBoundingClientRect();
                    tooltip.style("left", (event.clientX - containerRect.left + 15) + "px")
                           .style("top", (event.clientY - containerRect.top - 15) + "px");
                })
                .on("mouseleave", () => {
                    tooltip.style("display", "none");
                });

                simulation.on("tick", () => {
                    link
                        .attr("x1", d => d.source.x)
                        .attr("y1", d => d.source.y)
                        .attr("x2", d => d.target.x)
                        .attr("y2", d => d.target.y);

                    node
                        .attr("transform", d => `translate(${d.x},${d.y})`);
                });

                function dragstarted(event, d) {
                    if (!event.active) simulation.alphaTarget(0.3).restart();
                    d.fx = d.x;
                    d.fy = d.y;
                }

                function dragged(event, d) {
                    d.fx = event.x;
                    d.fy = event.y;
                }

                function dragended(event, d) {
                    if (!event.active) simulation.alphaTarget(0);
                    d.fx = null;
                    d.fy = null;
                }
            });

            // Máscaras de Digitação
            const phoneInput = document.getElementById('profilePhone');
            const cpfInput = document.getElementById('profileCpf');

            if (phoneInput) {
                phoneInput.addEventListener('input', (e) => {
                    let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
                    e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
                });
            }

            if (cpfInput) {
                cpfInput.addEventListener('input', (e) => {
                    let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,3})(\d{0,2})/);
                    e.target.value = !x[2] ? x[1] : x[1] + '.' + x[2] + (x[3] ? '.' + x[3] : '') + (x[4] ? '-' + x[4] : '');
                });
            }
        });
    </script>
</body>
</html>
