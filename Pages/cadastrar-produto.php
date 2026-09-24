<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prisma - Cadastrar Produto</title>
    <link rel="stylesheet" href="../CSS/cadastrar-produto.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

    
<?php require_once "sidebar.php" ?>
    <!-- MAIN -->
    <main class="main">
        <!-- HEADER -->
        <header class="header">

            <div class="search">
                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    placeholder="Buscar no sistema..."
                >
            </div>

            <div class="header-right">

                <i class="fa-regular fa-bell notification"></i>

                <div class="administrator">

                    <div class="admin-icon">
                        <i class="fa-solid fa-user"></i>
                    </div>

                    <span>Administrador</span>

                    <i class="fa-solid fa-chevron-down"></i>
                </div>
            </div>
        </header>


        <!-- CONTEÚDO -->
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


            <!-- FORMULÁRIO -->
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


                <form class="form">
                    <!-- NOME -->
                    <div class="input-group full">

                        <label>Nome do produto</label>

                        <input
                            type="text"
                            placeholder="Ex: Filtro de óleo"
                            required
                        >
                    </div>


                    <!-- CÓDIGO -->
                    <div class="input-group">
                        <label>Código do produto</label>

                        <input
                            type="text"
                            placeholder="Ex: FIL-001"
>
                    </div>


                    <!-- CATEGORIA -->
                    <div class="input-group">

                        <label>Categoria</label>

                        <select required>

                            <option value="">
                                Selecione uma categoria
                            </option>

                            <option>
                                Motor
                            </option>

                            <option>
                                Freios
                            </option>

                            <option>
                                Suspensão
                            </option>

                            <option>
                                Elétrica
                            </option>

                            <option>
                                Lubrificantes
                            </option>

                            <option>
                                Ferramentas
                            </option>

                            <option>
                                Outros
                            </option>

                        </select>

                    </div>


                    <!-- MARCA -->
                    <div class="input-group">

                        <label>Marca</label>

                        <input
                            type="text"
                            placeholder="Ex: Bosch"
                        >

                    </div>


                    <!-- UNIDADE -->
                    <div class="input-group">

                        <label>Unidade</label>

                        <select>

                            <option>Unidade</option>
                            <option>Caixa</option>
                            <option>Litro</option>
                            <option>Quilograma</option>
                            <option>Par</option>
                            <option>Jogo</option>

                        </select>

                    </div>


                    <!-- QUANTIDADE -->
                    <div class="input-group">

                        <label>Quantidade em estoque</label>

                        <input
                            type="number"
                            min="0"
                            placeholder="0"
                            required
                        >

                    </div>


                    <!-- ESTOQUE MÍNIMO -->
                    <div class="input-group">

                        <label>Estoque mínimo</label>

                        <input
                            type="number"
                            min="0"
                            placeholder="Ex: 5"
                        >

                    </div>


                    <!-- PREÇO DE CUSTO -->
                    <div class="input-group">

                        <label>Preço de custo</label>

                        <div class="money-input">

                            <span>R$</span>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="0,00"
                            >

                        </div>

                    </div>


                    <!-- PREÇO DE VENDA -->
                    <div class="input-group">

                        <label>Preço de venda</label>

                        <div class="money-input">

                            <span>R$</span>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="0,00"
                            >

                        </div>

                    </div>


                    <!-- FORNECEDOR -->
                    <div class="input-group">

                        <label>Fornecedor</label>

                        <input
                            type="text"
                            placeholder="Nome do fornecedor"
                        >

                    </div>


                    <!-- LOCALIZAÇÃO -->
                    <div class="input-group">

                        <label>Localização no estoque</label>

                        <input
                            type="text"
                            placeholder="Ex: Prateleira A1"
                        >

                    </div>


                    <!-- DESCRIÇÃO -->
                    <div class="input-group full">

                        <label>Descrição</label>

                        <textarea
                            placeholder="Digite uma descrição do produto..."
                        ></textarea>

                    </div>


                    <!-- STATUS -->
                    <div class="input-group">

                        <label>Status</label>

                        <select>

                            <option>Ativo</option>
                            <option>Inativo</option>

                        </select>

                    </div>


                </form>


                <!-- BOTÕES -->
                <div class="buttons">

                    <a href="estoque.html" class="cancel">
                        Cancelar
                    </a>

                    <button class="save">

                        <i class="fa-solid fa-check"></i>

                        Cadastrar produto

                    </button>

                </div>

            </div>

        </section>

    </main>

</body>
</html>