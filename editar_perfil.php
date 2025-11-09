<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'aluno') { header('Location: index.php'); exit; }
require_once 'conexao.php';
$stmt = $pdo->prepare('SELECT ra, foto FROM usuarios WHERE id = ?'); $stmt->execute([$_SESSION['user_id']]); $u = $stmt->fetch();
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Editar Foto</title>
<link rel="stylesheet" href="css/estilo-votacao.css" /></head><body>
  <div class="topbar">
    <div class="logos"><img src="imagens/fatec_logo.png" alt="Fatec"><img src="imagens/cps_logo.png" alt="CPS"></div>
    <h1>Eleição Representante de Sala</h1>
  </div>
  <div class="container">
    <div class="center-flex"><div class="card">
      <h2>Editar Foto de Perfil</h2>
      <?php if (!empty($u['foto'])): ?><img src="<?= htmlspecialchars($u['foto']) ?>" class="avatar" alt="foto" /><?php else: ?><p>(sem foto)</p><?php endif; ?>
      <form action="processa_editar_perfil.php" method="post" enctype="multipart/form-data">
        <?php require_once 'csrf.php'; echo csrf_field(); ?>
        <label>Nova foto (jpg/png, max 2MB)</label><input type="file" name="foto" accept=".jpg,.jpeg,.png" required />
        <div style="margin-top:12px;"><button class="btn" type="submit">Atualizar Foto</button> <a class="btn btn-secondary" href="menu_aluno.php" style="margin-left:8px;">Voltar</a></div>
      </form>
    </div></div>
  </div>
</body></html>
