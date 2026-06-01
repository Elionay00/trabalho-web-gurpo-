<?php
require_once 'conexao.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
?>

<?php
require_once 'conexao.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Carrinho - Game da Sala</title>
    <link rel="stylesheet" href="Css/carrinho.css">
</head>
<body>
    <div class="container">
    <div style="margin-bottom: 20px;">
        <a href="vitrine.php" style="text-decoration: none; font-weight: bold; color: #6c757d; font-size: 14px;">← VOLTAR PARA A VITRINE</a>
    </div>

    <h2 class="cart-title">Meu Carrinho</h2>

    <div class="header">
        <h1>🎮 GAME DA SALA - ESTÁCIO</h1>
        <nav><a href="vitrine.php">CONTINUAR COMPRANDO</a> | <a href="logout.php">SAIR</a></nav>
    </div>

    <div class="container">
        <h2 class="cart-title">Meu Carrinho</h2>

        <table class="cart-table" id="tabela-carrinho" width="100%">
            </table>

        <div id="carrinho-vazio" style="display:none; text-align:center; margin:30px 0;">
            <p>Seu carrinho está vazio atualmente.</p>
        </div>

        <div style="margin-top: 25px; text-align: right;" id="botoes-acoes">
            <a href="vitrine.php" class="btn-addmore">➕ ADICIONAR MAIS ITENS</a>
            <button class="btn-clear" onclick="limparCarrinho()">🗑️ LIMPAR CARRINHO</button>
            <button class="btn-checkout" onclick="finalizarCompra()">✅ FINALIZAR COMPRA</button>
        </div>

        <br>
        <div class="coupon-section" id="secao-cupom">
            <b>🎟️ CUPOM DE DESCONTO</b><br><br>
            <input type="text" id="input-cupom" placeholder="Digite seu cupom">
            <button class="btn-coupon" onclick="aplicarCupom()">Aplicar</button>
            <p id="msg-cupom" style="margin-top:10px; font-weight:bold;"></p>
            <br><small>Ex: GAMER10 (10%), ESTACIO15 (15%), WEB2026 (20%)</small>
        </div>
    </div>

    <script>
        let fatorDesconto = 0.0;

        function obterCarrinho() {
            return JSON.parse(localStorage.getItem('carrinho')) || [];
        }

        function renderizarCarrinho() {
            const carrinho = obterCarrinho();
            const tabela = document.getElementById('tabela-carrinho');
            const msgVazio = document.getElementById('carrinho-vazio');
            const acoes = document.getElementById('botoes-acoes');
            const cupomSecao = document.getElementById('secao-cupom');

            if (carrinho.length === 0) {
                tabela.innerHTML = "";
                msgVazio.style.display = "block";
                acoes.style.display = "none";
                cupomSecao.style.display = "none";
                return;
            }

            msgVazio.style.display = "none";
            acoes.style.display = "block";
            cupomSecao.style.display = "block";

            // Monta o cabeçalho da tabela
            let html = `
                <thead>
                    <tr>
                        <th>PRODUTO</th>
                        <th>PREÇO</th>
                        <th>QUANTIDADE</th>
                        <th>TOTAL</th>
                        <th>REMOVER</th>
                    </tr>
                </thead>
                <tbody>
            `;

            let subtotal = 0;

            // Percorre os elementos calculando os valores dinamicamente no cliente
            carrinho.forEach(item => {
                let totalItem = item.preco * item.quantidade;
                subtotal += totalItem;

                html += `
                    <tr>
                        <td class="product-cell">
                            <img src="${item.imagem}" width="50">
                            <div class="product-info">
                                <strong>${item.nome}</strong>
                                <small>Código: ${item.codigo}</small>
                            </div>
                        </td>
                        <td>R$ ${item.preco.toFixed(2).replace('.', ',')}</td>
                        <td>
                            <div class="quantity-control">
                                <button class="btn-icon" onclick="alterarQtd(${item.id}, -1)">-</button>
                                <input type="text" value="${item.quantidade}" size="3" style="text-align:center;" readonly>
                                <button class="btn-icon" onclick="alterarQtd(${item.id}, 1)">+</button>
                            </div>
                        </td>
                        <td><b>R$ ${totalItem.toFixed(2).replace('.', ',')}</b></td>
                        <td><button onclick="removerItem(${item.id})" style="background:none; border:none; cursor:pointer;">❌</button></td>
                    </tr>
                `;
            });

            // Cálculos matemáticos do total geral e descontos em JS
            let valorDesconto = subtotal * fatorDesconto;
            let totalGeral = subtotal - valorDesconto;

            html += `
                <tr class="summary-row">
                    <td colspan="3" style="text-align:right;">Subtotal:</td>
                    <td colspan="2">R$ ${subtotal.toFixed(2).replace('.', ',')}</td>
                </tr>
            `;

            if (valorDesconto > 0) {
                html += `
                    <tr class="summary-row" style="color:#dc3545;">
                        <td colspan="3" style="text-align:right;">Desconto:</td>
                        <td colspan="2">- R$ ${valorDesconto.toFixed(2).replace('.', ',')}</td>
                    </tr>
                `;
            }

            html += `
                <tr class="summary-row">
                    <td colspan="3" style="text-align:right;">TOTAL DO PEDIDO:</td>
                    <td colspan="2" style="color:#28a745; font-size:20px;">R$ ${totalGeral.toFixed(2).replace('.', ',')}</td>
                </tr>
                </tbody>
            `;

            tabela.innerHTML = html;
        }

        function alterarQtd(id, delta) {
            let carrinho = obterCarrinho();
            let item = carrinho.find(i => i.id === id);
            if (item) {
                item.quantidade += delta;
                if (item.quantidade < 1) item.quantidade = 1;
                localStorage.setItem('carrinho', JSON.stringify(carrinho));
                renderizarCarrinho();
            }
        }

        function removerItem(id) {
            let carrinho = obterCarrinho();
            carrinho = carrinho.filter(i => i.id !== id);
            localStorage.setItem('carrinho', JSON.stringify(carrinho));
            renderizarCarrinho();
        }

        function limparCarrinho() {
            localStorage.removeItem('carrinho');
            renderizarCarrinho();
        }

        function aplicarCupom() {
            const txt = document.getElementById('input-cupom').value.toUpperCase().trim();
            const msg = document.getElementById('msg-cupom');

            if (txt === "GAMER10") { fatorDesconto = 0.10; msg.style.color = "green"; msg.innerText = "Cupom de 10% aplicado!"; }
            else if (txt === "ESTACIO15") { fatorDesconto = 0.15; msg.style.color = "green"; msg.innerText = "Cupom de 15% aplicado!"; }
            else if (txt === "WEB2026") { fatorDesconto = 0.20; msg.style.color = "green"; msg.innerText = "Cupom de 20% aplicado!"; }
            else { fatorDesconto = 0.0; msg.style.color = "red"; msg.innerText = "Cupom inválido."; }
            
            renderizarCarrinho();
        }

        function finalizarCompra() {
            alert("Compra enviada com sucesso! Obrigado por comprar no Game da Sala.");
            limparCarrinho();
            window.location.href = 'vitrine.php';
        }

        // Execução inicial
        renderizarCarrinho();
    </script>
</body>
</html>