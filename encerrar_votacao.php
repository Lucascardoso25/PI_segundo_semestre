<?php
session_start();
require_once 'conexao.php';
require_once 'csrf.php';

// Verifica se é administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'adm') {
    header('Location: index.php');
    exit;
}

// Encerrar votação selecionada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_votacao'])) {
    csrf_check();

    $id_votacao = intval($_POST['id_votacao']);

    try {
        // Atualiza a votação para encerrada (ativo = 0 e fim = data atual)
        $stmt = $pdo->prepare("UPDATE votacoes SET ativo = 0, fim = NOW() WHERE id = ?");
        $stmt->execute([$id_votacao]);

        echo "<script>
            alert('Votação encerrada com sucesso!');
            window.location.href = 'encerrar_votacao.php';
        </script>";
        exit;
    } catch (PDOException $e) {
        echo "Erro ao encerrar votação: " . $e->getMessage();
    }
}

// Busca todas as votações ativas (abertas)
try {
    $stmt = $pdo->query("SELECT id, curso, semestre, ano, inicio FROM votacoes WHERE ativo = 1 ORDER BY inicio DESC");
    $votacoes_abertas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $votacoes_abertas = [];
    echo "Erro ao buscar votações: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Encerrar Votação</title>
    <link rel="stylesheet" href="css/estilo-encerrar-votacao.css">
</head>
<body>
    <!-- Cabeçalho -->
    <div class="header">
        <h2>Encerrar Votação</h2>
    </div>

    <header>
        <div class="logos">
            <img src="img/logo-fatec.png" alt="Logo Fatec" width="250">
        </div>
    </header>

    <main>
        <div class="caixa">
            <h2>Votações Abertas</h2>

            <?php if (empty($votacoes_abertas)): ?>
                <p><strong>Nenhuma votação aberta no momento.</strong></p>
            <?php else: ?>
                <form method="POST" onsubmit="return confirmarEncerramento();">
                    <?= csrf_field(); ?>
                    <label for="id_votacao">Selecione uma votação:</label>
                    <select name="id_votacao" id="id_votacao" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($votacoes_abertas as $v): ?>
                            <option value="<?= $v['id'] ?>">
                                <?= htmlspecialchars($v['curso']) ?> —
                                <?= $v['semestre'] ?>º Semestre (<?= $v['ano'] ?>)
                                - Início: <?= date('d/m/Y H:i', strtotime($v['inicio'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <br><br>
                    <button type="submit">Encerrar Votação</button>
                </form>
            <?php endif; ?>

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

    <script>
    function confirmarEncerramento() {
        const select = document.getElementById('id_votacao');
        if (!select.value) {
            alert('Por favor, selecione uma votação.');
            return false;
        }
        return confirm('Tem certeza de que deseja encerrar esta votação?');
    }
    </script>
</body>
</html>
