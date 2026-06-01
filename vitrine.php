<?php
require_once 'conexao.php'; // O conexao.php já inicia a sessão com session_start()

// Se a variável de sessão não existir, significa que não houve login
if (!isset($_SESSION['user_id'])) {
    // Redireciona imediatamente para a tela de login
    header("Location: login.php");
    exit; // Para a execução do script para não carregar o HTML de baixo
}
?>
<?php
require_once 'conexao.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$stmt = $conn->query("SELECT * FROM produtos");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Vitrine Gamer - Estácio</title>
    <link rel="stylesheet" href="Css/vitrine.css">
</head>
<body>
    <div class="header">
        <h1>🎮 GAME DA SALA - ESTÁCIO</h1>
        <div class="nav">
            <span style="margin-right:15px;">Olá, <b><?= htmlspecialchars($_SESSION['user_name']); ?></b></span>
            <a href="carrinho.php">🛒 MEU CARRINHO (<span id="contador-carrinho">0</span>)</a>
            <a href="logout.php" style="color:#ffc107;">SAIR</a>
        </div>
    </div>

    <div class="container">
        <div style="margin-bottom: 20px;">
            <a href="logout.php" style="text-decoration: none; font-weight: bold; color: #6c757d; font-size: 14px;">← VOLTAR AO LOGIN</a>
        </div>

        <div class="page-title">🔥 CATÁLOGO DE JOGOS</div>
        <div class="page-subtitle"><i>Os jogos mais buscados do momento!</i></div>

        <div class="games-grid">
            <?php foreach($produtos as $p): ?>
                <div class="game-card">
                    <img src="<?= $p['imagem']; ?>" alt="<?= $p['nome']; ?>">
                    <h3><?= htmlspecialchars($p['nome']); ?></h3>
                    <div class="game-price">R$ <?= number_format($p['preco'], 2, ',', '.'); ?></div>
                    
                    <div class="btn-group-grid" style="display: flex; flex-direction: column; gap: 8px; align-items: center;">
                        <div style="display: flex; gap: 8px; justify-content: center; width: 100%;">
                            <a href="detalhes.php?id=<?= $p['id']; ?>" class="btn-detalhes" style="margin:0; width: 48%; text-align:center;">📋 DETALHES</a>
                            <button class="btn-detalhes" style="background: #28a745; margin:0; width: 48%;" onclick="adicionarAoCarrinho(<?= $p['id']; ?>, '<?= htmlspecialchars($p['nome']); ?>', <?= $p['preco']; ?>, '<?= $p['imagem']; ?>', '<?= $p['codigo']; ?>', false)">➕ CARRINHO</button>
                        </div>
                        <button class="btn-comprar" style="margin:0; width: 98%; text-align:center;" onclick="adicionarAoCarrinho(<?= $p['id']; ?>, '<?= htmlspecialchars($p['nome']); ?>', <?= $p['preco']; ?>, '<?= $p['imagem']; ?>', '<?= $p['codigo']; ?>', true)">🛒 COMPRAR AGORA</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function obterCarrinho() {
            return JSON.parse(localStorage.getItem('carrinho')) || [];
        }

        function salvarCarrinho(carrinho) {
            localStorage.setItem('carrinho', JSON.stringify(carrinho));
            atualizarContador();
        }

        // Modificado para aceitar o parâmetro 'redirecionar'
        function adicionarAoCarrinho(id, nome, preco, imagem, codigo, redirecionar) {
            let carrinho = obterCarrinho();
            let produtoExistente = carrinho.find(item => item.id === id);

            if (produtoExistente) {
                produtoExistente.quantidade += 1;
            } else {
                carrinho.push({ id, nome, preco, imagem, codigo, quantidade: 1 });
            }

            salvarCarrinho(carrinho);
            
            if (redirecionar) {
                window.location.href = 'carrinho.php';
            } else {
                alert(`${nome} adicionado ao carrinho com sucesso!`);
            }
        }

        function atualizarContador() {
            let carrinho = obterCarrinho();
            let totalItens = carrinho.reduce((soma, item) => soma + item.quantidade, 0);
            document.getElementById('contador-carrinho').innerText = totalItens;
        }

        atualizarContador();
    </script>
</body>
</html>