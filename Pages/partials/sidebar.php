<?php
require_once '../../Crud/crud.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$paginaAtual = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="../partials-css/sidebar.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<aside class="sidebar">

    <div class="logo">

        <div class="logo-image">

            <img
                src="../imagens/logo-prisma.png"
                alt="Prisma Mecânica"
            >

        </div>

    </div>

    <nav class="menu">

        <a
            href="dashboard.php"
            class="menu-item <?= $paginaAtual === 'dashboard.php' ? 'active' : '' ?>"
        >
            <i class="fa-solid fa-house"></i>
            <span>Dashboard</span>
        </a>

        <a
            href="agendamento.php"
            class="menu-item <?= in_array($paginaAtual, ['agendamento.php', 'cadastrar-agendamento.php', 'novo-agendamento.php'], true) ? 'active' : '' ?>"
        >
            <i class="fa-solid fa-calendar-days"></i>
            <span>Agendamentos</span>
        </a>

        <a
            href="estoque.php"
            class="menu-item <?= in_array($paginaAtual, ['estoque.php', 'cadastrar-produto.php', 'editar-produto.php'], true) ? 'active' : '' ?>"
        >
            <i class="fa-solid fa-box"></i>
            <span>Estoque</span>
        </a>

        <a
            href="financeiro.php"
            class="menu-item <?= $paginaAtual === 'financeiro.php' ? 'active' : '' ?>"
        >
            <i class="fa-solid fa-dollar-sign"></i>
            <span>Financeiro</span>
        </a>

        <a
            href="dados-oficina.php"
            class="menu-item <?= $paginaAtual === 'dados-oficina.php' ? 'active' : '' ?>"
        >
            <i class="fa-solid fa-building"></i>
            <span>Dados da Oficina</span>
        </a>

        <a
            href="seguranca.php"
            class="menu-item <?= $paginaAtual === 'seguranca.php' ? 'active' : '' ?>"
        >
            <i class="fa-solid fa-shield-halved"></i>
            <span>Segurança</span>
        </a>

        <?php if (isset($_SESSION['usuario']) && ($_SESSION['usuario']['tipo'] ?? '') === 'gerente'): ?>

            <a
                href="configuracoes.php"
                class="menu-item <?= in_array($paginaAtual, ['configuracoes.php', 'cadastrar-funcionario.php'], true) ? 'active' : '' ?>"
            >
                <i class="fa-solid fa-gear"></i>
                <span>Configurações</span>
            </a>

        <?php endif; ?>

    </nav>

    <div class="sidebar-bottom">

        <div class="user">

            <div class="user-icon">
                <i class="fa-solid fa-user"></i>
            </div>

            <div class="user-info">

                <strong>
                    <?= htmlspecialchars(
                        $_SESSION['usuario']['nome'] ?? 'Administrador',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>

                <small>
                    Sistema de Gestão
                </small>

            </div>

        </div>

        <a
            href="logout.php"
            class="logout"
            onclick="return confirm('Deseja realmente sair do sistema?')"
        >
            <i class="fa-solid fa-right-from-bracket"></i>

            <span>
                Sair
            </span>
        </a>

    </div>

</aside>