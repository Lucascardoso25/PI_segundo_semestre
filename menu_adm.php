<?php
session_start();
require_once 'conexao.php';
require_once 'csrf.php';

// Verifica se o usuário é administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'adm') {
    header('Location: index.php');
    exit;
}

// Pega dados do usuário (foto e RA)
$id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT foto, ra FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$u = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Menu ADM - Eleição Representante de Sala</title>
<link rel="stylesheet" href="css/estilo-adm.css">

<style>
.top-left {
    position: fixed;
    top: 15px;
    left: 15px;
}
.avatar {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid #444;
}
</style>
</head>
<body>

<!-- Foto do Usuário -->
<div class="top-left">
    <form id="form-foto" action="processa_editar_perfil.php" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <label for="fotoInput">
            <?php if (!empty($u['foto']) && file_exists($u['foto'])): ?>
                <img src="<?= htmlspecialchars($u['foto']) ?>" class="avatar" alt="Foto de perfil" title="Clique para alterar">
            <?php else: ?>
                <img src="img/perfil-padrao.png" class="avatar" alt="Sem foto" title="Clique para adicionar foto">
            <?php endif; ?>
        </label>
        <input type="file" name="foto" id="fotoInput" accept="image/*" style="display:none;" onchange="document.getElementById('form-foto').submit();">
    </form>
</div>

<!-- Cabeçalho -->
<div class="header">
    <h2>Eleição Representante de Sala</h2>
</div>
<header>
    <div class="logos">
        <img src="img/logo-fatec.png" alt="logo_fatec" width="250">
    </div>
</header>

<main>
    <div class="caixa">
        <button onclick="window.location.href='abrir_votacao.php'">Abrir Votação</button>
        <button onclick="window.location.href='encerrar_votacao.php'">Encerrar Votação</button>
        <button onclick="window.location.href='consulta_candidatos.php'">Consultar Candidatos</button>
        <button onclick="window.location.href='cadastrar_representante_adm.php'">Cadastro de Representante</button>
        <button onclick="window.location.href='gerar_ata.php'">Gerar Ata</button>
    </div>

    <div class="botao-voltar">
        <a href="index.php" class="voltar">
            <img src="img/voltar1.png" alt="Voltar">
        </a>
    </div>
</main>

<footer>
    <img class="logo-sp" src="img/logo-saopaulo.png" alt="Governo SP">
    <div class="desenvolvido">
        <p><i>Desenvolvido por</i></p>
        <img src="img/webvote.png" alt="WebVote">
    </div>
</footer>
</body>
</html>
