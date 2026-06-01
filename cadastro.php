<?php
require_once 'conexao.php';
$mensagem = "";
$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novoUsuario = trim($_POST['usuario']);
    $novoEmail = trim($_POST['email']);
    $novaSenha = trim($_POST['senha']);

    if (!empty($novoUsuario) && !empty($novoEmail) && !empty($novaSenha)) {
        try {
            // Verifica se o usuário ou email já existem
            $stmtCheck = $conn->prepare("SELECT id FROM usuarios WHERE usuario = :u OR email = :e");
            $stmtCheck->execute(['u' => $novoUsuario, 'e' => $novoEmail]);
            
            if ($stmtCheck->rowCount() > 0) {
                $erro = "Nome de usuário ou e-mail já estão cadastrados!";
            } else {
                // Insere o novo usuário (Como simplificamos o login, salvamos em texto limpo para casar com seu login atual)
                $stmtInsert = $conn->prepare("INSERT INTO usuarios (usuario, email, senha) VALUES (:u, :e, :s)");
                $stmtInsert->execute([
                    'u' => $novoUsuario,
                    'e' => $novoEmail,
                    's' => $novaSenha
                ]);
                $mensagem = "Cadastro realizado com sucesso! Você já pode fazer login.";
            }
        } catch (PDOException $e) {
            $erro = "Erro ao cadastrar: " . $e->getMessage();
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
    <title>Cadastre-se - Game da Sala</title>
    <link rel="stylesheet" href="Css/login.css">
</head>
<body>

    <div class="login-container">
        <div class="login-card">
            <h2>🎮 CRIAR CONTA</h2>
            <p class="subtitle">Cadastre-se para ver as ofertas da Estácio</p>

            <?php if (!empty($erro)): ?>
                <div style="color: #dc3545; text-align: center; margin-bottom: 15px; font-weight: bold; font-size: 14px;"><?= $erro; ?></div>
            <?php endif; ?>

            <?php if (!empty($mensagem)): ?>
                <div style="color: #28a745; text-align: center; margin-bottom: 15px; font-weight: bold; font-size: 14px;"><?= $mensagem; ?></div>
            <?php endif; ?>

            <form action="cadastro.php" method="POST">
                <div class="input-group">
                    <label for="usuario">CRIE UM USUÁRIO:</label>
                    <input type="text" name="usuario" id="usuario" placeholder="Ex: joao123" required>
                </div>

                <div class="input-group">
                    <label for="email">SEU E-MAIL:</label>
                    <input type="email" name="email" id="email" placeholder="joao@estacio.br" required>
                </div>
                
                <div class="input-group">
                    <label for="senha">CRIE UMA SENHA:</label>
                    <input type="password" name="senha" id="senha" placeholder="******" required>
                </div>

                <button type="submit" class="btn-entrar" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%);">FINALIZAR CADASTRO</button>
            </form>

            <div class="links">
                <a href="login.php" style="font-weight: bold;">← Voltar para o Login</a>
            </div>
        </div>
    </div>

</body>
</html>