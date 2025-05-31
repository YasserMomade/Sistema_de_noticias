<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['utilizador'])) {
    header("Location: Login.php");
    exit;
}

$id_utilizador = $_SESSION['utilizador']['id'];

   //Usando o singleton para instanciar a conexao
$pdo = Conexao::getInstance()->getPDO();

// Mensagens
if (isset($_SESSION['mensagem'])) {
    echo "<script>alert('" . $_SESSION['mensagem'] . "');</script>";
    unset($_SESSION['mensagem']);
}

if (isset($_SESSION['erro'])) {
    echo "<script>alert('" . $_SESSION['erro'] . "');</script>";
    unset($_SESSION['erro']);
}

// chamar oublicadores
$stmt = $pdo->prepare("
    SELECT u.id AS id_publicador, u.nome AS publisher_nome, t.id AS id_topico, t.topico 
    FROM utilizador u 
    JOIN topico t ON u.id = t.id_publicador 
    WHERE u.perfil = 'publicador'
");
$stmt->execute();
$publicadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// mmostar as inscricoes do cara
$stmt = $pdo->prepare("SELECT id_publicador FROM inscricoes WHERE id_utilizador = ?");
$stmt->execute([$id_utilizador]);
$inscricoes = $stmt->fetchAll(PDO::FETCH_COLUMN);

// criar inscricao
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subscrever'])) {
    $id_publicador = $_POST['subscrever'];
    try {
        $stmt = $pdo->prepare("INSERT INTO inscricoes (id_utilizador, id_publicador) VALUES (:id_utilizador, :id_publicador)");
        $stmt->execute([
            ':id_utilizador' => $id_utilizador,
            ':id_publicador' => $id_publicador
        ]);
        $_SESSION['mensagem'] = "Subscrição realizada com sucesso!";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['erro'] = 'Você já está inscrito neste publicador.';
        } else {
            $_SESSION['erro'] = "Erro: " . $e->getMessage();
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Cancelar inscricao
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancelar'])) {
    $id_publicador = $_POST['cancelar'];
    try {
        $stmt = $pdo->prepare("DELETE FROM inscricoes WHERE id_utilizador = :id_utilizador AND id_publicador = :id_publicador");
        $stmt->execute([
            ':id_utilizador' => $id_utilizador,
            ':id_publicador' => $id_publicador
        ]);
        $_SESSION['mensagem'] = "Inscrição cancelada com sucesso!";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } catch (PDOException $e) {
        $_SESSION['erro'] = "Erro ao cancelar inscrição: " . $e->getMessage();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Busca noticias
$noticias = [];
if (!empty($inscricoes)) {
    $in = str_repeat('?,', count($inscricoes) - 1) . '?';
    $stmt = $pdo->prepare("
        SELECT n.*, t.topico, u.nome AS publisher_nome
        FROM noticias n
        JOIN topico t ON n.id_topico = t.id
        JOIN utilizador u ON t.id_publicador = u.id
        WHERE t.id_publicador IN ($in)
        ORDER BY n.data_criacao DESC
    ");
    $stmt->execute($inscricoes);
    $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Minhas Subscripções</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <p>Publicação de artigos</p>
    <span>Subscriber</span>
    <span><a href="login.php">Sair</a></span>
</header>

<div class="sub-conteiner" id="sub-conteiner">
    <form action="" method="POST">
        <strong>Minhas subscrições</strong>
        <div class="line"></div>

        <div class="subscrever-content">
            <div class="subs-side">
                <strong>Subscrições</strong>
                <div>
                    <ul class="ul-subscriptions">
                        <?php foreach ($publicadores as $pub): ?>
                            <li>
                                <strong><?= htmlspecialchars($pub['topico']) ?> (Publisher: <?= htmlspecialchars($pub['publisher_nome']) ?>)</strong>
                                <?php if (in_array($pub['id_publicador'], $inscricoes)): ?>
                                    <button type="submit" name="cancelar" value="<?= $pub['id_publicador'] ?>">Cancelar</button>
                                <?php else: ?>
                                    <button type="submit" name="subscrever" value="<?= $pub['id_publicador'] ?>">Subscrever</button>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="publicacoes">
                <strong>Inbox de Notícias</strong>
                <p class="line"></p>

                <?php if (!empty($noticias)) : ?>
                    <?php foreach ($noticias as $noticia) : ?>
                        <div style="width: 96%; border: 1px solid rgba(0, 0, 0, 0.5); padding: 10px; 
                            margin-bottom: 10px; margin: 5px auto; border-radius: 5px;">
                            <h4><?= htmlspecialchars($noticia['titulo']) ?></h4>
                            <p><?= nl2br(htmlspecialchars($noticia['conteudo'])) ?></p>
                            <small><em><?= htmlspecialchars($noticia['topico']) ?> | <?= htmlspecialchars($noticia['publisher_nome']) ?> | <?= $noticia['data_criacao'] ?></em></small>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p style="margin: 0 1rem;">A sua lista de notícias está vazia.</p>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>
</body>
</html>
