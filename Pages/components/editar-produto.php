<?php

session_start();

require_once __DIR__ . "/../../Crud/crud.php";

if (empty($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: estoque.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT *
    FROM produtos
    WHERE id_produto = ?
    LIMIT 1
");

$stmt->execute([$id]);

$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    header('Location: estoque.php');
    exit;
}

$categorias = $pdo->query("
    SELECT
        id_categoria,
        nome
    FROM categoria
    ORDER BY nome
")->fetchAll(PDO::FETCH_ASSOC);

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');
    $sku = trim($_POST['sku'] ?? '');
    $idCategoria = (int) ($_POST['id_categoria'] ?? 0);
    $preco = str_replace(',', '.', $_POST['preco'] ?? '');
    $estoque = (int) ($_POST['estoque'] ?? 0);
    $estoqueMinimo = (int) ($_POST['estoque_minimo'] ?? 0);
    $descricao = trim($_POST['descricao'] ?? '');

    if ($nome === '') {

        $erro = 'Informe o nome do produto.';

    } elseif ($sku === '') {

        $erro = 'Informe o SKU do produto.';

    } elseif ($idCategoria <= 0) {

        $erro = 'Selecione uma categoria.';

    } elseif (!is_numeric($preco) || $preco < 0) {

        $erro = 'Informe um preço válido.';

    } elseif ($estoque < 0) {

        $erro = 'O estoque não pode ser negativo.';

    } elseif ($estoqueMinimo < 0) {

        $erro = 'O estoque mínimo não pode ser negativo.';

    } else {

        $stmt = $pdo->prepare("
            SELECT id_produto
            FROM produtos
            WHERE sku = ?
            AND id_produto <> ?
            LIMIT 1
        ");

        $stmt->execute([
            $sku,
            $id
        ]);

        if ($stmt->fetch()) {

            $erro = 'Este SKU já está cadastrado em outro produto.';

        } else {

            $stmt = $pdo->prepare("
                SELECT id_categoria
                FROM categoria
                WHERE id_categoria = ?
                LIMIT 1
            ");

            $stmt->execute([$idCategoria]);

            if (!$stmt->fetch()) {

                $erro = 'A categoria selecionada não existe.';

            } else {

                $stmt = $pdo->prepare("
                    UPDATE produtos
                    SET
                        id_categoria = ?,
                        nome = ?,
                        sku = ?,
                        preco = ?,
                        estoque = ?,
                        descricao = ?,
                        estoque_minimo = ?
                    WHERE id_produto = ?
                ");

                $stmt->execute([
                    $idCategoria,
                    $nome,
                    $sku,
                    $preco,
                    $estoque,
                    $descricao !== '' ? $descricao : null,
                    $estoqueMinimo,
                    $id
                ]);

                header('Location: estoque.php?editado=1');
                exit;
            }
        }
    }

    $produto['nome'] = $nome;
    $produto['sku'] = $sku;
    $produto['id_categoria'] = $idCategoria;
    $produto['preco'] = $preco;
    $produto['estoque'] = $estoque;
    $produto['estoque_minimo'] = $estoqueMinimo;
    $produto['descricao'] = $descricao;
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

    <title>Editar Produto - Prisma Mecânica</title>

    <link
        rel="stylesheet"
        href="../css/cadastrar-produto.css"
    >

    <link
        rel="stylesheet"
        href="../css/editar-produto.css"
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

        <?php require_once __DIR__ . "/../partials/header.php"; ?>

        <div class="produto-container">

            <div class="page-header">

                <div>

                    <h1>
                        Editar Produto
                    </h1>

                    <p>
                        Altere as informações do produto.
                    </p>

                </div>

                <a
                    href="estoque.php"
                    class="btn-voltar"
                >
                    <i class="fa-solid fa-arrow-left"></i>
                    Voltar
                </a>

            </div>


            <?php if ($erro): ?>

                <div class="alert-error">

                    <i class="fa-solid fa-circle-exclamation"></i>

                    <?= escapar($erro) ?>

                </div>

            <?php endif; ?>


            <form
                method="POST"
                class="form-card"
            >

                <div class="form-section">

                    <div class="section-title">

                        <i class="fa-solid fa-box"></i>

                        <div>

                            <h2>
                                Informações do produto
                            </h2>

                            <p>
                                Atualize os dados do produto.
                            </p>

                        </div>

                    </div>


                    <div class="form-grid">

                        <div class="form-group full">

                            <label for="nome">
                                Nome do produto *
                            </label>

                            <input
                                type="text"
                                id="nome"
                                name="nome"
                                value="<?= escapar($produto['nome']) ?>"
                                placeholder="Digite o nome do produto"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label for="sku">
                                SKU *
                            </label>

                            <input
                                type="text"
                                id="sku"
                                name="sku"
                                value="<?= escapar($produto['sku']) ?>"
                                placeholder="Ex.: OLEO-5W30"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label for="id_categoria">
                                Categoria *
                            </label>

                            <select
                                id="id_categoria"
                                name="id_categoria"
                                required
                            >

                                <option value="">
                                    Selecione uma categoria
                                </option>

                                <?php foreach ($categorias as $categoria): ?>

                                    <option
                                        value="<?= (int) $categoria['id_categoria'] ?>"
                                        <?= (int) $produto['id_categoria'] === (int) $categoria['id_categoria'] ? 'selected' : '' ?>
                                    >

                                        <?= escapar($categoria['nome']) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="form-group">

                            <label for="preco">
                                Preço *
                            </label>

                            <input
                                type="number"
                                id="preco"
                                name="preco"
                                value="<?= escapar($produto['preco']) ?>"
                                min="0"
                                step="0.01"
                                placeholder="0,00"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label for="estoque">
                                Estoque *
                            </label>

                            <input
                                type="number"
                                id="estoque"
                                name="estoque"
                                value="<?= (int) $produto['estoque'] ?>"
                                min="0"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label for="estoque_minimo">
                                Estoque mínimo *
                            </label>

                            <input
                                type="number"
                                id="estoque_minimo"
                                name="estoque_minimo"
                                value="<?= (int) $produto['estoque_minimo'] ?>"
                                min="0"
                                required
                            >

                        </div>


                        <div class="form-group full">

                            <label for="descricao">
                                Descrição
                            </label>

                            <textarea
                                id="descricao"
                                name="descricao"
                                rows="5"
                                placeholder="Digite uma descrição para o produto..."
                            ><?= escapar($produto['descricao']) ?></textarea>

                        </div>

                    </div>

                </div>


                <div class="form-actions">

                    <a
                        href="estoque.php"
                        class="btn-cancelar"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="btn-salvar"
                    >

                        <i class="fa-solid fa-floppy-disk"></i>

                        Salvar alterações

                    </button>

                </div>

            </form>

        </div>

    </main>

</div>

</body>
</html>