<?php

session_start();

require_once "../../Crud/crud.php";


if (empty($_SESSION['usuario'])) {
    header("Location: ../login/login.php");
    exit;
}

$erro = '';
$sucesso = '';



$sqlCategorias = "
    SELECT id_categoria, nome
    FROM categoria
    ORDER BY nome ASC
";

$stmtCategorias = $pdo->query($sqlCategorias);
$categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);




if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');
    $sku = trim($_POST['sku'] ?? '');
    $id_categoria = filter_input(
        INPUT_POST,
        'id_categoria',
        FILTER_VALIDATE_INT
    );

    $estoque = filter_input(
        INPUT_POST,
        'estoque',
        FILTER_VALIDATE_INT
    );

    $estoque_minimo = filter_input(
        INPUT_POST,
        'estoque_minimo',
        FILTER_VALIDATE_INT
    );

    $preco = str_replace(
        ',',
        '.',
        trim($_POST['preco'] ?? '0')
    );

    $descricao = trim($_POST['descricao'] ?? '');


 

    if ($nome === '') {

        $erro = 'Informe o nome do produto.';

    } elseif (!$id_categoria) {

        $erro = 'Selecione uma categoria.';

    } elseif ($estoque === false || $estoque < 0) {

        $erro = 'Informe uma quantidade de estoque válida.';

    } elseif (
        $estoque_minimo === false ||
        $estoque_minimo < 0
    ) {

        $erro = 'Informe um estoque mínimo válido.';

    } elseif (!is_numeric($preco) || $preco < 0) {

        $erro = 'Informe um preço válido.';

    } else {


        $sqlCategoria = "
            SELECT id_categoria
            FROM categoria
            WHERE id_categoria = ?
            LIMIT 1
        ";

        $stmtCategoria = $pdo->prepare($sqlCategoria);
        $stmtCategoria->execute([$id_categoria]);

        if (!$stmtCategoria->fetch()) {

            $erro = 'A categoria selecionada não existe.';

        } else {

     

            if ($sku !== '') {

                $sqlSku = "
                    SELECT id_produto
                    FROM produtos
                    WHERE sku = ?
                    LIMIT 1
                ";

                $stmtSku = $pdo->prepare($sqlSku);
                $stmtSku->execute([$sku]);

                if ($stmtSku->fetch()) {

                    $erro = 'Já existe um produto cadastrado com esse código SKU.';

                }

            }


        

            if ($erro === '') {

                try {

                    $sql = "
                        INSERT INTO produtos (
                            id_categoria,
                            nome,
                            sku,
                            preco,
                            estoque,
                            descricao,
                            data_cadastro,
                            estoque_minimo
                        )
                        VALUES (
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            CURDATE(),
                            ?
                        )
                    ";

                    $stmt = $pdo->prepare($sql);

                    $stmt->execute([
                        $id_categoria,
                        $nome,
                        $sku !== '' ? $sku : null,
                        number_format((float) $preco, 2, '.', ''),
                        $estoque,
                        $descricao !== '' ? $descricao : null,
                        $estoque_minimo
                    ]);


                  

                    $_SESSION['sucesso'] = 'Produto cadastrado com sucesso!';

                    header("Location: estoque.php");
                    exit;

                } catch (PDOException $e) {

                    $erro = 'Não foi possível cadastrar o produto.';

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

    <title>Prisma - Cadastrar Produto</title>

    <link
        rel="stylesheet"
        href="../css/cadastrar-produto.css"
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


    <div class="page-title">


        <div class="section-name">

            <i class="fa-solid fa-box"></i>

            ESTOQUE

        </div>


        <h1>Cadastrar Produto</h1>


        <p>
            Adicione um novo produto ao estoque da oficina.
        </p>


    </div>


    <?php if ($erro !== ''): ?>

        <div class="alert alert-error">

            <i class="fa-solid fa-circle-exclamation"></i>

            <?= htmlspecialchars($erro) ?>

        </div>

    <?php endif; ?>


    <div class="card">


        <div class="card-header">


            <div class="card-icon">

                <i class="fa-solid fa-box-open"></i>

            </div>


            <div>

                <h2>Informações do produto</h2>

                <p>
                    Preencha os dados do produto.
                </p>

            </div>


        </div>


        <form
            class="form"
            method="POST"
            action=""
        >



            <div class="input-group full">

                <label for="nome">
                    Nome do produto
                </label>

                <input
                    type="text"
                    id="nome"
                    name="nome"
                    placeholder="Ex: Filtro de óleo"
                    value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
                    required
                >

            </div>



            <div class="input-group">

                <label for="sku">
                    Código do produto / SKU
                </label>

                <input
                    type="text"
                    id="sku"
                    name="sku"
                    placeholder="Ex: FIL-001"
                    value="<?= htmlspecialchars($_POST['sku'] ?? '') ?>"
                >

            </div>



            <div class="input-group">

                <label for="id_categoria">
                    Categoria
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
                            <?= (
                                isset($_POST['id_categoria']) &&
                                (int) $_POST['id_categoria'] ===
                                (int) $categoria['id_categoria']
                            ) ? 'selected' : '' ?>
                        >

                            <?= htmlspecialchars($categoria['nome']) ?>

                        </option>

                    <?php endforeach; ?>


                </select>

            </div>



            <div class="input-group">

                <label for="estoque">
                    Quantidade em estoque
                </label>

                <input
                    type="number"
                    id="estoque"
                    name="estoque"
                    min="0"
                    placeholder="0"
                    value="<?= htmlspecialchars($_POST['estoque'] ?? '') ?>"
                    required
                >

            </div>


            <div class="input-group">

                <label for="estoque_minimo">
                    Estoque mínimo
                </label>

                <input
                    type="number"
                    id="estoque_minimo"
                    name="estoque_minimo"
                    min="0"
                    placeholder="Ex: 5"
                    value="<?= htmlspecialchars($_POST['estoque_minimo'] ?? '') ?>"
                    required
                >

            </div>



            <div class="input-group">

                <label for="preco">
                    Preço
                </label>

                <div class="money-input">

                    <span>R$</span>

                    <input
                        type="number"
                        id="preco"
                        name="preco"
                        step="0.01"
                        min="0"
                        placeholder="0,00"
                        value="<?= htmlspecialchars($_POST['preco'] ?? '') ?>"
                        required
                    >

                </div>

            </div>



            <div class="input-group full">

                <label for="descricao">
                    Descrição
                </label>

                <textarea
                    id="descricao"
                    name="descricao"
                    placeholder="Digite uma descrição do produto..."
                ><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>

            </div>



            <div class="buttons">


                <a
                    href="estoque.php"
                    class="cancel"
                >
                    Cancelar
                </a>


                <button
                    type="submit"
                    class="save"
                >

                    <i class="fa-solid fa-check"></i>

                    Cadastrar produto

                </button>


            </div>


        </form>


    </div>


</section>


</main>


</body>

</html>

