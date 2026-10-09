<?php

session_start();

require_once __DIR__ . "/../../Crud/crud.php";

if (empty($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$erro = '';
$idEdicao = (int) ($_GET['editar'] ?? 0);

$form = [
    'nome' => '',
    'telefone' => '',
    'email' => '',
    'id_usuario' => 0,
    'data' => '',
    'hora' => '',
    'status' => 'agendado',
    'observacao' => ''
];

if ($idEdicao > 0) {
    $stmt = $pdo->prepare("
        SELECT
            a.id_agendamento,
            a.id_usuario,
            a.data,
            a.hora,
            a.status,
            a.observacao,
            c.nome,
            c.telefone,
            c.email
        FROM agendamento a
        INNER JOIN cliente c ON c.id_cliente = a.id_cliente
        WHERE a.id_agendamento = ?
        LIMIT 1
    ");
    $stmt->execute([$idEdicao]);
    $agendamentoEdicao = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$agendamentoEdicao) {
        header('Location: agendamento.php');
        exit;
    }

    $form = [
        'nome' => $agendamentoEdicao['nome'],
        'telefone' => $agendamentoEdicao['telefone'],
        'email' => $agendamentoEdicao['email'] ?? '',
        'id_usuario' => (int) $agendamentoEdicao['id_usuario'],
        'data' => $agendamentoEdicao['data'],
        'hora' => substr($agendamentoEdicao['hora'], 0, 5),
        'status' => $agendamentoEdicao['status'],
        'observacao' => $agendamentoEdicao['observacao'] ?? ''
    ];
}

$clientes = $pdo->query("
    SELECT
        id_cliente,
        nome,
        telefone,
        email
    FROM cliente
    ORDER BY nome
")->fetchAll(PDO::FETCH_ASSOC);

$usuarios = $pdo->query("
    SELECT
        id_usuario,
        nome,
        tipo
    FROM usuario
    WHERE status = 'ativo'
    ORDER BY nome
")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? $form['nome']);
    $telefone = trim($_POST['telefone'] ?? $form['telefone']);
    $email = trim($_POST['email'] ?? $form['email']);
    $idUsuario = (int) ($_POST['id_usuario'] ?? $form['id_usuario']);
    $data = $_POST['data'] ?? $form['data'];
    $hora = $_POST['hora'] ?? $form['hora'];
    $observacao = trim($_POST['observacao'] ?? $form['observacao']);
    $status = $_POST['status'] ?? $form['status'];

    if ($nome === '') {
        $erro = 'Informe o nome do cliente.';
    } elseif ($telefone === '') {
        $erro = 'Informe o telefone do cliente.';
    } elseif ($idUsuario <= 0) {
        $erro = 'Selecione o responsável.';
    } elseif ($data === '') {
        $erro = 'Informe a data.';
    } elseif ($hora === '') {
        $erro = 'Informe o horário.';
    } elseif (!in_array($status, ['agendado', 'cancelado', 'concluido'], true)) {
        $erro = 'Status inválido.';
    } elseif ($data < date('Y-m-d')) {
        $erro = 'Não é possível cadastrar um agendamento para uma data passada.';
    } else {

        $dataObj = new DateTime($data);

        $dias = [
            1 => 'segunda-feira',
            2 => 'terça-feira',
            3 => 'quarta-feira',
            4 => 'quinta-feira',
            5 => 'sexta-feira',
            6 => 'sábado',
            7 => 'domingo'
        ];

        $diaSemana = $dias[(int) $dataObj->format('N')];

        $stmt = $pdo->prepare("
            SELECT
                id_horario,
                hora_abertura,
                hora_fechamento,
                ativo
            FROM horario_funcionamento
            WHERE LOWER(dia_semana) = LOWER(?)
            LIMIT 1
        ");

        $stmt->execute([$diaSemana]);

        $horarioFuncionamento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$horarioFuncionamento || !$horarioFuncionamento['ativo']) {

            $erro = 'A oficina não possui atendimento nesse dia.';

        } elseif (
            $hora < $horarioFuncionamento['hora_abertura'] ||
            $hora > $horarioFuncionamento['hora_fechamento']
        ) {

            $erro = 'O horário escolhido está fora do horário de funcionamento.';

        } else {

            $stmt = $pdo->prepare("
                SELECT id_cliente
                FROM cliente
                WHERE telefone = ?
                LIMIT 1
            ");

            $stmt->execute([$telefone]);

            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cliente) {

                $idCliente = $cliente['id_cliente'];

                $stmt = $pdo->prepare("
                    UPDATE cliente
                    SET nome = ?, email = ?
                    WHERE id_cliente = ?
                ");

                $stmt->execute([
                    $nome,
                    $email !== '' ? $email : null,
                    $idCliente
                ]);

            } else {

                $stmt = $pdo->prepare("
                    INSERT INTO cliente
                    (
                        nome,
                        telefone,
                        email
                    )
                    VALUES (?, ?, ?)
                ");

                $stmt->execute([
                    $nome,
                    $telefone,
                    $email !== '' ? $email : null
                ]);

                $idCliente = $pdo->lastInsertId();
            }

            $stmt = $pdo->prepare("
                SELECT id_agendamento
                FROM agendamento
                WHERE data = ?
                AND hora = ?
                AND status = 'agendado'
                AND id_agendamento <> ?
                LIMIT 1
            ");

            $stmt->execute([
                $data,
                $hora,
                $idEdicao
            ]);

            if ($stmt->fetch()) {

                $erro = 'Já existe um agendamento para esse horário.';

            } else {

                if ($idEdicao > 0) {
                    $stmt = $pdo->prepare("
                        SELECT id_cliente
                        FROM agendamento
                        WHERE id_agendamento = ?
                        LIMIT 1
                    ");
                    $stmt->execute([$idEdicao]);
                    $agendamentoAtual = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$agendamentoAtual) {
                        $erro = 'Agendamento não encontrado.';
                    } else {
                        $stmt = $pdo->prepare("
                            UPDATE agendamento
                            SET
                                id_cliente = ?,
                                id_usuario = ?,
                                id_horario_funcionamento = ?,
                                data = ?,
                                hora = ?,
                                status = ?,
                                observacao = ?
                            WHERE id_agendamento = ?
                        ");

                        $stmt->execute([
                            $idCliente,
                            $idUsuario,
                            $horarioFuncionamento['id_horario'],
                            $data,
                            $hora,
                            $status,
                            $observacao !== '' ? $observacao : null,
                            $idEdicao
                        ]);

                        header('Location: agendamento.php?editado=1');
                        exit;
                    }
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO agendamento
                        (
                            id_cliente,
                            id_usuario,
                            id_horario_funcionamento,
                            data,
                            hora,
                            status,
                            observacao
                        )
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");

                    $stmt->execute([
                        $idCliente,
                        $idUsuario,
                        $horarioFuncionamento['id_horario'],
                        $data,
                        $hora,
                        $status,
                        $observacao !== '' ? $observacao : null
                    ]);

                    header('Location: agendamento.php?sucesso=1');
                    exit;
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title><?= $idEdicao > 0 ? 'Editar Agendamento' : 'Cadastrar Agendamento' ?> - Prisma Mecânica</title>

    <link
        rel="stylesheet"
        href="../css/novo-agendamento.css?v=1"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>

<body>

<div class="layout">

    <?php require_once __DIR__ . "/../partials/sidebar.php"; ?>

    <main class="main-content">


        <div class="agendamento-container">

            <div class="page-header">

                <div>

                    <h1>
                        <?= $idEdicao > 0 ? 'Editar Agendamento' : 'Novo Agendamento' ?>
                    </h1>

                    <p>
                        Cadastre ou altere um atendimento da oficina.
                    </p>

                </div>

                <a
                    href="agendamento.php"
                    class="btn-voltar"
                >
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


            <form
                method="POST"
                class="form-card"
            >

                <div class="form-section">

                    <div class="section-title">

                        <i class="fa-solid fa-user"></i>

                        <div>

                            <h2>
                                Dados do cliente
                            </h2>

                            <p>
                                Informe os dados do cliente.
                            </p>

                        </div>

                    </div>


                    <div class="form-grid">

                        <div class="form-group full">

                            <label for="nome">
                                Nome completo *
                            </label>

                            <input
                                type="text"
                                id="nome"
                                name="nome"
                                value="<?= htmlspecialchars($_POST['nome'] ?? $form['nome']) ?>"
                                placeholder="Digite o nome do cliente"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label for="telefone">
                                Telefone *
                            </label>

                            <input
                                type="text"
                                id="telefone"
                                name="telefone"
                                value="<?= htmlspecialchars($_POST['telefone'] ?? $form['telefone']) ?>"
                                placeholder="(00) 00000-0000"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label for="email">
                                E-mail
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="<?= htmlspecialchars($_POST['email'] ?? $form['email']) ?>"
                                placeholder="cliente@email.com"
                            >

                        </div>

                    </div>

                </div>


                <div class="form-section">

                    <div class="section-title">

                        <i class="fa-solid fa-calendar-days"></i>

                        <div>

                            <h2>
                                Dados do agendamento
                            </h2>

                            <p>
                                Escolha o responsável, data e horário.
                            </p>

                        </div>

                    </div>


                    <div class="form-grid">

                        <div class="form-group">

                            <label for="id_usuario">
                                Responsável *
                            </label>

                            <select
                                id="id_usuario"
                                name="id_usuario"
                                required
                            >

                                <option value="">
                                    Selecione o responsável
                                </option>

                                <?php foreach ($usuarios as $usuario): ?>

                                    <option
                                        value="<?= (int) $usuario['id_usuario'] ?>"
                                        <?= ((int) ($_POST['id_usuario'] ?? $form['id_usuario']) === (int) $usuario['id_usuario']) ? 'selected' : '' ?>
                                    >

                                        <?= htmlspecialchars($usuario['nome']) ?>

                                        -
                                        <?= htmlspecialchars(ucfirst($usuario['tipo'])) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="form-group">

                            <label for="data">
                                Data *
                            </label>

                            <input
                                type="date"
                                id="data"
                                name="data"
                                min="<?= $idEdicao > 0 ? '' : date('Y-m-d') ?>"
                                value="<?= htmlspecialchars($_POST['data'] ?? '') ?>"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label for="hora">
                                Horário *
                            </label>

                            <input
                                type="time"
                                id="hora"
                                name="hora"
                                value="<?= htmlspecialchars($_POST['hora'] ?? $form['hora']) ?>"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label for="status">
                                Status
                            </label>

                            <select
                                id="status"
                                name="status"
                            >

                                <option
                                    value="agendado"
                                    <?= ($_POST['status'] ?? $form['status']) === 'agendado' ? 'selected' : '' ?>
                                >
                                    Agendado
                                </option>

                                <option
                                    value="cancelado"
                                    <?= ($_POST['status'] ?? $form['status']) === 'cancelado' ? 'selected' : '' ?>
                                >
                                    Cancelado
                                </option>

                                <option
                                    value="concluido"
                                    <?= ($_POST['status'] ?? $form['status']) === 'concluido' ? 'selected' : '' ?>
                                >
                                    Concluído
                                </option>

                            </select>

                        </div>


                        <div class="form-group full">

                            <label for="observacao">
                                Observações
                            </label>

                            <textarea
                                id="observacao"
                                name="observacao"
                                rows="5"
                                placeholder="Digite alguma observação sobre o agendamento..."
                            ><?= htmlspecialchars($_POST['observacao'] ?? $form['observacao']) ?></textarea>

                        </div>

                    </div>

                </div>


                <div class="form-actions">

                    <a
                        href="agendamento.php"
                        class="btn-cancelar"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="btn-salvar"
                    >

                        <i class="fa-solid fa-calendar-check"></i>

                        Cadastrar agendamento

                    </button>

                </div>

            </form>

        </div>

    </main>

</div>

</body>
</html>