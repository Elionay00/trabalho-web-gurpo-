<?php
require_once 'conexao.php';
$mensagem = "";
$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailInput = trim($_POST['email']);
    $novaSenha = trim($_POST['nova_senha']);

    if (!empty($emailInput) && !empty($novaSenha)) {
        // Verifica se o email realmente existe na tabela de usuários
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = :e");
        $stmt->execute(['e' => $emailInput]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Atualiza a senha diretamente para o texto digitado
            $stmtUpdate = $conn->prepare("UPDATE usuarios SET senha = :s WHERE email = :e");
            $stmtUpdate->execute(['s' => $novaSenha, 'e' => $emailInput]);
            
            $mensagem = "Senha redefinida com sucesso para o usuário: <b>" . htmlspecialchars($user['usuario']) . "</b>! Você já pode logar.";
        } else {
            $erro = "O e-mail digitado não foi encontrado no nosso sistema.";
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
    <title>Recuperar Senha - Game da Sala</title>
    <link rel="stylesheet" href="Css/login.css">
</head>
<body>

    <div class="login-container">
        <div class="login-card">
            <h2>🔑 RECUPERAR ACESSO</h2>
            <p class="subtitle">Digite seu e-mail cadastrado para escolher uma nova senha</p>

            <?php if (!empty($erro)): ?>
                <div style="color: #dc3545; text-align: center; margin-bottom: 15px; font-weight: bold; font-size: 14px;"><?= $erro; ?></div>
            <?php endif; ?>

            <?php if (!empty($mensagem)): ?>
                <div style="color: #28a745; text-align: center; margin-bottom: 15px; font-weight: bold; font-size: 14px;"><?= $mensagem; ?></div>
            <?php endif; ?>

            <form action="recuperar.php" method="POST">
                <div class="input-group">
                    <label for="email">DIGITE SEU E-MAIL:</label>
                    <input type="email" name="email" id="email" placeholder="Seu e-mail da Estácio" required>
                </div>

                <div class="input-group">
                    <label for="nova_senha">DIGITE A NOVA SENHA:</label>
                    <input type="password" name="nova_senha" id="nova_senha" placeholder="Nova senha de acesso" required>
                </div>

                <button type="submit" class="btn-entrar">REDEFINIR SENHA</button>
            </form>

            <div class="links">
                <a href="login.php">← Cancelar e Voltar</a>
            </div>
        </div>
    </div>

</body>
</html>