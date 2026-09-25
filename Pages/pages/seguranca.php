<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Prisma - Segurança</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../CSS/seguranca.css">
</head>

<body>

    
<?php require_once "sidebar.php" ?>
    <main class="main">

        <?php require_once "./partials/header.php" ?>


        <section class="page-title">

            <h1>Segurança</h1>

            <p>
                Gerencie a segurança e o acesso ao sistema.
            </p>

        </section>


        <section class="security-container">


            <div class="security-card">

                <div class="card-title">

                    <div class="title-icon">
                        <i class="fa-solid fa-lock"></i>
                    </div>

                    <div>
                        <h2>Alterar senha</h2>
                        <p>
                            Atualize a senha da sua conta de administrador.
                        </p>
                    </div>

                </div>


                <form>

                    <div class="form-group">

                        <label>Senha atual</label>

                        <div class="password-input">

                            <input
                                type="password"
                                placeholder="Digite sua senha atual"
                            >

                            <i class="fa-regular fa-eye"></i>

                        </div>

                    </div>


                    <div class="form-group">

                        <label>Nova senha</label>

                        <div class="password-input">

                            <input
                                type="password"
                                placeholder="Digite a nova senha"
                            >

                            <i class="fa-regular fa-eye"></i>

                        </div>

                    </div>


                    <div class="form-group">

                        <label>Confirmar nova senha</label>

                        <div class="password-input">

                            <input
                                type="password"
                                placeholder="Digite novamente a nova senha"
                            >

                            <i class="fa-regular fa-eye"></i>

                        </div>

                    </div>


                    <button class="btn-save">
                        <i class="fa-solid fa-check"></i>
                        Alterar senha
                    </button>

                </form>

            </div>


            

            <div class="security-card">

                <div class="card-title">

                    <div class="title-icon">
                        <i class="fa-solid fa-desktop"></i>
                    </div>

                    <div>
                        <h2>Sessões ativas</h2>
                        <p>
                            Dispositivos conectados à sua conta.
                        </p>
                    </div>

                </div>


                <div class="session">

                    <div class="session-icon">
                        <i class="fa-solid fa-laptop"></i>
                    </div>

                    <div class="session-info">

                        <strong>Computador atual</strong>

                        <span>
                            Chrome • Windows
                        </span>

                    </div>

                    <span class="current">
                        Sessão atual
                    </span>

                </div>


                <div class="session">

                    <div class="session-icon">
                        <i class="fa-solid fa-mobile-screen"></i>
                    </div>

                    <div class="session-info">

                        <strong>Dispositivo móvel</strong>

                        <span>
                            Chrome • Android
                        </span>

                    </div>

                    <button class="btn-remove">
                        Encerrar
                    </button>

                </div>

            </div>


            <div class="security-card">

                <div class="card-title">

                    <div class="title-icon">
                        <i class="fa-solid fa-list"></i>
                    </div>

                    <div>
                        <h2>Atividade recente</h2>
                        <p>
                            Últimas atividades realizadas na conta.
                        </p>
                    </div>

                </div>


                <div class="activity">

                    <div class="activity-icon">
                        <i class="fa-solid fa-right-to-bracket"></i>
                    </div>

                    <div>
                        <strong>Login realizado</strong>
                        <span>Hoje às 08:32</span>
                    </div>

                </div>


                <div class="activity">

                    <div class="activity-icon">
                        <i class="fa-solid fa-key"></i>
                    </div>

                    <div>
                        <strong>Senha alterada</strong>
                        <span>Ontem às 15:20</span>
                    </div>

                </div>


            </div>

        </section>

    </main>

</body>
</html>