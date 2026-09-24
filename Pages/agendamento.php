<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prisma - Agendamento</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../CSS/agendamento.css">
</head>

<body>

<?php require_once "sidebar.php" ?>

    <main class="main">

        <!-- HEADER -->
        <header>

            <div class="search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Pesquisar...">
            </div>

            <div class="header-right">
                <i class="fa-regular fa-bell notification"></i>

                <div class="profile">

                    <div class="profile-icon">
                        <i class="fa-solid fa-user"></i>
                    </div>

                    <div>
                        <strong>Administrator</strong>
                        <small>Administrador</small>
                    </div>

                </div>
            </div>
        </header>


        <!-- TÍTULO -->
        <section class="page-title">
            <div>
                <h1>Agendamento</h1>

                <p>
                    Gerencie os serviços agendados da oficina.
                </p>
            </div>

            <button class="new-button">
                <i class="fa-solid fa-plus"></i>
                Novo agendamento
            </button>
        </section>


        <!-- FILTROS -->
        <section class="filters">
            <div class="filter">

                <label>Data</label>

                <input type="date">

            </div>

            <div class="filter">

                <label>Status</label>

                <select>

                    <option>Todos</option>
                    <option>Agendado</option>
                    <option>Em andamento</option>
                    <option>Concluído</option>
                    <option>Cancelado</option>

                </select>

            </div>

            <div class="filter search-client">

                <label>Pesquisar cliente</label>

                <div>
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input
                        type="text"
                        placeholder="Nome ou telefone"
                    >
                </div>

            </div>
        </section>


        <!-- AGENDAMENTOS -->
        <section class="appointments">
            <div class="section-header">
                <div>
                    <h2>Agendamentos</h2>
                    <p>Serviços programados para hoje.</p>
                </div>

                <span class="date">
                    24 de Setembro de 2026
                </span>
            </div>


            <!-- AGENDAMENTO 1 -->
            <div class="appointment">
                <div class="time">
                    <strong>08:00</strong>
                    <span>1h 30min</span>
                </div>

                <div class="appointment-info">
                    <div class="client-icon">
                        <i class="fa-solid fa-user"></i>
                    </div>

                    <div>
                        <strong>João da Silva</strong>

                        <span>
                            Troca de óleo e filtros
                        </span>

                        <small>
                            <i class="fa-solid fa-car"></i>
                            Honda Civic • ABC-1234
                        </small>
                    </div>
                </div>

                <span class="status scheduled">
                    Agendado
                </span>

                <button class="more">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
            </div>


            <!-- AGENDAMENTO 2 -->
            <div class="appointment">
                <div class="time">
                    <strong>10:00</strong>
                    <span>2h</span>
                </div>

                <div class="appointment-info">
                    <div class="client-icon">
                        <i class="fa-solid fa-user"></i>
                    </div>

                    <div>
                        <strong>Marcos Oliveira</strong>

                        <span>
                            Revisão completa
                        </span>

                        <small>
                            <i class="fa-solid fa-car"></i>
                            Toyota Corolla • DEF-5678
                        </small>
                    </div>
                </div>

                <span class="status progress">
                    Em andamento
                </span>

                <button class="more">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
            </div>


            <!-- AGENDAMENTO 3 -->
            <div class="appointment">
                <div class="time">
                    <strong>13:30</strong>
                    <span>1h</span>
                </div>

                <div class="appointment-info">

                    <div class="client-icon">
                        <i class="fa-solid fa-user"></i>
                    </div>

                    <div>
                        <strong>Lucas Ferreira</strong>

                        <span>
                            Alinhamento e balanceamento
                        </span>

                        <small>
                            <i class="fa-solid fa-car"></i>
                            Volkswagen Jetta • GHI-9012
                        </small>
                    </div>
                </div>

                <span class="status scheduled">
                    Agendado
                </span>

                <button class="more">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
            </div>


            <!-- AGENDAMENTO 4 -->
            <div class="appointment">
                <div class="time">
                    <strong>15:30</strong>
                    <span>2h</span>
                </div>

                <div class="appointment-info">

                    <div class="client-icon">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div>
                        <strong>Rafael Santos</strong>

                        <span>
                            Manutenção dos freios
                        </span>

                        <small>
                            <i class="fa-solid fa-car"></i>
                            Chevrolet Onix • JKL-3456
                        </small>
                    </div>

                </div>

                <span class="status scheduled">
                    Agendado
                </span>

                <button class="more">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
            </div>
        </section>
    </main>
</body>
</html>