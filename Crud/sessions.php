<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function usuarioLogado(): bool
{
    return isset($_SESSION['usuario']) && isset($_SESSION['usuario']['id_usuario']);
}

function idUsuarioLogado(): ?int
{
    return usuarioLogado() ? (int) $_SESSION['usuario']['id_usuario'] : null;
}

function salvarUsuarioNaSessao(array $usuario): void
{
    $_SESSION['usuario'] = [
        'id_usuario' => (int) $usuario['id_usuario'],
        'nome' => $usuario['nome'] ?? '',
        'email' => $usuario['email'] ?? '',
        'perfil_usuario' => $usuario['perfil_usuario'] ?? 'cliente',
        'cep' => $usuario['cep'] ?? null,
        'cidade' => $usuario['cidade'] ?? null,
        'estado' => $usuario['estado'] ?? null
    ];

    $_SESSION['logado'] = [
        'id' => (int) $usuario['id_usuario'],
        'usuario' => $usuario['nome'] ?? '',
        'email' => $usuario['email'] ?? ''
    ];

    if (!empty($usuario['cep'])) {
        $_SESSION['cep_index'] = $usuario['cep'];
    }
}

function buscarOuCriarCarrinhoAberto(PDO $pdo, int $idUsuario): int
{
    $result = $pdo->query("SELECT id_carrinho FROM carrinho WHERE idUsuario = $idUsuario AND status_carrinho = 'aberto' ORDER BY id_carrinho DESC LIMIT 1");
    $carrinho = $result->fetch(PDO::FETCH_ASSOC);

    if ($carrinho) {
        return (int) $carrinho['id_carrinho'];
    }

    $pdo->query("INSERT INTO carrinho (idUsuario, status_carrinho) VALUES ($idUsuario, 'aberto')");
    return (int) $pdo->lastInsertId();
}

function sincronizarCarrinhoSessaoComBanco(PDO $pdo, int $idUsuario): void
{
    if (empty($_SESSION['carrinho']) || !is_array($_SESSION['carrinho'])) {
        return;
    }

    foreach ($_SESSION['carrinho'] as $idProduto => $quantidade) {
        adicionarProdutoCarrinhoBanco($pdo, $idUsuario, (int) $idProduto, (int) $quantidade);
    }

    unset($_SESSION['carrinho']);
}

function adicionarProdutoCarrinhoBanco(PDO $pdo, int $idUsuario, int $idProduto, int $quantidade): void
{
    $quantidade = max(1, $quantidade);
    $idCarrinho = buscarOuCriarCarrinhoAberto($pdo, $idUsuario);

    $result = $pdo->query("SELECT preco_unitario FROM produto WHERE id_produto = $idProduto AND status_produto = 'ativo'");
    $produto = $result->fetch(PDO::FETCH_ASSOC);

    if (!$produto) {
        throw new Exception('Produto não encontrado ou inativo.');
    }

    $preco = (float) $produto['preco_unitario'];

    $result = $pdo->query("SELECT id_item_carrinho, quantidade FROM item_carrinho WHERE idCarrinho = $idCarrinho AND idProduto = $idProduto");
    $item = $result->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        $novaQuantidade = (int) $item['quantidade'] + $quantidade;
        $subtotal = $novaQuantidade * $preco;
        $pdo->query("UPDATE item_carrinho SET quantidade = $novaQuantidade, preco_unitario = $preco, subtotal = $subtotal WHERE id_item_carrinho = {$item['id_item_carrinho']}");
    } else {
        $subtotal = $quantidade * $preco;
        $pdo->query("INSERT INTO item_carrinho (idCarrinho, idProduto, quantidade, preco_unitario, subtotal) VALUES ($idCarrinho, $idProduto, $quantidade, $preco, $subtotal)");
    }
}

function removerProdutoCarrinhoBanco(PDO $pdo, int $idUsuario, int $idProduto): void
{
    $idCarrinho = buscarOuCriarCarrinhoAberto($pdo, $idUsuario);
    $pdo->query("DELETE FROM item_carrinho WHERE idCarrinho = $idCarrinho AND idProduto = $idProduto");
}

function contarItensCarrinho(PDO $pdo): int
{
    if (usuarioLogado()) {
        $idCarrinho = buscarOuCriarCarrinhoAberto($pdo, idUsuarioLogado());
        $result = $pdo->query("SELECT COALESCE(SUM(quantidade), 0) AS total FROM item_carrinho WHERE idCarrinho = $idCarrinho");
        return (int) $result->fetch(PDO::FETCH_ASSOC)['total'];
    }

    return isset($_SESSION['carrinho']) && is_array($_SESSION['carrinho']) ? array_sum($_SESSION['carrinho']) : 0;
}

function buscarItensCarrinhoAtual(PDO $pdo): array
{
    if (usuarioLogado()) {
        $idCarrinho = buscarOuCriarCarrinhoAberto($pdo, idUsuarioLogado());
        $result = $pdo->query("
            SELECT ic.id_item_carrinho,
                   ic.quantidade,
                   ic.preco_unitario,
                   ic.subtotal,
                   p.*,
                   MIN(f.caminho_imagem) AS caminho_imagem
            FROM item_carrinho ic
            JOIN produto p ON p.id_produto = ic.idProduto
            LEFT JOIN foto_produto f ON f.idProduto = p.id_produto
            WHERE ic.idCarrinho = $idCarrinho
            GROUP BY ic.id_item_carrinho, p.id_produto
        ");
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    $carrinhoSessao = $_SESSION['carrinho'] ?? [];
    if (empty($carrinhoSessao)) {
        return [];
    }

    $ids = implode(',', array_map('intval', array_keys($carrinhoSessao)));
    $result = $pdo->query("
        SELECT p.*, MIN(f.caminho_imagem) AS caminho_imagem
        FROM produto p
        LEFT JOIN foto_produto f ON p.id_produto = f.idProduto
        WHERE p.id_produto IN ($ids)
        GROUP BY p.id_produto
    ");
    $produtos = $result->fetchAll(PDO::FETCH_ASSOC);

    foreach ($produtos as &$produto) {
        $idProduto = (int) $produto['id_produto'];
        $produto['quantidade'] = (int) $carrinhoSessao[$idProduto];
        $produto['subtotal'] = (float) $produto['preco_unitario'] * (int) $produto['quantidade'];
    }

    return $produtos;
}

