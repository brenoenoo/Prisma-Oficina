<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prisma - Configurações</title>
    <link rel="stylesheet" href="../CSS/configuracoes.css">
    <link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

   
<?php require_once "sidebar.php" ?>


    <!-- CONTEÚDO -->
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


        <!-- CONFIGURAÇÕES -->
        <section class="content">

            <div class="page-title">

                <div class="section-name">
                    <i class="fa-solid fa-gear"></i>
                    CONFIGURAÇÕES
                </div>

                <h1>Configurações</h1>

                <p>
                    Gerencie funcionários, usuários e informações da oficina.
                </p>

            </div>


            <div class="settings-layout">

                <!-- MENU DE CONFIGURAÇÕES -->
                <div class="settings-menu">

                    <div class="settings-menu-title">
                        Configurações
                    </div>

                    <a href="#funcionarios" class="settings-link active">
                        <i class="fa-solid fa-users"></i>
                        Funcionários
                    </a>

                    <a href="#usuarios" class="settings-link">
                        <i class="fa-solid fa-user-shield"></i>
                        Usuários e permissões
                    </a>

                    <a href="#oficina" class="settings-link">
                        <i class="fa-solid fa-building"></i>
                        Dados da oficina
                    </a>

                    <a href="#categorias" class="settings-link">
                        <i class="fa-solid fa-tags"></i>
                        Categorias
                    </a>

                    <a href="#seguranca" class="settings-link">
                        <i class="fa-solid fa-lock"></i>
                        Segurança
                    </a>

                </div>


                <!-- ÁREA PRINCIPAL -->
                <div class="settings-content">


                    <!-- FUNCIONÁRIOS -->
                    <div class="settings-section" id="funcionarios">

                        <div class="section-header">

                            <div>
                                <h2>Funcionários</h2>

                                <p>
                                    Cadastre e gerencie os funcionários da oficina.
                                </p>
                            </div>

                            <button class="primary-button">
                                <i class="fa-solid fa-plus"></i>
                                Cadastrar funcionário
                            </button>

                        </div>


                        <!-- FORMULÁRIO -->
                        <div class="form-box">

                            <h3>
                                <i class="fa-solid fa-user-plus"></i>
                                Novo funcionário
                            </h3>

                            <div class="form-grid">

                                <div class="input-group">
                                    <label>Nome completo</label>
                                    <input
                                        type="text"
                                        placeholder="Digite o nome"
                                    >
                                </div>

                                <div class="input-group">
                                    <label>CPF</label>
                                    <input
                                        type="text"
                                        placeholder="000.000.000-00"
                                    >
                                </div>

                                <div class="input-group">
                                    <label>E-mail</label>
                                    <input
                                        type="email"
                                        placeholder="funcionario@email.com"
                                    >
                                </div>

                                <div class="input-group">
                                    <label>Telefone</label>
                                    <input
                                        type="text"
                                        placeholder="(00) 00000-0000"
                                    >
                                </div>

                                <div class="input-group">
                                    <label>Cargo</label>

                                    <select>
                                        <option>Selecione o cargo</option>
                                        <option>Mecânico</option>
                                        <option>Gerente</option>
                                        <option>Atendente</option>
                                        <option>Estoquista</option>
                                        <option>Administrador</option>
                                    </select>
                                </div>

                                <div class="input-group">
                                    <label>Senha inicial</label>

                                    <input
                                        type="password"
                                        placeholder="Digite uma senha"
                                    >
                                </div>

                            </div>

                            <div class="form-buttons">

                                <button class="cancel-button">
                                    Cancelar
                                </button>

                                <button class="save-button">
                                    <i class="fa-solid fa-check"></i>
                                    Cadastrar funcionário
                                </button>

                            </div>

                        </div>


                        <!-- LISTA -->
                        <div class="employees-box">

                            <div class="employees-header">

                                <div>
                                    <h3>Funcionários cadastrados</h3>
                                    <span>4 funcionários ativos</span>
                                </div>

                                <div class="employee-search">
                                    <i class="fa-solid fa-magnifying-glass"></i>

                                    <input
                                        type="text"
                                        placeholder="Buscar funcionário..."
                                    >
                                </div>

                            </div>


                            <div class="employee-list">

                                <!-- FUNCIONÁRIO 1 -->
                                <div class="employee">

                                    <div class="employee-avatar">
                                        AC
                                    </div>

                                    <div class="employee-info">
                                        <strong>André Costa</strong>
                                        <span>Mecânico</span>
                                    </div>

                                    <div class="employee-email">
                                        andre@email.com
                                    </div>

                                    <div class="employee-status">
                                        <i class="fa-solid fa-circle"></i>
                                        Ativo
                                    </div>

                                    <div class="employee-actions">
                                        <button title="Editar">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>

                                        <button title="Excluir">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>

                                </div>


                                <!-- FUNCIONÁRIO 2 -->
                                <div class="employee">

                                    <div class="employee-avatar">
                                        MS
                                    </div>

                                    <div class="employee-info">
                                        <strong>Marcos Silva</strong>
                                        <span>Mecânico</span>
                                    </div>

                                    <div class="employee-email">
                                        marcos@email.com
                                    </div>

                                    <div class="employee-status">
                                        <i class="fa-solid fa-circle"></i>
                                        Ativo
                                    </div>

                                    <div class="employee-actions">
                                        <button>
                                            <i class="fa-solid fa-pen"></i>
                                        </button>

                                        <button>
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>

                                </div>


                                <!-- FUNCIONÁRIO 3 -->
                                <div class="employee">

                                    <div class="employee-avatar">
                                        JP
                                    </div>

                                    <div class="employee-info">
                                        <strong>João Pereira</strong>
                                        <span>Estoquista</span>
                                    </div>

                                    <div class="employee-email">
                                        joao@email.com
                                    </div>

                                    <div class="employee-status">
                                        <i class="fa-solid fa-circle"></i>
                                        Ativo
                                    </div>

                                    <div class="employee-actions">
                                        <button>
                                            <i class="fa-solid fa-pen"></i>
                                        </button>

                                        <button>
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>

                                </div>


                                <!-- FUNCIONÁRIO 4 -->
                                <div class="employee">

                                    <div class="employee-avatar">
                                        RL
                                    </div>

                                    <div class="employee-info">
                                        <strong>Rafael Lima</strong>
                                        <span>Atendente</span>
                                    </div>

                                    <div class="employee-email">
                                        rafael@email.com
                                    </div>

                                    <div class="employee-status">
                                        <i class="fa-solid fa-circle"></i>
                                        Ativo
                                    </div>

                                    <div class="employee-actions">
                                        <button>
                                            <i class="fa-solid fa-pen"></i>
                                        </button>

                                        <button>
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- USUÁRIOS -->
                    <div class="simple-section" id="usuarios">

                        <div class="simple-icon">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>

                        <div>
                            <h2>Usuários e permissões</h2>

                            <p>
                                Controle quem pode acessar cada área do sistema.
                            </p>
                        </div>

                        <button class="secondary-button">
                            Gerenciar permissões
                        </button>

                    </div>


                    <!-- OFICINA -->
                    <div class="simple-section" id="oficina">

                        <div class="simple-icon">
                            <i class="fa-solid fa-building"></i>
                        </div>

                        <div>
                            <h2>Dados da oficina</h2>

                            <p>
                                Nome, endereço, telefone, CNPJ e outras informações.
                            </p>
                        </div>

                        <button class="secondary-button">
                            Editar informações
                        </button>

                    </div>


                    <!-- CATEGORIAS -->
                    <div class="simple-section" id="categorias">

                        <div class="simple-icon">
                            <i class="fa-solid fa-tags"></i>
                        </div>

                        <div>
                            <h2>Categorias</h2>

                            <p>
                                Crie e organize as categorias dos produtos do estoque.
                            </p>
                        </div>

                        <button class="secondary-button">
                            Gerenciar categorias
                        </button>

                    </div>


                    <!-- SEGURANÇA -->
                    <div class="simple-section" id="seguranca">

                        <div class="simple-icon">
                            <i class="fa-solid fa-lock"></i>
                        </div>

                        <div>
                            <h2>Segurança</h2>

                            <p>
                                Altere sua senha e configure opções de segurança.
                            </p>
                        </div>

                        <button class="secondary-button">
                            Configurar
                        </button>

                    </div>

                </div>

            </div>

        </section>

    </main>

</body>
</html>