<?php

session_start();

require_once __DIR__ . "/../../Crud/crud.php";

if (empty($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$dataAtual = date('d/m/Y');

$sqlTotalProdutos = "
    SELECT COUNT(*)
    FROM produtos
";

$stmt = $pdo->query($sqlTotalProdutos);
$totalProdutos = (int) $stmt->fetchColumn();

$sqlValorEstoque = "
    SELECT COALESCE(SUM(preco * estoque), 0)
    FROM produtos
";

$stmt = $pdo->query($sqlValorEstoque);
$valorEstoque = (float) $stmt->fetchColumn();

$sqlEstoqueBaixo = "
    SELECT COUNT(*)
    FROM produtos
    WHERE estoque <= estoque_minimo
";

$stmt = $pdo->query($sqlEstoqueBaixo);
$estoqueBaixo = (int) $stmt->fetchColumn();

$sqlMovimentacoes = "
    SELECT COUNT(*)
    FROM movimentacao
    WHERE realizado_em >= DATE_SUB(NOW(), INTERVAL 10 DAY)
";

$stmt = $pdo->query($sqlMovimentacoes);
$totalMovimentacoes = (int) $stmt->fetchColumn();

$sqlProdutosCriticos = "
    SELECT
        p.id_produto,
        p.nome,
        p.estoque,
        p.estoque_minimo,
        p.capa,
        c.nome AS categoria_nome
    FROM produtos p
    INNER JOIN categoria c
        ON c.id_categoria = p.id_categoria
    WHERE p.estoque <= p.estoque_minimo
    ORDER BY p.estoque ASC, p.nome ASC
    LIMIT 10
";

$stmt = $pdo->query($sqlProdutosCriticos);
$produtosCriticos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$valorEstoqueFormatado = number_format(
    $valorEstoque,
    2,
    ',',
    '.'
);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Prisma Mecânica - Dashboard</title>

    <link
        rel="stylesheet"
        href="../css/dashboard.css"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>

<body>

<?php require_once __DIR__ . "/../partials/sidebar.php"; ?>

<main class="main">

    <?php require_once __DIR__ . "/../partials/header.php"; ?>

    <section class="content">

        <div class="page-top">

            <div>

                <h1>Dashboard</h1>

                <p>
                    Visão geral do seu negócio em tempo real.
                </p>

            </div>

            <div class="date">

                <i class="fa-regular fa-calendar"></i>

                Hoje, <?= htmlspecialchars($dataAtual) ?>

            </div>

        </div>

        <div class="cards">

            <div class="card">

                <div class="card-icon blue">
                    <i class="fa-solid fa-box"></i>
                </div>

                <span>Total de Produtos</span>

                <strong>
                    <?= $totalProdutos ?>
                </strong>

                <small class="green">

                    <i class="fa-solid fa-box"></i>

                    Produtos cadastrados

                </small>

            </div>

            <div class="card">

                <div class="card-icon green">
                    <i class="fa-solid fa-dollar-sign"></i>
                </div>

                <span>Valor em Estoque</span>

                <strong>
                    R$ <?= $valorEstoqueFormatado ?>
                </strong>

                <small>

                    <i class="fa-regular fa-circle"></i>

                    Atualizado agora

                </small>

            </div>

            <div class="card">

                <div class="card-icon red">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <span>Estoque Baixo</span>

                <strong>
                    <?= $estoqueBaixo ?>
                </strong>

                <small class="red-text">

                    <i class="fa-solid fa-arrow-down"></i>

                    Necessita atenção

                </small>

            </div>

            <div class="card">

                <div class="card-icon purple">
                    <i class="fa-solid fa-right-left"></i>
                </div>

                <span>Movimentações</span>

                <strong>
                    <?= $totalMovimentacoes ?>
                </strong>

                <small>

                    <i class="fa-regular fa-circle"></i>

                    Últimos 10 dias

                </small>

            </div>

        </div>

        <div class="critical">

            <div class="critical-header">

                <div class="critical-title">

                    <div class="card-icon red">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>

                    <div>

                        <h2>Estoque Crítico</h2>

                        <p>
                            Produtos abaixo ou no limite do estoque mínimo.
                        </p>

                    </div>

                </div>

                <a href="estoque.php">

                    <i class="fa-solid fa-chevron-right"></i>

                </a>

            </div>

            <div class="table-header">

                <span>Produto</span>

                <span>Categoria</span>

                <span>Quantidade</span>

                <span>Status</span>

            </div>

            <?php if (empty($produtosCriticos)): ?>

                <div class="product">

                    <div class="product-name">

                        <div class="product-image">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>

                        <span>
                            Nenhum produto com estoque crítico.
                        </span>

                    </div>

                    <span>-</span>

                    <div class="quantity">

                        <strong>0</strong>

                        <small>uni.</small>

                    </div>

                    <span class="status">

                        <i class="fa-solid fa-circle"></i>

                        Normal

                    </span>

                </div>

            <?php else: ?>

                <?php foreach ($produtosCriticos as $produto): ?>

                    <div
    class="product"
    data-id="<?= (int) $produto['id_produto'] ?>"
>

                        <div class="product-name">

                            <div class="product-image">

                                <?php if (!empty($produto['capa'])): ?>

                                    <img
                                        src="<?= htmlspecialchars($produto['capa']) ?>"
                                        alt="<?= htmlspecialchars($produto['nome']) ?>"
                                    >

                                <?php else: ?>

                                    <i class="fa-solid fa-box"></i>

                                <?php endif; ?>

                            </div>

                            <span>
                                <?= htmlspecialchars($produto['nome']) ?>
                            </span>

                        </div>

                        <span>
                            <?= htmlspecialchars($produto['categoria_nome']) ?>
                        </span>

                        <div class="quantity">

                            <strong>
                                <?= (int) $produto['estoque'] ?>
                            </strong>

                            <small>uni.</small>

                        </div>

                        <span class="status critical-status">

                            <i class="fa-solid fa-circle"></i>

                            Crítico

                        </span>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

    </section>

</main>

</body>

</html>