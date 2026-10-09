<?php

session_start();

require_once "../../Crud/crud.php";

if (empty($_SESSION['usuario'])) {
    header("Location: ../login/login.php");
    exit;
}

$idUsuario = (int)($_SESSION['usuario']['id_usuario'] ?? 0);

if ($idUsuario <= 0) {
    header("Location: ../login/login.php");
    exit;
}

$mensagem = $_SESSION['mensagem'] ?? '';
$erro = $_SESSION['erro'] ?? '';

unset($_SESSION['mensagem']);
unset($_SESSION['erro']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $senhaAtual = trim($_POST['senha_atual'] ?? '');
    $novaSenha = trim($_POST['nova_senha'] ?? '');
    $confirmarSenha = trim($_POST['confirmar_senha'] ?? '');

    if ($senhaAtual === '' || $novaSenha === '' || $confirmarSenha === '') {
        $_SESSION['erro'] = 'Preencha todos os campos.';
        header("Location: seguranca.php");
        exit;
    }

    if ($novaSenha !== $confirmarSenha) {
        $_SESSION['erro'] = 'As novas senhas não são iguais.';
        header("Location: seguranca.php");
        exit;
    }

    if (strlen($novaSenha) < 4) {
        $_SESSION['erro'] = 'A nova senha deve ter pelo menos 4 caracteres.';
        header("Location: seguranca.php");
        exit;
    }

    $usuario = read(
        $pdo,
        'usuario',
        '*',
        'id_usuario = ' . $idUsuario
    );

    if (!$usuario) {
        $_SESSION['erro'] = 'Usuário não encontrado.';
        header("Location: seguranca.php");
        exit;
    }

    if ($senhaAtual !== $usuario['senha']) {
        $_SESSION['erro'] = 'A senha atual está incorreta.';
        header("Location: seguranca.php");
        exit;
    }

    $dataAlteracao = date('Y-m-d H:i:s');

    update(
        $pdo,
        'usuario',
        [
            'senha' => $novaSenha,
            'senha_alterada_em' => $dataAlteracao
        ],
        'id_usuario = ' . $idUsuario
    );

    $_SESSION['mensagem'] = 'Senha alterada com sucesso.';

    header("Location: seguranca.php");
    exit;
}

$usuario = read(
    $pdo,
    'usuario',
    '*',
    'id_usuario = ' . $idUsuario
);

if (!$usuario) {
    header("Location: ../login/login.php");
    exit;
}

function formatarData($data)
{
    if (empty($data)) {
        return 'Nenhum registro';
    }

    $dataObj = new DateTime($data);

    return $dataObj->format('d/m/Y') . ' às ' . $dataObj->format('H:i');
}

$ultimoLogin = $usuario['ultimo_acesso'] ?? null;
$senhaAlterada = $usuario['senha_alterada_em'] ?? null;

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Prisma - Segurança</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="../css/seguranca.css"
    >

</head>

<body>

<?php require_once __DIR__ . "/../partials/sidebar.php"; ?>

<main class="main">

<?php require_once __DIR__ . "/../partials/header.php"; ?>

<section class="page-title">

    <h1>Segurança</h1>

    <p>
        Gerencie a segurança e o acesso ao sistema.
    </p>

</section>

<?php if ($mensagem !== ''): ?>

    <div class="alert success">
        <i class="fa-solid fa-circle-check"></i>
        <?= htmlspecialchars($mensagem) ?>
    </div>

<?php endif; ?>

<?php if ($erro !== ''): ?>

    <div class="alert error">
        <i class="fa-solid fa-circle-exclamation"></i>
        <?= htmlspecialchars($erro) ?>
    </div>

<?php endif; ?>

<section class="security-container">

    <div class="security-card">

        <div class="card-title">

            <div class="title-icon">
                <i class="fa-solid fa-lock"></i>
            </div>

            <div>

                <h2>Alterar senha</h2>

                <p>
                    Atualize a senha da sua conta.
                </p>

            </div>

        </div>

        <form method="POST">

            <div class="form-group">

                <label for="senha_atual">
                    Senha atual
                </label>

                <div class="password-input">

                    <input
                        type="password"
                        id="senha_atual"
                        name="senha_atual"
                        placeholder="Digite sua senha atual"
                        required
                    >

                    <i
                        class="fa-regular fa-eye"
                        onclick="mostrarSenha('senha_atual', this)"
                    ></i>

                </div>

            </div>

            <div class="form-group">

                <label for="nova_senha">
                    Nova senha
                </label>

                <div class="password-input">

                    <input
                        type="password"
                        id="nova_senha"
                        name="nova_senha"
                        placeholder="Digite a nova senha"
                        required
                    >

                    <i
                        class="fa-regular fa-eye"
                        onclick="mostrarSenha('nova_senha', this)"
                    ></i>

                </div>

            </div>

            <div class="form-group">

                <label for="confirmar_senha">
                    Confirmar nova senha
                </label>

                <div class="password-input">

                    <input
                        type="password"
                        id="confirmar_senha"
                        name="confirmar_senha"
                        placeholder="Digite novamente a nova senha"
                        required
                    >

                    <i
                        class="fa-regular fa-eye"
                        onclick="mostrarSenha('confirmar_senha', this)"
                    ></i>

                </div>

            </div>

            <button
                type="submit"
                class="btn-save"
            >

                <i class="fa-solid fa-check"></i>

                Alterar senha

            </button>

        </form>

    </div>

    <div class="security-card">

        <div class="card-title">

            <div class="title-icon">
                <i class="fa-solid fa-desktop"></i>
            </div>

            <div>

                <h2>Sessão atual</h2>

                <p>
                    Informações da sua conta.
                </p>

            </div>

        </div>

        <div class="session">

            <div class="session-icon">
                <i class="fa-solid fa-user"></i>
            </div>

            <div class="session-info">

                <strong>
                    <?= htmlspecialchars($usuario['nome']) ?>
                </strong>

                <span>
                    <?= htmlspecialchars($usuario['email']) ?>
                </span>

            </div>

            <span class="current">
                Sessão atual
            </span>

        </div>

    </div>

    <div class="security-card">

        <div class="card-title">

            <div class="title-icon">
                <i class="fa-solid fa-list"></i>
            </div>

            <div>

                <h2>Atividade recente</h2>

                <p>
                    Últimas atividades realizadas na conta.
                </p>

            </div>

        </div>

        <div class="activity">

            <div class="activity-icon">
                <i class="fa-solid fa-right-to-bracket"></i>
            </div>

            <div>

                <strong>
                    Último login realizado
                </strong>

                <span>
                    <?= htmlspecialchars(formatarData($ultimoLogin)) ?>
                </span>

            </div>

        </div>

        <div class="activity">

            <div class="activity-icon">
                <i class="fa-solid fa-key"></i>
            </div>

            <div>

                <strong>
                    Senha alterada
                </strong>

                <span>
                    <?= htmlspecialchars(formatarData($senhaAlterada)) ?>
                </span>

            </div>

        </div>

    </div>

</section>

</main>

<script>

function mostrarSenha(id, icone) {

    const campo = document.getElementById(id);

    if (campo.type === 'password') {

        campo.type = 'text';

        icone.classList.remove('fa-eye');
        icone.classList.add('fa-eye-slash');

    } else {

        campo.type = 'password';

        icone.classList.remove('fa-eye-slash');
        icone.classList.add('fa-eye');

    }
}

</script>

</body>

</html>