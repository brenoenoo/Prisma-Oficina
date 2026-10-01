<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prisma - Login</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../CSS/login.css">
</head>

<body>

    <div class="login-container">
        <div class="logo">


            <div class="logo-image">
            <img src="./imagens/logo-prisma.png" alt="Logo da empresa">
        </div>
    
        </div>


        <div class="login-card">

            <div class="login-header">

                <h2>Bem-vindo</h2>

                <p>
                    Entre na sua conta para acessar o sistema.
                </p>

            </div>


            <form action="dashboard.html" method="POST">

                <div class="form-group">

                    <label for="email">
                        E-mail
                    </label>

                    <div class="input-box">

                        <i class="fa-regular fa-envelope"></i>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Digite seu e-mail"
                            required
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label for="senha">
                        Senha
                    </label>

                    <div class="input-box">

                        <i class="fa-solid fa-lock"></i>

                        <input
                            type="password"
                            id="senha"
                            name="senha"
                            placeholder="Digite sua senha"
                            required
                        >

                        <i
                            class="fa-regular fa-eye eye"
                            onclick="mostrarSenha()"
                        ></i>

                    </div>

                </div>


                <div class="login-options">

                    <label class="remember">

                        <input type="checkbox">

                        <span>Lembrar de mim</span>

                    </label>

                    <a href="#">
                        Esqueci minha senha
                    </a>

                </div>


                <button type="submit" class="login-button">

                    <i class="fa-solid fa-right-to-bracket"></i>

                    Entrar

                </button>

            </form>


            <div class="login-footer">

                <span>Prisma Mecânica</span>

                <p>
                    Sistema de Gestão
                </p>

            </div>

        </div>


        <div class="copyright">
            © 2026 Prisma Mecânica. Todos os direitos reservados.
        </div>

    </div>


    <?php require_once "javascript/senha.js" ?>

</body>

</html>