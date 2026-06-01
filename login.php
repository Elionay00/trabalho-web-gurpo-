<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexao.php';
$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioInput = trim($_POST['usuario']);
    $senhaInput = trim($_POST['senha']);

    if (!empty($usuarioInput) && !empty($senhaInput)) {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = :u OR email = :u");
        $stmt->execute(['u' => $usuarioInput]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // COMPARAÇÃO DIRETA DE TEXTO: Evita falhas de criptografia do MySQL no ambiente local
        if ($user && $senhaInput === $user['senha']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['usuario'];
            header("Location: vitrine.php");
            exit;
        } else {
            $erro = "Usuário ou senha inválidos.";
        }
    } else {
        $erro = "Por favor, preencha todos os campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Game da Sala Estácio</title>
    <link rel="stylesheet" href="Css/login.css">
</head>
<body>

    <div class="login-container">
        <div class="login-card">
            <h2>🎮 GAME DA SALA</h2>
            <p class="subtitle">Faça login para ver as ofertas da Estácio</p>

            <?php if (!empty($erro)): ?>
                <div style="color: #dc3545; text-align: center; margin-bottom: 15px; font-weight: bold; font-size: 14px;">
                    <?= $erro; ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="input-group">
                    <label for="usuario">USUÁRIO:</label>
                    <input type="text" name="usuario" id="usuario" placeholder="Seu e-mail ou usuário" required>
                </div>
                
                <div class="input-group">
                    <label for="senha">SENHA:</label>
                    <input type="password" name="senha" id="senha" placeholder="******" required>
                </div>

                <button type="submit" class="btn-entrar">ENTRAR NO GAME</button>
            </form>

           <div class="links">
                <a href="recuperar.php">Esqueci a senha</a> 
                <span class="separator">|</span>
                <a href="cadastro.php" style="font-weight: bold; color: #28a745;">Cadastre-se</a>
            </div>
        </div>
    </div>

</body>
</html>