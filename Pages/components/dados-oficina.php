<?php

session_start();

require_once "../../Crud/crud.php";

if (empty($_SESSION['usuario'])) {
    header("Location: ../login/login.php");
    exit;
}

$mensagem = $_SESSION['mensagem'] ?? '';
$erro = $_SESSION['erro'] ?? '';

unset($_SESSION['mensagem'], $_SESSION['erro']);

$config = read(
    $pdo,
    'configuracoes_loja',
    '*',
    'id_config_loja = 1'
);

if (!$config) {
    $config = [
        'id_config_loja' => null,
        'nome' => '',
        'cnpj' => '',
        'telefone' => '',
        'email_comercial' => '',
        'endereco' => '',
        'cidade' => '',
        'estado' => '',
        'cep' => ''
    ];
}

$dias = [
    'segunda' => 'Segunda-feira',
    'terca' => 'Terça-feira',
    'quarta' => 'Quarta-feira',
    'quinta' => 'Quinta-feira',
    'sexta' => 'Sexta-feira',
    'sabado' => 'Sábado',
    'domingo' => 'Domingo'
];

$estados = [
    'AC' => 'Acre',
    'AL' => 'Alagoas',
    'AP' => 'Amapá',
    'AM' => 'Amazonas',
    'BA' => 'Bahia',
    'CE' => 'Ceará',
    'DF' => 'Distrito Federal',
    'ES' => 'Espírito Santo',
    'GO' => 'Goiás',
    'MA' => 'Maranhão',
    'MT' => 'Mato Grosso',
    'MS' => 'Mato Grosso do Sul',
    'MG' => 'Minas Gerais',
    'PA' => 'Pará',
    'PB' => 'Paraíba',
    'PR' => 'Paraná',
    'PE' => 'Pernambuco',
    'PI' => 'Piauí',
    'RJ' => 'Rio de Janeiro',
    'RN' => 'Rio Grande do Norte',
    'RS' => 'Rio Grande do Sul',
    'RO' => 'Rondônia',
    'RR' => 'Roraima',
    'SC' => 'Santa Catarina',
    'SP' => 'São Paulo',
    'SE' => 'Sergipe',
    'TO' => 'Tocantins'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $acao = $_POST['acao'] ?? '';

   

    if ($acao === 'salvar_oficina') {

        $nome = trim($_POST['nome'] ?? '');
        $cnpj = trim($_POST['cnpj'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $email = trim($_POST['email_comercial'] ?? '');
        $endereco = trim($_POST['endereco'] ?? '');
        $cidade = trim($_POST['cidade'] ?? '');
        $estado = trim($_POST['estado'] ?? '');
        $cep = trim($_POST['cep'] ?? '');

        if ($nome === '') {
            $_SESSION['erro'] = 'Informe o nome da oficina.';
            header("Location: dados-oficina.php");
            exit;
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['erro'] = 'Informe um e-mail comercial válido.';
            header("Location: dados-oficina.php");
            exit;
        }

        try {

            $dados = [
                'nome' => $nome,
                'cnpj' => $cnpj,
                'telefone' => $telefone,
                'email_comercial' => $email,
                'endereco' => $endereco,
                'cidade' => $cidade,
                'estado' => $estado,
                'cep' => $cep
            ];

            if (!empty($config['id_config_loja'])) {

                update(
                    $pdo,
                    'configuracoes_loja',
                    $dados,
                    'id_config_loja = ' . (int)$config['id_config_loja']
                );

            } else {

                create(
                    $pdo,
                    'configuracoes_loja',
                    $dados
                );
            }

            $_SESSION['mensagem'] = 'Dados da oficina salvos com sucesso.';

        } catch (PDOException $e) {

            $_SESSION['erro'] = 'Não foi possível salvar os dados da oficina.';
        }

        header("Location: dados-oficina.php");
        exit;
    }

    if ($acao === 'salvar_horarios') {

        if (empty($config['id_config_loja'])) {
            $_SESSION['erro'] = 'Primeiro cadastre os dados da oficina.';
            header("Location: dados-oficina.php");
            exit;
        }

        $idConfig = (int)$config['id_config_loja'];

        try {

            $pdo->beginTransaction();

            $horariosExistentes = readAll(
                $pdo,
                'horario_funcionamento',
                'id_configuracoes_loja = ' . $idConfig
            );

            $idsExistentes = [];

            foreach ($horariosExistentes as $horario) {
                $idsExistentes[$horario['dia_semana']] = $horario['id_horario'];
            }

            foreach ($dias as $chave => $dia) {

                $abertura = $_POST['abertura_' . $chave] ?? '00:00';
                $fechamento = $_POST['fechamento_' . $chave] ?? '00:00';

                $ativo = isset($_POST['ativo_' . $chave]) ? 1 : 0;

                if ($ativo === 0) {
                    $abertura = '00:00';
                    $fechamento = '00:00';
                }

                if (!preg_match('/^\d{2}:\d{2}$/', $abertura)) {
                    $abertura = '00:00';
                }

                if (!preg_match('/^\d{2}:\d{2}$/', $fechamento)) {
                    $fechamento = '00:00';
                }

                $dadosHorario = [
                    'id_configuracoes_loja' => $idConfig,
                    'dia_semana' => $dia,
                    'hora_abertura' => $abertura . ':00',
                    'hora_fechamento' => $fechamento . ':00',
                    'ativo' => $ativo
                ];

                if (isset($idsExistentes[$dia])) {

                    update(
                        $pdo,
                        'horario_funcionamento',
                        [
                            'hora_abertura' => $abertura . ':00',
                            'hora_fechamento' => $fechamento . ':00',
                            'ativo' => $ativo
                        ],
                        'id_horario = ' . (int)$idsExistentes[$dia]
                    );

                } else {

                    create(
                        $pdo,
                        'horario_funcionamento',
                        $dadosHorario
                    );
                }
            }

            $pdo->commit();

            $_SESSION['mensagem'] = 'Horários de funcionamento atualizados com sucesso.';

        } catch (PDOException $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $_SESSION['erro'] = 'Não foi possível salvar os horários.';
        }

        header("Location: dados-oficina.php");
        exit;
    }
}

$horarios = [];

if (!empty($config['id_config_loja'])) {

    $horariosBanco = readAll(
        $pdo,
        'horario_funcionamento',
        'id_configuracoes_loja = ' . (int)$config['id_config_loja']
    );

    foreach ($horariosBanco as $horario) {
        $horarios[$horario['dia_semana']] = $horario;
    }
}

function horarioDoDia($horarios, $dia)
{
    if (isset($horarios[$dia])) {
        return $horarios[$dia];
    }

    return [
        'hora_abertura' => '08:00:00',
        'hora_fechamento' => '18:00:00',
        'ativo' => 1
    ];
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Prisma - Dados da Oficina</title>

    <link rel="stylesheet" href="../css/dados-oficina.css">

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
            <i class="fa-solid fa-building"></i>
            CONFIGURAÇÕES
        </div>

        <h1>Dados da Oficina</h1>

        <p>
            Gerencie as informações e os dados da sua oficina.
        </p>

    </div>

    <?php if ($mensagem): ?>

        <div class="alert success">
            <i class="fa-solid fa-circle-check"></i>
            <?= htmlspecialchars($mensagem) ?>
        </div>

    <?php endif; ?>

    <?php if ($erro): ?>

        <div class="alert error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?= htmlspecialchars($erro) ?>
        </div>

    <?php endif; ?>

    <div class="card">

        <div class="card-header">

            <div class="card-icon">
                <i class="fa-solid fa-building"></i>
            </div>

            <div>

                <h2>Informações da oficina</h2>

                <p>
                    Essas informações serão utilizadas no sistema.
                </p>

            </div>

        </div>

        <form method="POST">

            <input
                type="hidden"
                name="acao"
                value="salvar_oficina"
            >

            <div class="form">

                <div class="input-group">

                    <label for="nome">
                        Nome da oficina
                    </label>

                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        value="<?= htmlspecialchars($config['nome'] ?? '') ?>"
                        placeholder="Nome da oficina"
                        required
                    >

                </div>

                <div class="input-group">

                    <label for="cnpj">
                        CNPJ
                    </label>

                    <input
                        type="text"
                        id="cnpj"
                        name="cnpj"
                        value="<?= htmlspecialchars($config['cnpj'] ?? '') ?>"
                        placeholder="00.000.000/0001-00"
                    >

                </div>

                <div class="input-group">

                    <label for="telefone">
                        Telefone
                    </label>

                    <input
                        type="text"
                        id="telefone"
                        name="telefone"
                        value="<?= htmlspecialchars($config['telefone'] ?? '') ?>"
                        placeholder="(00) 00000-0000"
                    >

                </div>

                <div class="input-group">

                    <label for="email_comercial">
                        E-mail comercial
                    </label>

                    <input
                        type="email"
                        id="email_comercial"
                        name="email_comercial"
                        value="<?= htmlspecialchars($config['email_comercial'] ?? '') ?>"
                        placeholder="contato@oficina.com"
                    >

                </div>

                <div class="input-group full">

                    <label for="endereco">
                        Endereço
                    </label>

                    <input
                        type="text"
                        id="endereco"
                        name="endereco"
                        value="<?= htmlspecialchars($config['endereco'] ?? '') ?>"
                        placeholder="Rua, avenida..."
                    >

                </div>

                <div class="input-group">

                    <label for="cep">
                        CEP
                    </label>

                    <input
                        type="text"
                        id="cep"
                        name="cep"
                        value="<?= htmlspecialchars($config['cep'] ?? '') ?>"
                        placeholder="00000-000"
                    >

                </div>

                <div class="input-group">

                    <label for="cidade">
                        Cidade
                    </label>

                    <input
                        type="text"
                        id="cidade"
                        name="cidade"
                        value="<?= htmlspecialchars($config['cidade'] ?? '') ?>"
                        placeholder="Cidade"
                    >

                </div>

                <div class="input-group">

                    <label for="estado">
                        Estado
                    </label>

                    <select
                        id="estado"
                        name="estado"
                    >

                        <option value="">
                            Selecione o estado
                        </option>

                        <?php foreach ($estados as $sigla => $nomeEstado): ?>

                            <option
                                value="<?= $sigla ?>"
                                <?= (($config['estado'] ?? '') === $sigla) ? 'selected' : '' ?>
                            >
                                <?= $nomeEstado ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

            </div>

            <div class="buttons">

                <button
                    type="reset"
                    class="cancel"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="save"
                >

                    <i class="fa-solid fa-check"></i>

                    Salvar alterações

                </button>

            </div>

        </form>

    </div>

    <div class="card">

        <div class="card-header">

            <div class="card-icon">
                <i class="fa-regular fa-clock"></i>
            </div>

            <div>

                <h2>Horário de funcionamento</h2>

                <p>
                    Defina os horários em que a oficina funciona.
                </p>

            </div>

        </div>

        <form method="POST">

            <input
                type="hidden"
                name="acao"
                value="salvar_horarios"
            >

            <div class="hours">

                <?php foreach ($dias as $chave => $diaNome): ?>

                    <?php

                    $horario = horarioDoDia(
                        $horarios,
                        $diaNome
                    );

                    $ativo = (int)$horario['ativo'] === 1;

                    $abertura = substr(
                        $horario['hora_abertura'],
                        0,
                        5
                    );

                    $fechamento = substr(
                        $horario['hora_fechamento'],
                        0,
                        5
                    );

                    ?>

                    <div class="day">

                        <span>
                            <?= htmlspecialchars($diaNome) ?>
                        </span>

                        <div>

                            <input
                                type="time"
                                name="abertura_<?= $chave ?>"
                                value="<?= htmlspecialchars($abertura) ?>"
                                <?= !$ativo ? 'disabled' : '' ?>
                            >

                            <b>até</b>

                            <input
                                type="time"
                                name="fechamento_<?= $chave ?>"
                                value="<?= htmlspecialchars($fechamento) ?>"
                                <?= !$ativo ? 'disabled' : '' ?>
                            >

                            <label>

                                <input
                                    type="checkbox"
                                    name="ativo_<?= $chave ?>"
                                    value="1"
                                    <?= $ativo ? 'checked' : '' ?>
                                    onchange="alternarHorario(this)"
                                >

                                Aberto

                            </label>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

            <div class="buttons">

                <button
                    type="submit"
                    class="save"
                >

                    <i class="fa-solid fa-check"></i>

                    Salvar horários

                </button>

            </div>

        </form>

    </div>


    </div>

</section>

</main>

<script>

function alternarHorario(checkbox) {

    const container = checkbox.closest('.day');
    const campos = container.querySelectorAll('input[type="time"]');

    campos.forEach(function(campo) {
        campo.disabled = !checkbox.checked;
    });
}

</script>

</body>

</html>