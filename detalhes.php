<?php
require_once 'conexao.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

<?php
require_once 'conexao.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $conn->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) { die("Produto não encontrado."); }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes - <?= htmlspecialchars($produto['nome']); ?></title>
    <link rel="stylesheet" href="Css/vitrine.css">
</head>
<body>
    <div class="header">
        <h1>GAME DA SALA - ESTÁCIO</h1>
        <div>
            <a href="vitrine.php">VOLTAR</a>
            <a href="carrinho.php">🛒 CARRINHO</a>
        </div>
    </div>

    <div class="container">
        <div style="margin-bottom: 20px;">
            <a href="vitrine.php" style="text-decoration: none; font-weight: bold; color: #6c757d; font-size: 14px;">← VOLTAR PARA O CATÁLOGO</a>
        </div>

        <div class="product-detail" style="display: flex; gap: 30px; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
            <div class="product-image" style="flex: 1; text-align: center;">
                <img src="<?= $produto['imagem']; ?>" style="max-width: 250px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
            </div>
            <div class="product-info" style="flex: 2;">
                <h2><?= htmlspecialchars($produto['nome']); ?></h2>
                <p style="margin: 10px 0;"><b>Plataforma:</b> <?= htmlspecialchars($produto['plataforma']); ?></p>
                <p style="margin: 10px 0;"><b>Gênero:</b> <?= htmlspecialchars($produto['genero']); ?></p>
                <div class="game-price" style="font-size: 28px; color: #28a745; font-weight: bold; margin: 15px 0;">R$ <?= number_format($produto['preco'], 2, ',', '.'); ?></div>
                <p class="description" style="color: #666; line-height: 1.6; margin-bottom: 20px;"><?= htmlspecialchars($produto['descricao']); ?></p>
                
                <div style="display: flex; gap: 10px;">
                    <button class="btn-detalhes" style="background: #28a745; margin:0;" onclick="adicionarAoCarrinho(<?= $produto['id']; ?>, '<?= htmlspecialchars($produto['nome']); ?>', <?= $produto['preco']; ?>, '<?= $produto['imagem']; ?>', '<?= $produto['codigo']; ?>', false)">➕ ADICIONAR AO CARRINHO</button>
                    <button class="btn-comprar" style="margin:0;" onclick="adicionarAoCarrinho(<?= $produto['id']; ?>, '<?= htmlspecialchars($produto['nome']); ?>', <?= $produto['preco']; ?>, '<?= $produto['imagem']; ?>', '<?= $produto['codigo']; ?>', true)">🛒 COMPRAR AGORA</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function adicionarAoCarrinho(id, nome, preco, imagem, codigo, redirecionar) {
            let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
            let item = carrinho.find(i => i.id === id);
            
            if (item) { 
                item.quantidade += 1; 
            } else { 
                carrinho.push({ id, nome, preco, imagem, codigo, quantity: 1, quantidade: 1 }); 
            }
            
            localStorage.setItem('carrinho', JSON.stringify(carrinho));
            
            if (redirecionar) {
                window.location.href = 'carrinho.php';
            } else {
                alert(nome + " adicionado ao seu carrinho!");
            }
        }
    </script>
</body>
</html>