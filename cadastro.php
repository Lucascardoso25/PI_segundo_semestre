<?php session_start(); if (isset($_SESSION['user_id'])) header('Location: menu_aluno.php'); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Cadastro</title>
<link rel="stylesheet" href="css/estilo-cadastro.css" /></head>
<body>
  <!-- Cabeçalho -->
  <div class="header">
      <h2>Tela de Cadastro </h2>
  </div>
  <header>
  <!-- Logos -->
   <header>
      <div class="logos">
          <img src="img/logo-fatec.png" alt="logo_fatec" width="250">
      </div> 
             
  </header>
  <div class="container">
    <div class="center-flex">
      <div class="card">
        <h2>Cadastre-se</h2>
        <form action="processa_cadastro.php" method="post" enctype="multipart/form-data">
          <?php require_once 'csrf.php'; echo csrf_field(); ?>
          <label>Nome completo</label><input type="text" name="nome" required minlength="3" />
          <label>RA</label><input type="text" name="ra" required />
          <label>Email</label><input type="email" name="email" required />
          <label>Senha</label><input type="password" name="senha" required minlength="6" />
          <label>Curso</label>
          <select name="curso" required>
            <option value="">-- selecione --</option>
            <option value="Gestão Empresa">Gestão Empresa</option>
            <option value="Gestão Industrial">Gestão Industrial</option>
            <option value="Desenvolvimento de Software">Desenvolvimento de Software</option>
          </select>
          <label>Semestre</label><input type="number" name="semestre" min="1" max="6" required />
          <label>Ano</label><input type="number" name="ano" min="2000" max="2100" required />
          <label>Foto (jpg/png, max 2MB)</label><input type="file" name="foto" accept=".jpg,.jpeg,.png" />
          <div style="margin-top:12px;">
            <button class="btn" type="submit">Cadastrar</button>
            <a href="index.php" class="btn btn-secondary" style="margin-left:8px;">Voltar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
<!-- Rodapé -->
  <footer>
      <img class="logo-sp" src="img/logo-saopaulo.png" alt="Governo SP">
      <div class="desenvolvido">
          <p><i>Desenvolvido por</i></p>
          <img src="img/webvote.png" alt="WebVote">
      </div>
  </footer>
</html>
