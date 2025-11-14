<?php
session_start();
require_once 'conexao.php';
require_once 'csrf.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'aluno') {
    header('Location: login.php');
    exit;
}

// Puxa dados do aluno logado
$stmt = $pdo->prepare("SELECT nome, curso, semestre, ra, ano FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);

// Se não encontrar o aluno (não deveria acontecer)
if (!$aluno) {
    die("Erro: usuário não encontrado.");
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Representante</title>
    <link rel="stylesheet" href="estilo-cadastrar-representante.css">
</head>
<body>

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

        <form action="processa_cadastro_representante.php" method="POST">
            <?= csrf_field(); ?>

            <legend>Nome Completo:</legend>
            <input type="text" name="nome" value="<?= htmlspecialchars($aluno['nome']) ?>" readonly>

            <legend>Curso:</legend>
            <input type="text" name="curso" value="<?= htmlspecialchars($aluno['curso']) ?>" readonly>

            <legend>Semestre:</legend>
            <input type="text" name="semestre" value="<?= htmlspecialchars($aluno['semestre']) ?>º" readonly>

            <legend>RA:</legend>
            <input type="text" name="ra" value="<?= htmlspecialchars($aluno['ra']) ?>" readonly>

            <legend>Ano:</legend>
            <input type="number" name="ano" value="<?= htmlspecialchars($aluno['ano']) ?>" readonly>

            <br><br>
            <button type="submit">Cadastrar</button>
        </form>

        <div class="botao-voltar">
            <a href="menu_aluno.php" class="voltar">
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

</body>
</html>
