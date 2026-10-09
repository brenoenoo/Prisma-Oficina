<?php

session_start();

require_once __DIR__ . "/../../Crud/crud.php";

if (empty($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$usuario = $_SESSION['usuario'];

$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $acao = $_POST['acao'] ?? '';

    if ($acao === 'cadastrar') {

        $tipo = $_POST['tipo'] ?? '';
        $descricao = trim($_POST['descricao'] ?? '');
        $categoria = trim($_POST['categoria'] ?? '');
        $valor = str_replace(',', '.', $_POST['valor'] ?? '');
        $data = $_POST['data_movimentacao'] ?? '';

        if (!in_array($tipo, ['receita', 'despesa'], true)) {
            $erro = 'Tipo de movimentação inválido.';
        } elseif ($descricao === '') {
            $erro = 'Informe a descrição.';
        } elseif ($categoria === '') {
            $erro = 'Informe a categoria.';
        } elseif (!is_numeric($valor) || $valor <= 0) {
            $erro = 'Informe um valor válido.';
        } elseif ($data === '') {
            $erro = 'Informe a data.';
        } else {

            $stmt = $pdo->prepare("
                INSERT INTO financeiro
                (
                    tipo,
                    descricao,
                    categoria,
                    valor,
                    data_movimentacao,
                    id_usuario
                )
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $tipo,
                $descricao,
                $categoria,
                $valor,
                $data,
                $usuario['id_usuario']
            ]);

            header('Location: financeiro.php?sucesso=1');
            exit;
        }
    }

    if ($acao === 'excluir') {

        $id = (int) ($_POST['id_financeiro'] ?? 0);

        if ($id > 0) {

            $stmt = $pdo->prepare("
                DELETE FROM financeiro
                WHERE id_financeiro = ?
            ");

            $stmt->execute([$id]);

            header('Location: financeiro.php?excluido=1');
            exit;
        }
    }
}

if (isset($_GET['sucesso'])) {
    $mensagem = 'Movimentação cadastrada com sucesso.';
}

if (isset($_GET['excluido'])) {
    $mensagem = 'Movimentação excluída com sucesso.';
}

$dataInicio = $_GET['data_inicio'] ?? '';
$dataFim = $_GET['data_fim'] ?? '';
$filtroTipo = $_GET['tipo'] ?? '';

$where = [];
$params = [];

if ($dataInicio !== '') {
    $where[] = 'f.data_movimentacao >= ?';
    $params[] = $dataInicio;
}

if ($dataFim !== '') {
    $where[] = 'f.data_movimentacao <= ?';
    $params[] = $dataFim;
}

if (in_array($filtroTipo, ['receita', 'despesa'], true)) {
    $where[] = 'f.tipo = ?';
    $params[] = $filtroTipo;
}

$whereSql = '';

if ($where) {
    $whereSql = 'WHERE ' . implode(' AND ', $where);
}

$stmt = $pdo->prepare("
    SELECT
        f.*,
        u.nome AS usuario_nome
    FROM financeiro f
    LEFT JOIN usuario u
        ON u.id_usuario = f.id_usuario
    $whereSql
    ORDER BY f.data_movimentacao DESC, f.id_financeiro DESC
");

$stmt->execute($params);

$movimentacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(valor), 0)
    FROM financeiro f
    WHERE f.tipo = 'receita'
    " . ($whereSql ? " AND " . implode(' AND ', array_map(function ($item) {
        return str_replace('f.', 'f.', $item);
    }, $where)) : '') . "
");

$stmt->execute($params);
$totalReceitas = (float) $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(valor), 0)
    FROM financeiro f
    WHERE f.tipo = 'despesa'
    " . ($whereSql ? " AND " . implode(' AND ', $where) : '') . "
");

$stmt->execute($params);
$totalDespesas = (float) $stmt->fetchColumn();

$saldo = $totalReceitas - $totalDespesas;

function moeda($valor)
{
    return 'R$ ' . number_format((float) $valor, 2, ',', '.');
}

function escapar($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
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

    <title>Financeiro - Prisma Mecânica</title>

    <link
        rel="stylesheet"
        href="../css/financeiro.css"
    >
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


</head>

<body>

<div class="layout">

<?php require_once __DIR__ . "/../partials/sidebar.php"; ?>

<main class="main">

<?php require_once __DIR__ . "/../partials/header.php"; ?>

        <section class="financeiro-container">

            <div class="page-header">

                <div>
                    <h1>Financeiro</h1>

                    <p>
                        Controle as receitas e despesas da oficina.
                    </p>
                </div>

                <button
                    type="button"
                    class="btn-primary"
                    onclick="abrirModal()"
                >
                    <i class="fa-solid fa-plus"></i>
                    Nova movimentação
                </button>

            </div>


            <?php if ($mensagem): ?>

                <div class="alert success">
                    <i class="fa-solid fa-circle-check"></i>
                    <?= escapar($mensagem) ?>
                </div>

            <?php endif; ?>


            <?php if ($erro): ?>

                <div class="alert error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?= escapar($erro) ?>
                </div>

            <?php endif; ?>


            <div class="cards-financeiro">

                <div class="finance-card receita">

                    <div class="finance-icon">
                        <i class="fa-solid fa-arrow-trend-up"></i>
                    </div>

                    <div>

                        <span>Total de receitas</span>

                        <strong>
                            <?= moeda($totalReceitas) ?>
                        </strong>

                    </div>

                </div>


                <div class="finance-card despesa">

                    <div class="finance-icon">
                        <i class="fa-solid fa-arrow-trend-down"></i>
                    </div>

                    <div>

                        <span>Total de despesas</span>

                        <strong>
                            <?= moeda($totalDespesas) ?>
                        </strong>

                    </div>

                </div>


                <div class="finance-card saldo">

                    <div class="finance-icon">
                        <i class="fa-solid fa-wallet"></i>
                    </div>

                    <div>

                        <span>Saldo</span>

                        <strong>
                            <?= moeda($saldo) ?>
                        </strong>

                    </div>

                </div>

            </div>


            <div class="filters">

                <form method="GET">

                    <div class="filter-group">

                        <label>
                            Data inicial
                        </label>

                        <input
                            type="date"
                            name="data_inicio"
                            value="<?= escapar($dataInicio) ?>"
                        >

                    </div>


                    <div class="filter-group">

                        <label>
                            Data final
                        </label>

                        <input
                            type="date"
                            name="data_fim"
                            value="<?= escapar($dataFim) ?>"
                        >

                    </div>


                    <div class="filter-group">

                        <label>
                            Tipo
                        </label>

                        <select name="tipo">

                            <option value="">
                                Todos
                            </option>

                            <option
                                value="receita"
                                <?= $filtroTipo === 'receita' ? 'selected' : '' ?>
                            >
                                Receitas
                            </option>

                            <option
                                value="despesa"
                                <?= $filtroTipo === 'despesa' ? 'selected' : '' ?>
                            >
                                Despesas
                            </option>

                        </select>

                    </div>


                    <button
                        type="submit"
                        class="btn-filter"
                    >
                        <i class="fa-solid fa-filter"></i>
                        Filtrar
                    </button>


                    <a
                        href="financeiro.php"
                        class="btn-clear"
                    >
                        Limpar
                    </a>

                </form>

            </div>


            <div class="table-container">

                <div class="table-header">

                    <div>

                        <h2>
                            Histórico financeiro
                        </h2>

                        <span>
                            <?= count($movimentacoes) ?> movimentação(ões)
                        </span>

                    </div>

                </div>


                <div class="table-responsive">

                    <table>

                        <thead>

                            <tr>

                                <th>Data</th>

                                <th>Descrição</th>

                                <th>Categoria</th>

                                <th>Tipo</th>

                                <th>Responsável</th>

                                <th>Valor</th>

                                <th>Ação</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php if (!$movimentacoes): ?>

                            <tr>

                                <td
                                    colspan="7"
                                    class="empty"
                                >

                                    <i class="fa-solid fa-receipt"></i>

                                    <p>
                                        Nenhuma movimentação encontrada.
                                    </p>

                                </td>

                            </tr>

                        <?php else: ?>

                            <?php foreach ($movimentacoes as $mov): ?>

                                <tr data-id="<?= (int) $mov['id_financeiro'] ?>">

                                    <td>
                                        <?= date('d/m/Y', strtotime($mov['data_movimentacao'])) ?>
                                    </td>

                                    <td>
                                        <?= escapar($mov['descricao']) ?>
                                    </td>

                                    <td>
                                        <?= escapar($mov['categoria']) ?>
                                    </td>

                                    <td>

                                        <?php if ($mov['tipo'] === 'receita'): ?>

                                            <span class="badge receita">
                                                <i class="fa-solid fa-arrow-up"></i>
                                                Receita
                                            </span>

                                        <?php else: ?>

                                            <span class="badge despesa">
                                                <i class="fa-solid fa-arrow-down"></i>
                                                Despesa
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td>
                                        <?= escapar($mov['usuario_nome'] ?? 'Não informado') ?>
                                    </td>

                                    <td>

                                        <strong class="<?= $mov['tipo'] === 'receita' ? 'valor-receita' : 'valor-despesa' ?>">

                                            <?= $mov['tipo'] === 'receita' ? '+' : '-' ?>

                                            <?= moeda($mov['valor']) ?>

                                        </strong>

                                    </td>

                                    <td>

                                        <form
                                            method="POST"
                                            onsubmit="return confirm('Deseja realmente excluir esta movimentação?')"
                                        >

                                            <input
                                                type="hidden"
                                                name="acao"
                                                value="excluir"
                                            >

                                            <input
                                                type="hidden"
                                                name="id_financeiro"
                                                value="<?= (int) $mov['id_financeiro'] ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="btn-delete"
                                                title="Excluir"
                                            >
                                                <i class="fa-solid fa-trash"></i>
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </section>

    </main>

</div>


<div
    class="modal"
    id="modalFinanceiro"
>

    <div class="modal-content">

        <div class="modal-header">

            <div>

                <h2>
                    Nova movimentação
                </h2>

                <p>
                    Registre uma receita ou despesa.
                </p>

            </div>

            <button
                type="button"
                class="modal-close"
                onclick="fecharModal()"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>


        <form
            method="POST"
            class="finance-form"
        >

            <input
                type="hidden"
                name="acao"
                value="cadastrar"
            >


            <div class="form-group">

                <label>
                    Tipo
                </label>

                <select
                    name="tipo"
                    required
                >

                    <option value="">
                        Selecione
                    </option>

                    <option value="receita">
                        Receita
                    </option>

                    <option value="despesa">
                        Despesa
                    </option>

                </select>

            </div>


            <div class="form-group">

                <label>
                    Descrição
                </label>

                <input
                    type="text"
                    name="descricao"
                    placeholder="Ex.: Serviço de troca de óleo"
                    maxlength="255"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Categoria
                </label>

                <select
                    name="categoria"
                    required
                >

                    <option value="">
                        Selecione
                    </option>

                    <option value="Serviço">
                        Serviço
                    </option>

                    <option value="Venda de produto">
                        Venda de produto
                    </option>

                    <option value="Peças">
                        Peças
                    </option>

                    <option value="Salários">
                        Salários
                    </option>

                    <option value="Aluguel">
                        Aluguel
                    </option>

                    <option value="Energia">
                        Energia
                    </option>

                    <option value="Água">
                        Água
                    </option>

                    <option value="Internet">
                        Internet
                    </option>

                    <option value="Outros">
                        Outros
                    </option>

                </select>

            </div>


            <div class="form-row">

                <div class="form-group">

                    <label>
                        Valor
                    </label>

                    <input
                        type="number"
                        name="valor"
                        min="0.01"
                        step="0.01"
                        placeholder="0,00"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Data
                    </label>

                    <input
                        type="date"
                        name="data_movimentacao"
                        value="<?= date('Y-m-d') ?>"
                        required
                    >

                </div>

            </div>


            <div class="modal-actions">

                <button
                    type="button"
                    class="btn-cancel"
                    onclick="fecharModal()"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="btn-primary"
                >
                    <i class="fa-solid fa-check"></i>
                    Salvar
                </button>

            </div>

        </form>

    </div>

</div>


<script>

function abrirModal() {
    document
        .getElementById('modalFinanceiro')
        .classList.add('active');
}

function fecharModal() {
    document
        .getElementById('modalFinanceiro')
        .classList.remove('active');
}

window.addEventListener('click', function(event) {

    const modal = document.getElementById('modalFinanceiro');

    if (event.target === modal) {
        fecharModal();
    }

});

</script>

</body>
</html>