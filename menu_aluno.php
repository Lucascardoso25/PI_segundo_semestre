<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'aluno') {
    header('Location: index.php');
    exit;
}
require_once 'conexao.php';
$stmt = $pdo->prepare('SELECT foto FROM usuarios WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$u = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tela Menu</title>
  <link rel="stylesheet" href="css/estilo-menu.css">
</head>
<body>

  <!-- Cabeçalho -->
  <div class="header">
      <h2>Tela menu</h2>
  </div>
  <header>
  <!-- Logos -->
   <header>
      <div class="logos">
          <img src="img/logo-fatec.png" alt="logo_fatec" width="250">
      </div> 
             
  </header>

 <!-- Foto do Usuário -->
<div class="top-left">
  <form id="form-foto" action="atualizar_foto.php" method="POST" enctype="multipart/form-data">
    <label for="fotoInput">
      <?php if (!empty($u['foto'])): ?>
        <img src="<?= htmlspecialchars($u['foto']) ?>" class="avatar" alt="Foto de perfil" title="Clique para alterar">
      <?php else: ?>
        <img src="img/perfil-padrao.png" class="avatar" alt="Sem foto" title="Clique para adicionar foto">
      <?php endif; ?>
    </label>
    <input type="file" name="foto" id="fotoInput" accept="image/*" style="display:none;" onchange="document.getElementById('form-foto').submit();">
  </form>
</div>

    </header>

  <!-- Menu Central -->
  <div class="menu">
    <div class="menu-box">
      <a href="cadastrar_representante.php" class="link-btn">Representante</a>
      <a href="votar.php" class="link-btn votar">VOTAR</a>
    </div>
  </div>

  <!-- Botão Voltar -->
  <div class="botao-voltar">
    <a href="index.php">
      <img src="img/voltar1.png" alt="Voltar">
    </a>
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
