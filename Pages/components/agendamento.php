<?php

session_start();

require_once "../../Crud/crud.php";


if (empty($_SESSION['usuario'])) {
    header("Location: ../login/login.php");
    exit;
}




$data = $_GET['data'] ?? date('Y-m-d');


$status = $_GET['status'] ?? '';


$busca = trim($_GET['busca'] ?? '');



$dataObjeto = DateTime::createFromFormat('Y-m-d', $data);

if (
    !$dataObjeto ||
    $dataObjeto->format('Y-m-d') !== $data
) {
    $data = date('Y-m-d');
}




$sql = "
    SELECT
        a.id_agendamento,
        a.id_cliente,
        a.id_usuario,
        a.id_horario_funcionamento,
        a.data,
        a.hora,
        a.status,
        a.observacao,

        c.nome AS cliente_nome,
        c.telefone AS cliente_telefone,

        u.nome AS usuario_nome

    FROM agendamento a

    INNER JOIN cliente c
        ON c.id_cliente = a.id_cliente

    INNER JOIN usuario u
        ON u.id_usuario = a.id_usuario

    WHERE a.data = ?
";

$params = [$data];




$statusPermitidos = [
    'agendado',
    'cancelado',
    'concluido'
];

if (
    $status !== '' &&
    in_array($status, $statusPermitidos, true)
) {

    $sql .= " AND a.status = ?";

    $params[] = $status;

}




if ($busca !== '') {

    $sql .= "
        AND (
            c.nome LIKE ?
            OR c.telefone LIKE ?
        )
    ";

    $termoBusca = '%' . $busca . '%';

    $params[] = $termoBusca;
    $params[] = $termoBusca;

}




$sql .= "
    ORDER BY a.hora ASC
";




$stmt = $pdo->prepare($sql);

$stmt->execute($params);

$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);



$dataFormatada = $dataObjeto->format('d/m/Y');

$meses = [
    1 => 'Janeiro',
    2 => 'Fevereiro',
    3 => 'Março',
    4 => 'Abril',
    5 => 'Maio',
    6 => 'Junho',
    7 => 'Julho',
    8 => 'Agosto',
    9 => 'Setembro',
    10 => 'Outubro',
    11 => 'Novembro',
    12 => 'Dezembro'
];

$dia = $dataObjeto->format('d');
$mes = $meses[(int) $dataObjeto->format('m')];
$ano = $dataObjeto->format('Y');

$dataTexto = $dia . ' de ' . $mes . ' de ' . $ano;



function nomeStatus($status)
{
    switch ($status) {

        case 'agendado':
            return 'Agendado';

        case 'concluido':
            return 'Concluído';

        case 'cancelado':
            return 'Cancelado';

        default:
            return ucfirst($status);
    }
}




function classeStatus($status)
{
    switch ($status) {

        case 'agendado':
            return 'scheduled';

        case 'concluido':
            return 'completed';

        case 'cancelado':
            return 'cancelled';

        default:
            return '';
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

    <title>Prisma - Agendamento</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="../css/agendamento.css"
    >

</head>


<body>


<?php require_once __DIR__ . "/../partials/sidebar.php"; ?>


<main class="main">


<?php require_once __DIR__ . "/../partials/header.php"; ?>

<section class="page-title">


    <div>

        <h1>Agendamento</h1>

        <p>
            Gerencie os serviços agendados da oficina.
        </p>

    </div>


   <a href="cadastrar-agendamento.php" class="btn-primary">
    <i class="fa-solid fa-plus"></i>
    Novo Agendamento
</a>


</section>




<section class="filters">



    <div class="filter">

        <label for="data">
            Data
        </label>

        <input
            type="date"
            id="data"
            name="data"
            value="<?= htmlspecialchars($data) ?>"
            form="filtros"
        >

    </div>



    <div class="filter">

        <label for="status">
            Status
        </label>

        <select
            id="status"
            name="status"
            form="filtros"
        >

            <option value="">
                Todos
            </option>

            <option
                value="agendado"
                <?= $status === 'agendado' ? 'selected' : '' ?>
            >
                Agendado
            </option>

            <option
                value="concluido"
                <?= $status === 'concluido' ? 'selected' : '' ?>
            >
                Concluído
            </option>

            <option
                value="cancelado"
                <?= $status === 'cancelado' ? 'selected' : '' ?>
            >
                Cancelado
            </option>

        </select>

    </div>



    <div class="filter search-client">

        <label for="busca">
            Pesquisar cliente
        </label>


        <div>

            <i class="fa-solid fa-magnifying-glass"></i>

            <input
                type="text"
                id="busca"
                name="busca"
                placeholder="Nome ou telefone"
                value="<?= htmlspecialchars($busca) ?>"
                form="filtros"
            >

        </div>

    </div>



    <form
        id="filtros"
        method="GET"
        action="agendamento.php"
    ></form>


    <button
        type="submit"
        form="filtros"
        class="filter-button"
    >
        Filtrar
    </button>


</section>




<section class="appointments">


    <div class="section-header">


        <div>

            <h2>Agendamentos</h2>

            <p>
                Serviços programados para o dia selecionado.
            </p>

        </div>


        <span class="date">

            <?= htmlspecialchars($dataTexto) ?>

        </span>


    </div>


    <?php if (empty($agendamentos)): ?>



        <div class="appointment">


            <div class="time">

                <strong>
                    --
                </strong>

                <span>
                    --
                </span>

            </div>


            <div class="appointment-info">


                <div class="client-icon">

                    <i class="fa-solid fa-calendar-xmark"></i>

                </div>


                <div>

                    <strong>
                        Nenhum agendamento encontrado
                    </strong>

                    <span>
                        Não existem agendamentos para os filtros selecionados.
                    </span>

                </div>


            </div>


            <span class="status">
                --
            </span>


        </div>


    <?php else: ?>


        <?php foreach ($agendamentos as $agendamento): ?>


            <?php

            $hora = date(
                'H:i',
                strtotime($agendamento['hora'])
            );

            $statusNome = nomeStatus(
                $agendamento['status']
            );

            $statusClasse = classeStatus(
                $agendamento['status']
            );

            ?>


            <div class="appointment">


                <!-- HORÁRIO -->

                <div class="time">

                    <strong>
                        <?= htmlspecialchars($hora) ?>
                    </strong>

                    <span>
                        Agendamento
                    </span>

                </div>


                <div class="appointment-info">


                    <div class="client-icon">

                        <i class="fa-solid fa-user"></i>

                    </div>


                    <div>


                        <strong>

                            <?= htmlspecialchars(
                                $agendamento['cliente_nome']
                            ) ?>

                        </strong>


                        <span>

                            <?php if (!empty($agendamento['observacao'])): ?>

                                <?= htmlspecialchars(
                                    $agendamento['observacao']
                                ) ?>

                            <?php else: ?>

                                Serviço agendado

                            <?php endif; ?>

                        </span>


                        <small>

                            <i class="fa-solid fa-phone"></i>

                            <?= htmlspecialchars(
                                $agendamento['cliente_telefone']
                            ) ?>

                        </small>


                    </div>


                </div>



                <span
                    class="status <?= htmlspecialchars($statusClasse) ?>"
                >

                    <i class="fa-solid fa-circle"></i>

                    <?= htmlspecialchars($statusNome) ?>

                </span>



                <a
                    href="cadastrar-agendamento.php?editar=<?= (int) $agendamento['id_agendamento'] ?>"
                    class="more"
                    title="Editar agendamento"
                >

                    <i class="fa-solid fa-ellipsis-vertical"></i>

                </a>


            </div>


        <?php endforeach; ?>


    <?php endif; ?>


</section>


</main>


</body>

</html>

