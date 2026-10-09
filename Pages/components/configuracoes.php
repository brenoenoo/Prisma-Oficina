<?php

session_start();

require_once __DIR__ . "/../../Crud/crud.php";

if (empty($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuarioLogado = $_SESSION['usuario'];

$mensagem = '';
$erro = '';

if (isset($_GET['funcionario']) && $_GET['funcionario'] === 'sucesso') {
    $mensagem = 'Funcionário cadastrado com sucesso.';
}

if (isset($_GET['atualizado']) && $_GET['atualizado'] === 'sucesso') {
    $mensagem = 'Configuração atualizada com sucesso.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $acao = $_POST['acao'] ?? '';

    if ($acao === 'alterar_status') {

        $idUsuario = (int) ($_POST['id_usuario'] ?? 0);
        $novoStatus = $_POST['status'] ?? '';

        if ($idUsuario <= 0 || !in_array($novoStatus, ['ativo', 'inativo'], true)) {
            $erro = 'Dados inválidos.';
        } elseif ($idUsuario === (int) $usuarioLogado['id_usuario']) {
            $erro = 'Você não pode alterar o próprio status.';
        } else {

            $stmt = $pdo->prepare("
                UPDATE usuario
                SET status = ?
                WHERE id_usuario = ?
                AND tipo = 'funcionario'
            ");

            $stmt->execute([
                $novoStatus,
                $idUsuario
            ]);

            $mensagem = 'Status do funcionário atualizado.';
        }
    }

    if ($acao === 'alterar_senha') {

        $idUsuario = (int) ($_POST['id_usuario'] ?? 0);
        $novaSenha = $_POST['nova_senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';

        if ($idUsuario <= 0) {

            $erro = 'Funcionário inválido.';

        } elseif (strlen($novaSenha) < 6) {

            $erro = 'A senha deve ter pelo menos 6 caracteres.';

        } elseif ($novaSenha !== $confirmarSenha) {

            $erro = 'As senhas não coincidem.';

        } else {

            $senhaHash = password_hash(
                $novaSenha,
                PASSWORD_DEFAULT
            );

            $stmt = $pdo->prepare("
                UPDATE usuario
                SET senha = ?
                WHERE id_usuario = ?
                AND tipo = 'funcionario'
            ");

            $stmt->execute([
                $senhaHash,
                $idUsuario
            ]);

            $mensagem = 'Senha alterada com sucesso.';
        }
    }

    if ($acao === 'dados_oficina') {

        $nome = trim($_POST['nome'] ?? '');
        $cnpj = trim($_POST['cnpj'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $email = trim($_POST['email_comercial'] ?? '');
        $endereco = trim($_POST['endereco'] ?? '');
        $cidade = trim($_POST['cidade'] ?? '');
        $estado = trim($_POST['estado'] ?? '');
        $cep = trim($_POST['cep'] ?? '');

        if ($nome === '') {

            $erro = 'Informe o nome da oficina.';

        } elseif (
            $email !== '' &&
            !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {

            $erro = 'Informe um e-mail comercial válido.';

        } else {

            $stmt = $pdo->query("
                SELECT id_config_loja
                FROM configuracoes_loja
                ORDER BY id_config_loja ASC
                LIMIT 1
            ");

            $configuracao = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($configuracao) {

                $stmt = $pdo->prepare("
                    UPDATE configuracoes_loja
                    SET
                        nome = ?,
                        cnpj = ?,
                        telefone = ?,
                        email_comercial = ?,
                        endereco = ?,
                        cidade = ?,
                        estado = ?,
                        cep = ?
                    WHERE id_config_loja = ?
                ");

                $stmt->execute([
                    $nome,
                    $cnpj,
                    $telefone,
                    $email,
                    $endereco,
                    $cidade,
                    $estado,
                    $cep,
                    $configuracao['id_config_loja']
                ]);

            } else {

                $stmt = $pdo->prepare("
                    INSERT INTO configuracoes_loja
                    (
                        nome,
                        cnpj,
                        telefone,
                        email_comercial,
                        endereco,
                        cidade,
                        estado,
                        cep
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");

                $stmt->execute([
                    $nome,
                    $cnpj,
                    $telefone,
                    $email,
                    $endereco,
                    $cidade,
                    $estado,
                    $cep
                ]);
            }

            $mensagem = 'Dados da oficina atualizados com sucesso.';
        }
    }
}

$stmt = $pdo->query("
    SELECT
        id_usuario,
        nome,
        email,
        tipo,
        status,
        ultimo_acesso
    FROM usuario
    ORDER BY
        tipo ASC,
        nome ASC
");

$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$funcionarios = array_filter(
    $usuarios,
    function ($usuario) {
        return $usuario['tipo'] === 'funcionario';
    }
);

$stmt = $pdo->query("
    SELECT *
    FROM configuracoes_loja
    ORDER BY id_config_loja ASC
    LIMIT 1
");

$oficina = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$oficina) {
    $oficina = [
        'nome' => '',
        'cnpj' => '',
        'telefone' => '',
        'email_comercial' => '',
        'endereco' => '',
        'cidade' => '',
        'estado' => '',
        'cep' => ''
    ];
}

$stmt = $pdo->query("
    SELECT
        id_categoria,
        nome
    FROM categoria
    ORDER BY nome ASC
");

$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Prisma - Configurações</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="../css/configuracoes.css?v=1"
    >

</head>

<body>

<?php require_once __DIR__ . "/../partials/sidebar.php"; ?>

<main class="main">

    <?php require_once __DIR__ . "/../partials/header.php"; ?>


    <section class="page-title">

        <div>

            <h1>Configurações</h1>

            <p>
                Gerencie as configurações da oficina e os usuários do sistema.
            </p>

        </div>

    </section>


    <?php if ($mensagem): ?>

        <div class="alert-success">

            <i class="fa-solid fa-circle-check"></i>

            <?= htmlspecialchars($mensagem) ?>

        </div>

    <?php endif; ?>


    <?php if ($erro): ?>

        <div class="alert-error">

            <i class="fa-solid fa-circle-exclamation"></i>

            <?= htmlspecialchars($erro) ?>

        </div>

    <?php endif; ?>


    <section class="settings-section">

        <div class="section-header">

            <div>

                <h2>
                    Funcionários
                </h2>

                <p>
                    Gerencie os usuários funcionários da oficina.
                </p>

            </div>

            <a
                href="cadastrar-funcionario.php"
                class="new-button"
            >

                <i class="fa-solid fa-user-plus"></i>

                Adicionar funcionário

            </a>

        </div>


        <div class="users-table">

            <div class="table-header">

                <span>Nome</span>

                <span>E-mail</span>

                <span>Status</span>

                <span>Ações</span>

            </div>


            <?php if (empty($funcionarios)): ?>

                <div class="empty-state">

                    <i class="fa-solid fa-users"></i>

                    <p>
                        Nenhum funcionário cadastrado.
                    </p>

                </div>

            <?php else: ?>


                <?php foreach ($funcionarios as $funcionario): ?>

                    <div class="table-row">

                        <div class="user-info">

                            <div class="user-icon">

                                <i class="fa-solid fa-user"></i>

                            </div>

                            <div>

                                <strong>
                                    <?= htmlspecialchars($funcionario['nome']) ?>
                                </strong>

                                <small>
                                    Funcionário
                                </small>

                            </div>

                        </div>


                        <span>

                            <?= htmlspecialchars($funcionario['email']) ?>

                        </span>


                        <span>

                            <?php if ($funcionario['status'] === 'ativo'): ?>

                                <span class="status active">
                                    Ativo
                                </span>

                            <?php else: ?>

                                <span class="status inactive">
                                    Inativo
                                </span>

                            <?php endif; ?>

                        </span>


                        <div class="actions">

                            <form method="POST">

                                <input
                                    type="hidden"
                                    name="acao"
                                    value="alterar_status"
                                >

                                <input
                                    type="hidden"
                                    name="id_usuario"
                                    value="<?= (int) $funcionario['id_usuario'] ?>"
                                >

                                <?php if ($funcionario['status'] === 'ativo'): ?>

                                    <input
                                        type="hidden"
                                        name="status"
                                        value="inativo"
                                    >

                                    <button
                                        type="submit"
                                        class="action-button danger"
                                        title="Desativar funcionário"
                                        onclick="return confirm('Deseja desativar este funcionário?')"
                                    >

                                        <i class="fa-solid fa-user-slash"></i>

                                    </button>

                                <?php else: ?>

                                    <input
                                        type="hidden"
                                        name="status"
                                        value="ativo"
                                    >

                                    <button
                                        type="submit"
                                        class="action-button success"
                                        title="Ativar funcionário"
                                    >

                                        <i class="fa-solid fa-user-check"></i>

                                    </button>

                                <?php endif; ?>

                            </form>


                            <button
                                type="button"
                                class="action-button"
                                title="Alterar senha"
                                onclick="abrirSenha(<?= (int) $funcionario['id_usuario'] ?>, '<?= htmlspecialchars($funcionario['nome'], ENT_QUOTES) ?>')"
                            >

                                <i class="fa-solid fa-key"></i>

                            </button>

                        </div>

                    </div>

                <?php endforeach; ?>


            <?php endif; ?>

        </div>

    </section>


    <section class="settings-section">

        <div class="section-header">

            <div>

                <h2>
                    Dados da oficina
                </h2>

                <p>
                    Atualize as informações principais da oficina.
                </p>

            </div>

        </div>


        <form
            method="POST"
            class="settings-form"
        >

            <input
                type="hidden"
                name="acao"
                value="dados_oficina"
            >


            <div class="form-grid">


                <div class="form-group full">

                    <label for="nome">
                        Nome da oficina
                    </label>

                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        value="<?= htmlspecialchars($oficina['nome']) ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="cnpj">
                        CNPJ
                    </label>

                    <input
                        type="text"
                        id="cnpj"
                        name="cnpj"
                        value="<?= htmlspecialchars($oficina['cnpj']) ?>"
                    >

                </div>


                <div class="form-group">

                    <label for="telefone">
                        Telefone
                    </label>

                    <input
                        type="text"
                        id="telefone"
                        name="telefone"
                        value="<?= htmlspecialchars($oficina['telefone']) ?>"
                    >

                </div>


                <div class="form-group">

                    <label for="email_comercial">
                        E-mail comercial
                    </label>

                    <input
                        type="email"
                        id="email_comercial"
                        name="email_comercial"
                        value="<?= htmlspecialchars($oficina['email_comercial']) ?>"
                    >

                </div>


                <div class="form-group">

                    <label for="cep">
                        CEP
                    </label>

                    <input
                        type="text"
                        id="cep"
                        name="cep"
                        value="<?= htmlspecialchars($oficina['cep']) ?>"
                    >

                </div>


                <div class="form-group full">

                    <label for="endereco">
                        Endereço
                    </label>

                    <input
                        type="text"
                        id="endereco"
                        name="endereco"
                        value="<?= htmlspecialchars($oficina['endereco']) ?>"
                    >

                </div>


                <div class="form-group">

                    <label for="cidade">
                        Cidade
                    </label>

                    <input
                        type="text"
                        id="cidade"
                        name="cidade"
                        value="<?= htmlspecialchars($oficina['cidade']) ?>"
                    >

                </div>


                <div class="form-group">

                    <label for="estado">
                        Estado
                    </label>

                    <select
                        id="estado"
                        name="estado"
                    >

                        <option value="">
                            Selecione
                        </option>

                        <?php

                        $estados = [
                            'AC' => 'Acre',
                            'AL' => 'Alagoas',
                            'AP' => 'Amapá',
                            'AM' => 'Amazonas',
                            'BA' => 'Bahia',
                            'CE' => 'Ceará',
                            'DF' => 'Distrito Federal',
                            'ES' => 'Espírito Santo',
                            'GO' => 'Goiás',
                            'MA' => 'Maranhão',
                            'MT' => 'Mato Grosso',
                            'MS' => 'Mato Grosso do Sul',
                            'MG' => 'Minas Gerais',
                            'PA' => 'Pará',
                            'PB' => 'Paraíba',
                            'PR' => 'Paraná',
                            'PE' => 'Pernambuco',
                            'PI' => 'Piauí',
                            'RJ' => 'Rio de Janeiro',
                            'RN' => 'Rio Grande do Norte',
                            'RS' => 'Rio Grande do Sul',
                            'RO' => 'Rondônia',
                            'RR' => 'Roraima',
                            'SC' => 'Santa Catarina',
                            'SP' => 'São Paulo',
                            'SE' => 'Sergipe',
                            'TO' => 'Tocantins'
                        ];

                        foreach ($estados as $sigla => $nomeEstado):

                        ?>

                            <option
                                value="<?= $sigla ?>"
                                <?= $oficina['estado'] === $sigla ? 'selected' : '' ?>
                            >
                                <?= $nomeEstado ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


            </div>


            <div class="form-actions">

                <button
                    type="submit"
                    class="new-button"
                >

                    <i class="fa-solid fa-floppy-disk"></i>

                    Salvar alterações

                </button>

            </div>

        </form>

    </section>


    <section class="settings-section">

        <div class="section-header">

            <div>

                <h2>
                    Categorias
                </h2>

                <p>
                    Categorias cadastradas para os produtos.
                </p>

            </div>

        </div>


        <div class="categories-list">

            <?php if (empty($categorias)): ?>

                <div class="empty-state">

                    <i class="fa-solid fa-folder-open"></i>

                    <p>
                        Nenhuma categoria cadastrada.
                    </p>

                </div>

            <?php else: ?>

                <?php foreach ($categorias as $categoria): ?>

                    <div class="category-item">

                        <div>

                            <i class="fa-solid fa-folder"></i>

                            <span>
                                <?= htmlspecialchars($categoria['nome']) ?>
                            </span>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

    </section>


</main>


<div
    id="modalSenha"
    class="modal"
>

    <div class="modal-content">

        <div class="modal-header">

            <div>

                <h2>
                    Alterar senha
                </h2>

                <p id="nomeFuncionario"></p>

            </div>

            <button
                type="button"
                onclick="fecharSenha()"
                class="modal-close"
            >

                <i class="fa-solid fa-xmark"></i>

            </button>

        </div>


        <form method="POST">

            <input
                type="hidden"
                name="acao"
                value="alterar_senha"
            >

            <input
                type="hidden"
                name="id_usuario"
                id="idUsuarioSenha"
            >


            <div class="form-group">

                <label for="nova_senha">
                    Nova senha
                </label>

                <input
                    type="password"
                    id="nova_senha"
                    name="nova_senha"
                    minlength="6"
                    required
                >

            </div>


            <div class="form-group">

                <label for="confirmar_senha">
                    Confirmar senha
                </label>

                <input
                    type="password"
                    id="confirmar_senha"
                    name="confirmar_senha"
                    minlength="6"
                    required
                >

            </div>


            <div class="form-actions">

                <button
                    type="button"
                    class="btn-cancelar"
                    onclick="fecharSenha()"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="new-button"
                >
                    Alterar senha
                </button>

            </div>

        </form>

    </div>

</div>


<script>

function abrirSenha(id, nome) {

    document.getElementById('modalSenha').classList.add('active');

    document.getElementById('idUsuarioSenha').value = id;

    document.getElementById('nomeFuncionario').textContent =
        'Funcionário: ' + nome;
}

function fecharSenha() {

    document.getElementById('modalSenha').classList.remove('active');

}

</script>

</body>

</html>