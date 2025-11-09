<?php
session_start();
require_once 'conexao.php';
require_once 'csrf.php';

// Verifica se o usuário é administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'adm') {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Representante (ADM)</title>
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
            <form action="processa_cadastro_representante.php" method="POST" enctype="multipart/form-data">
                <?= csrf_field(); ?>

                <legend>Nome Completo:</legend>
                <input type="text" name="nome" required>

                <legend>Curso:</legend>
                <select name="curso" required>
                    <option value="">Selecione</option>
                    <option value="Gestão Empresarial">Gestão Empresarial</option>
                    <option value="Gestão Industrial">Gestão Industrial</option>
                    <option value="Desenvolvimento de Software">Desenvolvimento de Software</option>
                </select>

                <legend>Semestre:</legend>
                <select name="semestre" required>
                    <option value="">Selecione</option>
                    <?php for ($i=1; $i<=6; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?>º</option>
                    <?php endfor; ?>
                </select>

                <legend>RA:</legend>
                <input type="text" name="ra" required>

                <legend>Ano:</legend>
                <input type="number" name="ano" min="2000" max="2100" required>

                <button type="submit">Cadastrar Representante</button>
            </form>

            <div class="botao-voltar">
                <a href="menu_adm.php" class="voltar">
                    <img src="img/voltar1.png" alt="Voltar">
                </a>
            </div>
        </div>
    </main>

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
