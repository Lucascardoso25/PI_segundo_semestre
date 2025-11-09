<?php
session_start();
require_once 'conexao.php';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'];

    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nome'] = $user['nome'];
        $_SESSION['user_tipo'] = $user['tipo'];

        if ($user['tipo'] === 'adm') {
            header('Location: menu_adm.php');
        } else {
            header('Location: menu_aluno.php');
        }
        exit;
    } else {
        $erro = 'Credenciais inválidas.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Eleição Representante de Sala</title>
  <link rel="stylesheet" href="css/estilo-login.css" />
</head>
<body>
  <!-- Cabeçalho -->
  <div class="header">
      <h2>Eleição Representante de Sala</h2>
  </div>

  <header>
      <div class="topo">
          <img src="img/logo-fatec.png" alt="logo_fatec" width="250">
      </div>        
  </header>

  <!-- Formulário de Login -->
  <div class="container">
    <div class="card">
      <h2>Login</h2>
      <?php if ($erro) echo '<p class="erro">'.htmlspecialchars($erro).'</p>'; ?>
      <form method="post" action="index.php">
        <label for="email">E-mail:</label>
        <input type="email" name="email" id="email" required />
        
        <label for="senha">Senha:</label>
        <input type="password" name="senha" id="senha" required />
        
        <div class="botoes">
          <button class="btn" type="submit">Login</button>
          <a href="cadastro.php" class="btn btn-secondary">Cadastrar</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Rodapé -->
  <footer>
      <img class="logo-sp" src="img/logo-saopaulo.png" alt="Governo SP">
      <div class="desenvolvido">
          <p><i>Desenvolvido por</i></p>
          <img src="img/webvote.png" alt="WebVote">
      </div>
  </footer>
</body>
</html>

