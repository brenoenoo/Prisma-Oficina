<?php
session_start();
require_once __DIR__ . "/../../Crud/crud.php";

if (empty($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if ($nome === '') {
        $erro = 'Informe o nome do funcionário.';
    } elseif ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um e-mail válido.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif ($senha !== $confirmarSenha) {
        $erro = 'As senhas não coincidem.';
    } else {
        $stmt = $pdo->prepare("
            SELECT id_usuario
            FROM usuario
            WHERE email = ?
            LIMIT 1
        ");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            $senhaHash = $senha;
            $stmt = $pdo->prepare("
                INSERT INTO usuario (nome, email, senha, tipo, status)
                VALUES (?, ?, ?, 'funcionario', 'ativo')
            ");
            $stmt->execute([$nome, $email, $senhaHash]);

            header('Location: configuracoes.php?funcionario=sucesso');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Funcionário - Prisma Mecânica</title>
    <link rel="stylesheet" href="../css/cadastrar-funcionario.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

<div class="layout">
    <?php require_once __DIR__ . "/../partials/sidebar.php"; ?>

    <main class="main-content">
        <?php require_once __DIR__ . "/../partials/header.php"; ?>

        <div class="config-container">
            <div class="page-header">
                <div>
                    <h1>Adicionar Funcionário</h1>
                    <p>Cadastre um novo funcionário para acessar o sistema.</p>
                </div>
                <a href="configuracoes.php" class="btn-voltar">
                    <i class="fa-solid fa-arrow-left"></i>
                    Voltar
                </a>
            </div>

            <?php if ($erro): ?>
                <div class="alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>

            <div class="form-card">
                <div class="form-card-header">
                    <div class="icon-box">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <div>
                        <h2>Dados do funcionário</h2>
                        <p>Preencha os dados abaixo para criar o acesso.</p>
                    </div>
                </div>

                <form method="POST">
                    <div class="form-grid">
                        <div class="form-group full">
                            <label for="nome">Nome completo *</label>
                            <div class="input-box">
                                <i class="fa-solid fa-user"></i>
                                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" placeholder="Digite o nome completo" required>
                            </div>
                        </div>

                        <div class="form-group full">
                            <label for="email">E-mail *</label>
                            <div class="input-box">
                                <i class="fa-solid fa-envelope"></i>
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="funcionario@email.com" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="senha">Senha *</label>
                            <div class="input-box">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" id="senha" name="senha" placeholder="Mínimo 6 caracteres" minlength="6" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="confirmar_senha">Confirmar senha *</label>
                            <div class="input-box">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Digite a senha novamente" minlength="6" required>
                            </div>
                        </div>
                    </div>

                    <div class="access-info">
                        <i class="fa-solid fa-circle-info"></i>
                        <div>
                            <strong>Permissão do usuário</strong>
                            <p>Este cadastro será criado automaticamente como <strong>Funcionário</strong>.</p>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="configuracoes.php" class="btn-cancelar">Cancelar</a>
                        <button type="submit" class="btn-primary">
                            <i class="fa-solid fa-user-plus"></i>
                            Cadastrar funcionário
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

</body>
</html>