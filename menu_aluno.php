<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'aluno') {
    header('Location: index.php');
    exit;
}
require_once 'conexao.php';
require_once 'csrf.php';
$stmt = $pdo->prepare('SELECT foto FROM usuarios WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$u = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Menu Aluno</title>
<link rel="stylesheet" href="css/estilo-menu.css">
<style>
.top-left { position: fixed; top: 15px; left: 15px; }
.avatar { width: 70px; height: 70px; object-fit: cover; border-radius: 50%; cursor: pointer; border: 2px solid #444; }
</style>
</head>
<body>

<div class="top-left">
<form id="form-foto" action="processa_editar_perfil.php" method="POST" enctype="multipart/form-data">
  <?= csrf_field(); ?>
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

<!-- Menu e Rodapé continuam iguais -->

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

<footer>
    <img class="logo-sp" src="img/logo-saopaulo.png" alt="Governo SP">
    <div class="desenvolvido">
        <p><i>Desenvolvido por</i></p>
        <img src="img/webvote.png" alt="WebVote">
    </div>
</footer>

</body>
</html>
