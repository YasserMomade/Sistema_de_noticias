 <?php
session_start();
require_once 'conexao.php';
require_once 'utilizadorFactory.php';
require_once 'utilizadorDAO.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['logar'])) {

    $email = $_POST['utilizador'] ?? '';
    $senha = $_POST['senha'] ?? '';

    //Usando o singleton para instanciar a conexao
    $pdo = Conexao::getInstance()->getPDO();


    $stmt = $pdo->prepare("SELECT * FROM utilizador WHERE email = ?");
    $stmt->execute([$email]);
    $dados = $stmt->fetch();

    if ($dados && $senha === $dados['senha']) {
        
        $_SESSION['utilizador'] = [
            'id' => $dados['id'],
            'nome' => $dados['nome'],
            'email' => $dados['email']
        ];

        
        header("Location: Subscribers.php");
        exit;
    } else {
        ?>
        <script>
            alert("Email ou senha incorretos");
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
        'senha' => $_POST['senha']
    ];

    $usuario = UtilizadorFactory::criar('utilizador', $dados);
    $dao = new UtilizadorDAO();
    $dao->salvar($usuario);

    echo "Utilizador cadastrado com sucesso!";
}

}



?> 



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>


    <link rel="stylesheet" href="userRegister.css">
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
                <input class="login-input"  id="user" type="text" name="utilizador"><br><br>

                <label for="password">Senha</label><br>
                <input class="login-input" id="password" type="text" name="senha"><br><br>

                <h5 style="color: #0f5b99; cursor: pointer;" onclick="tgl_cadastro()">
                    Já possiu uma conta? <strong>Criar nova conta</strong>
                </h5>

                <button type="submit" name="logar">Logar</button>
            </form>
            </div>

        <div>
            <form action="" method="POST">
                <div class="cadastro-utilizador" id="cadastro-utilizador">
                        <h1 style="font: 30px; color: #dae7ec;" class="user-signUp-title">
                            Cadastro 
                            <span style="font-size: 25px; font-weight: 100;">de Utilizadores</span>
                        </h1><br>
                        <div class="cadastrar-utilizador">
                            <img src="image/x.png" onclick="toogle_closedBox()" id="closed"  class="closed"><br><br><br><br><br>
                            <label class="label" for="">Nome</label><br>
                            <input class="user-input" name="nome" type="text"><br><br>

                            <label class="label" for="">Email</label><br>
                            <input class="user-input" name="email" type="email"><br><br>

                            <label class="label" for="">Criar senha</label><br>
                            <input class="user-input" name="senha" type="text">

                            <button class="btn_cadastrar-user" type="submit" name="cadastrar">Cadastrar</button>
                        </div>
                </div>
            </form> 
        </div>

        </main>
    </div>
</body>
</html>