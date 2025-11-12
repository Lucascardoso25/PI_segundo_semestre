<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário é administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'adm') {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menu ADM - Eleição Representante de Sala</title>
    <link rel="stylesheet" href="css/estilo-adm.css">
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

            <form action="#" method="post">
                <button type="button" onclick="window.location.href='abrir_votacao.php'">
                    Abrir Votação
                </button>

                <button type="button" onclick="window.location.href='encerrar_votacao.php'">
                    Encerrar Votação
                </button>

                <button type="button" onclick="window.location.href='consulta_candidatos.php'">
                    Consultar Candidatos
                </button>

                <button type="button" onclick="window.location.href='cadastrar_representante_adm.php'">
                    Cadastro de Representante
                </button>


                <button type="button" onclick="window.location.href='gerar_ata.php'">
                    Gerar Ata
                </button>
            </form>
        </div>

        <div class="botao-voltar">
    <a href="logout.php" class="voltar" onclick="return confirm('Deseja realmente sair do sistema?');">
        <img src="img/sair.png" alt="Sair">
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
