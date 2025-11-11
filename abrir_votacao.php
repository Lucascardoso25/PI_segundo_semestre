<?php
session_start();
require_once 'conexao.php';
require_once 'csrf.php';

// Verifica se é administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'adm') {
    header('Location: index.php');
    exit;
}

// Pega dados do usuário (foto)
$id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT foto FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$u = $stmt->fetch(PDO::FETCH_ASSOC);

// Caminho da imagem
$foto = (!empty($u['foto']) && file_exists($u['foto'])) ? $u['foto'] : "img/perfil-padrao.png";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Abrir Votação</title>
<link rel="stylesheet" href="css/estilo-abrir-votacao.css">
</head>
<body>


<!-- CABEÇALHO -->
<div class="header">
    <!-- FOTO DO ADM -->
    <form id="form-foto" action="processa_editar_perfil.php" method="POST" enctype="multipart/form-data">
        <?= csrf_field(); ?>
        <label for="fotoInput">
            <img src="<?= htmlspecialchars($foto) ?>" class="avatar" alt="Foto de perfil" title="Clique para alterar foto">
        </label>
        <input type="file" name="foto" id="fotoInput" accept="image/*" style="display:none;" onchange="document.getElementById('form-foto').submit();">
    </form>

    <!-- TÍTULO -->
    <h2>Eleição Representante de Sala</h2>
</div>

<!-- LOGOS ABAIXO -->
<header>
    <div class="logos">
        <img src="img/logo-fatec.png" alt="logo_fatec" width="250">
    </div>
</header>
<main>
    <div class="caixa">
        <h2>Abrir Nova Votação</h2>
        <form action="processa_abrir_votacao.php" method="POST">
            <?= csrf_field(); ?>

            <label for="curso">Curso:</label>
            <select name="curso" id="curso" required>
                <option value="">Selecione</option>
                <option value="Gestão Empresa">Gestão Empresa</option>
                <option value="Gestão Industrial">Gestão Industrial</option>
                <option value="Desenvolvimento de Software">Desenvolvimento de Software</option>
            </select>

            <label for="semestre">Semestre:</label>
            <select name="semestre" id="semestre" required>
                <option value="">Selecione</option>
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?>º</option>
                <?php endfor; ?>
            </select>

            <label for="ano">Ano:</label>
            <input type="number" name="ano" id="ano" min="2000" max="2100" required>

            <label for="inicio">Início:</label>
            <input type="datetime-local" name="inicio" id="inicio" required>

            <label for="fim">Fim:</label>
            <input type="datetime-local" name="fim" id="fim" required>

            <button type="submit" class="btn" onclick="return confirmarAbertura()">Abrir Votação</button>
        </form>

        <div class="botao-voltar">
            <a href="menu_adm.php" class="voltar">
                <img src="img/voltar1.png" alt="Voltar">
            </a>
        </div>
    </div>
</main>

<footer>
    <img class="logo-sp" src="img/logo-saopaulo.png" alt="Governo SP">
    <div class="desenvolvido">
        <p><i>Desenvolvido por</i></p>
        <img src="img/webvote.png" alt="WebVote">
    </div>
</footer>

<script>
function confirmarAbertura() {
    return confirm("Tem certeza de que deseja abrir esta votação?");
}
</script>

</body>
</html>
