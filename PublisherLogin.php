<?php
session_start();
require_once 'conexao.php';
require_once 'utilizadorFactory.php';
require_once 'utilizadorDAO.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['logar'])) {
    $email = $_POST['publicador'] ?? '';
    $senha = $_POST['senha'] ?? '';

       //Usando o singleton para instanciar a conexao
        $pdo = Conexao::getInstance()->getPDO();

    $stmt = $pdo->prepare("SELECT * FROM utilizador WHERE email = ?");
    $stmt->execute([$email]);
    $dados = $stmt->fetch();

    if ($dados && $senha === $dados['senha'] && $dados['perfil'] === 'publicador') {
        
        
        $stmtTopico = $pdo->prepare("SELECT topico FROM topico WHERE id_publicador = ?");
        $stmtTopico->execute([$dados['id']]);
        $topico = $stmtTopico->fetchColumn();

    
        $stmt_topico = $pdo->prepare("SELECT id, topico FROM topico WHERE id_publicador = ?");
        $stmt_topico->execute([$dados['id']]);
        $topico = $stmt_topico->fetch();
        
        $_SESSION['utilizador'] = [
            'id' => $dados['id'],
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'topico' => $topico['topico'] ?? 'Nenhum',
            'id_topico' => $topico['id'] ?? null
        ];

    
        header("Location: index.php");
        exit;
    } else {
        ?>
        <script>
            alert("Email, senha incorretos ou perfil nao autorizado");
        </script>
        <?php
    }
}
}

    
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 if (isset($_POST['cadastrar'])) {
    $dados = [
        'nome' => $_POST['nome'],
        'email' => $_POST['email'],
        'senha' => $_POST['senha'],
        'topico' => $_POST['topico']
    ];

    $usuario = UtilizadorFactory::criar('publicador', $dados);
    $dao = new UtilizadorDAO();
    $dao->salvar($usuario);

    echo "Publicador cadastrado com sucesso!";
}
}
   




?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="style.css">
    <script defer src="Events.js"></script>
</head>
<body>
  
        <div class="main-conteiner">
  
        <main>
            <form action="" method="POST">
            <div class="login-conteiner" id="login-conteiner">
                <h1>Login</h1>
                <label for="user">Utilizador</label><br>
                <input class="login-input" id="user" type="text" name="publicador"><br><br>

                <label for="password">Senha</label><br>
                <input class="login-input" id="password" type="text" name="senha"><br><br>

                <h5 style="color: #0f5b99; cursor: pointer;" onclick="toogle_cadastro()">
                    Já possiu uma conta como publicador? <strong>Criar nova conta</strong>
                </h5>

                <button type="submit" name="logar">Logar</button>
            </form>


            <form action="" method="POST">
                <div class="publisher-login" id="publisher-login">
                        <h1 style="font: 30px; color: #dae7ec;" class="pub-login-title">
                            Cadastro 
                            <span style="font-size: 25px; font-weight: 100;">de publicadores</span>
                        </h1>
                        <div class="register-conteiner">
                            <img src="image/x.png" onclick="toogle_closeBox()" id="close"  class="close"><br><br><br><br><br>
                            <label for="">Nome</label><br>
                            <input class="register-input" type="text" name="nome"><br><br>

                            <label for="topicos">Topicos</label><br>
                            <div class="topicos" id="topicos">

                                <ul class="ul-topicos" >
                                    <li value="" class="li-topicos">
                                        <ul class="input-topico">
                                            <li>
                                                <input  
                                                    class="topic-input" 
                                                    type="text"
                                                    name="topico"
                                                    placeholder="Escreva o topico ou selecione"
                                                >
                                            </li>
                                            
                                            <li> <img onmouseenter="categoryEvt()"  id="down_icon" src="image/abaixo.png" > 
                                                 <img src="image/x.png" onmouseenter="toogleClose()" > </li>
                                        </ul>
                                    </li>
                                    <li value="" class="li-topicos">Academia</li>
                                    <li value="" class="li-topicos">Politica</li>
                                    <li value="" class="li-topicos">Saude</li>
                                    <li value="" class="li-topicos">Tecnologia</li>
                                    <li value="" class="li-topicos">Artes</li>
                                    <li value="" class="li-topicos">Alimentação</li>
                                </ul>
                            </div><br><br><br><br>

                            <label for="">Email</label><br>
                            <input class="register-input" type="email" name="email"><br><br>

                            <label for="">Criar senha</label><br>
                            <input class="register-input" type="password" name="senha">

                            <button class="btn_cadastrar" type="submit" name="cadastrar">Cadastrar</button>
                        </div>
                </div>
            </form> 
            </div>

        </main>
    </div>
</body>
</html>