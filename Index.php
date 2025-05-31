<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require_once 'conexao.php';
session_start();

$topico_tl = $_SESSION['utilizador']['topico'] ?? 'Desconhecido';
$id_publicador = $_SESSION['utilizador']['id'] ?? null;
$nome = $_SESSION['utilizador']['nome'] ?? 'Desconhecido';
$id_topico = $_SESSION['utilizador']['id_topico'] ?? null;

if (isset($_POST['publicar'])) {

    $titulo = $_POST['titulo'] ?? '';
    $conteudo = $_POST['conteudo'] ?? '';
    $id_topico = $_POST['id_topico'] ?? '';

       //Usando o singleton para instanciar a conexao
$pdo = Conexao::getInstance()->getPDO();
    
   
    if ($titulo && $conteudo && $id_topico && $id_publicador) {
       
        $stmt = $pdo->prepare("INSERT INTO noticias (titulo, conteudo, id_topico, id_publicador) VALUES (?, ?, ?, ?)");
        $stmt->execute([$titulo, $conteudo, $id_topico, $id_publicador]);

        $stmt = $pdo->prepare("
            SELECT email, nome 
            FROM utilizador 
            WHERE id IN (
                SELECT id_utilizador 
                FROM inscricoes 
                WHERE id_publicador = ?
            )
        ");
        $stmt->execute([$id_publicador]);
        $destinatarios = $stmt->fetchAll();

        // Enviar e-mail\\
        foreach ($destinatarios as $dest) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'seuEmail@gmail.com';
                $mail->Password   = 'seuPassword';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('seuEmail@gmail.com', 'Noticias');
                $mail->addAddress($dest['email'], $dest['nome']);

                $mail->isHTML(true);
                $mail->Subject = "Nova notícia de $nome: $titulo";
                $mail->Body    = nl2br($conteudo);
                $mail->AltBody = $conteudo;

                $mail->send();
            } catch (Exception $e) {
                echo "Erro ao enviar para {$dest['email']}: {$mail->ErrorInfo}<br>";
            }
        }

        ?>
        <script>
            alert("Noticia publicada com sucesso");
        </script>
        <?php
    } else {
        echo "<script>alert('Erro: Dados incompletos para publicar noticia');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicação de Artigos</title>
    <link rel="stylesheet" href="style.css">
    <script defer src="Events.js"></script>
</head>
<body>
    <header>
        <ul class="ul-left">
            <li>
                <p>Publicação de artigos</p>
                <span>Publisher</span>
                <span><a href="PublisherLogin.php">Sair</a></span>
            </li>
            <li>
                <ul class="ul-right">
                    <li><h4>Publisher: <strong><?= htmlspecialchars($nome) ?></strong></h4></li>
                    <li><h4>Tópico: <strong><?= htmlspecialchars($topico_tl) ?></strong></h4></li>
                </ul>
            </li>
        </ul>
    </header>

    <div class="pub-conteiner" id="pub-conteiner">
        <form action="" method="POST">
            <strong>Partilhe algo novo</strong>
            <div class="line"></div>
            <div class="postInsert">
                <label class="tituloPost" for="Titulo">Tema</label><br>
                <input name="titulo" type="text" required><br><br>
                <input type="hidden" name="id_topico" value="<?= $id_topico ?>">

                <label class="tituloPost" for="descricao">Descrição</label><br>
                <textarea name="conteudo" required></textarea><br><br>

                <button name="publicar" type="submit">Publicar</button>
            </div>
        </form>
    </div>
</body>
</html>
