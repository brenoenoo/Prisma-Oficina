<link rel="stylesheet" href="../partials-css/header.css?v=1">

<header class="header">


    <div class="header-right">

        <div class="administrator">

            <div class="admin-icon">
                <i class="fa-solid fa-user"></i>
            </div>

            <span>
                <?= htmlspecialchars($_SESSION['usuario']['nome'] ?? 'Administrador') ?>
            </span>

            <i class="fa-solid fa-chevron-down"></i>

        </div>

    </div>

</header>

<script src="../javascript/pesquisa.js"></script>