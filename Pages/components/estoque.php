<?php

session_start();

require_once "../../Crud/crud.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login/login.php");
    exit;
}

$usuario = $_SESSION['usuario'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['acao']) && $_POST['acao'] === 'excluir') {

        $id_produto = filter_input(
            INPUT_POST,
            'id_produto',
            FILTER_VALIDATE_INT
        );

        if ($id_produto) {

            try {

                $stmt = $pdo->prepare("
                    SELECT COUNT(*)
                    FROM movimentacao
                    WHERE id_produto = ?
                ");

                $stmt->execute([$id_produto]);

                $temMovimentacao = $stmt->fetchColumn();

                if ($temMovimentacao > 0) {

                    $_SESSION['estoque_erro'] =
                        'Este produto possui movimentações e não pode ser excluído.';

                } else {

                    $stmt = $pdo->prepare("
                        DELETE FROM produtos
                        WHERE id_produto = ?
                    ");

                    $stmt->execute([$id_produto]);

                    $_SESSION['estoque_sucesso'] =
                        'Produto excluído com sucesso.';
                }

            } catch (PDOException $e) {

                $_SESSION['estoque_erro'] =
                    'Não foi possível excluir o produto.';
            }

            header("Location: estoque.php");
            exit;
        }
    }
}

$sucesso = $_SESSION['estoque_sucesso'] ?? '';
$erro = $_SESSION['estoque_erro'] ?? '';

unset($_SESSION['estoque_sucesso']);
unset($_SESSION['estoque_erro']);

$busca = trim($_GET['busca'] ?? '');

if ($busca !== '') {

    $termo = $pdo->quote("%{$busca}%");

    $produtos = readAll(
        $pdo,
        'produtos',
        "nome LIKE {$termo} OR sku LIKE {$termo}"
    );

    if ($produtos) {

        foreach ($produtos as &$produto) {

            $stmt = $pdo->prepare("
                SELECT nome
                FROM categoria
                WHERE id_categoria = ?
            ");

            $stmt->execute([
                $produto['id_categoria']
            ]);

            $produto['categoria_nome'] =
                $stmt->fetchColumn() ?: 'Sem categoria';
        }

        unset($produto);
    }

} else {

    $stmt = $pdo->query("
        SELECT
            p.id_produto,
            p.id_categoria,
            p.nome,
            p.sku,
            p.preco,
            p.estoque,
            p.descricao,
            p.capa,
            p.data_cadastro,
            p.estoque_minimo,
            c.nome AS categoria_nome
        FROM produtos p
        INNER JOIN categoria c
            ON c.id_categoria = p.id_categoria
        ORDER BY p.id_produto DESC
    ");

    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM produtos
");

$totalProdutos = (int) $stmt->fetchColumn();

$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM produtos
    WHERE estoque <= estoque_minimo
");

$estoqueBaixo = (int) $stmt->fetchColumn();

$stmt = $pdo->query("
    SELECT COALESCE(
        SUM(preco * estoque),
        0
    )
    FROM produtos
");

$valorEstoque = (float) $stmt->fetchColumn();

$stmt = $pdo->query("
    SELECT MAX(data_cadastro)
    FROM produtos
");

$ultimaAtualizacao = $stmt->fetchColumn();

if ($ultimaAtualizacao) {

    $ultimaAtualizacaoFormatada = date(
        'd/m/Y',
        strtotime($ultimaAtualizacao)
    );

} else {

    $ultimaAtualizacaoFormatada = 'Nenhuma';
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

<title>
    Prisma Mecânica - Estoque
</title>


<link
    rel="stylesheet"
    href="../css/estoque.css"
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




<?php if ($sucesso): ?>

    <div class="alert success">

        <i class="fa-solid fa-circle-check"></i>

        <?= htmlspecialchars($sucesso) ?>

    </div>

<?php endif; ?>


<?php if ($erro): ?>

    <div class="alert error">

        <i class="fa-solid fa-circle-exclamation"></i>

        <?= htmlspecialchars($erro) ?>

    </div>

<?php endif; ?>




<div class="page-top">


    <div>

        <div class="section-name">

            <i class="fa-solid fa-box"></i>

            ESTOQUE

        </div>


        <h1>
            Controle de <span>Estoque</span>
        </h1>


        <p>

            Gerencie seus produtos, acompanhe os níveis de estoque
            e evite a falta de itens no seu dia a dia.

        </p>

    </div>



</div>





<div class="cards">



    <div class="card">

        <div class="card-icon blue">

            <i class="fa-solid fa-box"></i>

        </div>


        <span>
            Total de Itens
        </span>


        <strong>
            <?= $totalProdutos ?>
        </strong>


        <small class="green">

            <i class="fa-solid fa-box"></i>

            Produtos cadastrados

        </small>

    </div>




    <div class="card">

        <div class="card-icon red">

            <i class="fa-solid fa-triangle-exclamation"></i>

        </div>


        <span>
            Itens em Estoque Baixo
        </span>


        <strong>
            <?= $estoqueBaixo ?>
        </strong>


        <small class="orange">

            <i class="fa-solid fa-arrow-down"></i>

            Precisam de reposição

        </small>

    </div>




    <div class="card">

        <div class="card-icon green">

            <i class="fa-solid fa-dollar-sign"></i>

        </div>


        <span>
            Valor Total em Estoque
        </span>


        <strong>

            R$

            <?= number_format(
                $valorEstoque,
                2,
                ',',
                '.'
            ) ?>

        </strong>


        <small class="green">

            <i class="fa-solid fa-chart-line"></i>

            Valor dos produtos

        </small>

    </div>




    <div class="card">

        <div class="card-icon purple">

            <i class="fa-regular fa-clock"></i>

        </div>


        <span>
            Última Atualização
        </span>


        <strong>

            <?= htmlspecialchars(
                $ultimaAtualizacaoFormatada
            ) ?>

        </strong>


        <small>

            <i class="fa-solid fa-circle live"></i>

            Estoque atualizado

        </small>

    </div>


</div>




<div class="products-box">


    <div class="products-header">


        <div class="products-title">


            <div class="title-icon">

                <i class="fa-solid fa-box"></i>

            </div>


            <h2>
                Produtos em Estoque
            </h2>


        </div>



        <div class="actions">


            <form
                method="GET"
                action=""
                class="product-search"
            >

                <i class="fa-solid fa-magnifying-glass"></i>


                <input
                    type="text"
                    name="busca"
                    placeholder="Buscar produto..."
                    value="<?= htmlspecialchars($busca) ?>"
                >

            </form>



            <button
                type="button"
                class="filter"
                onclick="document.querySelector('.product-search input').focus()"
            >

                <i class="fa-solid fa-filter"></i>

            </button>



            <a
                href="cadastrar-produto.php"
                class="new-product small"
            >

                <i class="fa-solid fa-plus"></i>

                Novo Produto

            </a>


        </div>

    </div>




    <div class="table">


        <div class="table-head">

            <span>
                Código
            </span>

            <span>
                Produto
            </span>

            <span>
                Categoria
            </span>

            <span>
                Quantidade
            </span>

            <span>
                Estoque Mínimo
            </span>

            <span>
                Valor Unitário
            </span>

            <span>
                Status
            </span>

            <span>
                Ações
            </span>

        </div>



        <?php if (empty($produtos)): ?>


            <div class="table-row">

                <span style="grid-column: 1 / -1;">

                    Nenhum produto encontrado.

                </span>

            </div>


        <?php else: ?>


            <?php foreach ($produtos as $produto): ?>


                <?php

                $estoqueAtual =
                    (int) $produto['estoque'];

                $estoqueMinimo =
                    (int) $produto['estoque_minimo'];


                $baixo =
                    $estoqueAtual <= $estoqueMinimo;

                ?>


                <div
    class="table-row"
    data-id="<?= (int) $produto['id_produto'] ?>"
>



                    <span>

                        <?= str_pad(
                            $produto['id_produto'],
                            3,
                            '0',
                            STR_PAD_LEFT
                        ) ?>

                    </span>




                    <div class="product-name">


                        <div class="product-img">


                                <i class="fa-solid fa-box"></i>


                        </div>


                        <?= htmlspecialchars(
                            $produto['nome']
                        ) ?>


                    </div>




                    <span>

                        <?= htmlspecialchars(
                            $produto['categoria_nome']
                        ) ?>

                    </span>




                    <span>

                        <?= $estoqueAtual ?>

                    </span>




                    <span>

                        <?= $estoqueMinimo ?>

                    </span>




                    <span>

                        R$

                        <?= number_format(
                            $produto['preco'],
                            2,
                            ',',
                            '.'
                        ) ?>

                    </span>




                    <?php if ($baixo): ?>


                        <span class="status low">

                            <i class="fa-solid fa-circle"></i>

                            Baixo

                        </span>


                    <?php else: ?>


                        <span class="status normal">

                            <i class="fa-solid fa-circle"></i>

                            Normal

                        </span>


                    <?php endif; ?>




                    <div class="row-actions">



                        <a
                            href="editar-produto.php?id=<?= (int) $produto['id_produto'] ?>"
                            title="Editar produto"
                        >

                            <i class="fa-solid fa-pen"></i>

                        </a>




                        <form
                            method="POST"
                            action=""
                            onsubmit="return confirmarExclusao('<?= htmlspecialchars($produto['nome'], ENT_QUOTES) ?>')"
                        >

                            <input
                                type="hidden"
                                name="acao"
                                value="excluir"
                            >


                            <input
                                type="hidden"
                                name="id_produto"
                                value="<?= $produto['id_produto'] ?>"
                            >


                            <button
                                type="submit"
                                title="Excluir produto"
                            >

                                <i class="fa-solid fa-trash"></i>

                            </button>

                        </form>


                    </div>


                </div>


            <?php endforeach; ?>


        <?php endif; ?>


    </div>


</div>

</section>

</main>

<script>

function confirmarExclusao(nome) {

    return confirm(
        'Deseja realmente excluir o produto "' +
        nome +
        '"?'
    );

}

</script>

</body>

</html>