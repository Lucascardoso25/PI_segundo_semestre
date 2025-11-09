<?php
session_start();
require_once 'conexao.php';
require_once 'csrf.php';

// Verifica se é administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'adm') {
    header('Location: index.php');
    exit;
}

$ata_html = '';
$dados_votacao = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gerar'])) {
    $curso = $_POST['curso'];
    $semestre = intval($_POST['semestre']);
    $ano = intval($_POST['ano']);

    $stmt = $pdo->prepare('SELECT * FROM votacoes WHERE curso = ? AND semestre = ? AND ano = ?');
    $stmt->execute([$curso, $semestre, $ano]);
    $v = $stmt->fetch();

    if (!$v) {
        $ata_html = '<p><strong>Nenhuma votação encontrada.</strong></p>';
    } else {
        $stmt = $pdo->prepare('
            SELECT u.nome, u.ra, v.criado_em 
            FROM votos v 
            JOIN usuarios u ON v.votante_id = u.id 
            WHERE v.votacao_id = ?
        ');
        $stmt->execute([$v['id']]);
        $votantes = $stmt->fetchAll();

        $stmt = $pdo->prepare('
            SELECT r.nome, r.ra, COUNT(v.id) votos 
            FROM representantes r 
            LEFT JOIN votos v 
            ON r.id = v.candidato_id AND v.votacao_id = ? 
            WHERE r.curso = ? AND r.semestre = ? AND r.ano = ?
            GROUP BY r.id ORDER BY votos DESC
        ');
        $stmt->execute([$v['id'], $curso, $semestre, $ano]);
        $resultados = $stmt->fetchAll();

        ob_start();
        ?>
        <h2>ATA de Votação — <?= htmlspecialchars($curso . ' - ' . $semestre . 'º Semestre - ' . $ano) ?></h2>
        <p>Período: <?= htmlspecialchars($v['inicio']) ?> até <?= htmlspecialchars($v['fim']) ?></p>

        <h3>Votantes (<?= count($votantes) ?>)</h3>
        <ul>
            <?php foreach ($votantes as $vt): ?>
                <li><?= htmlspecialchars($vt['nome'] . ' (RA: ' . $vt['ra'] . ') — ' . $vt['criado_em']) ?></li>
            <?php endforeach; ?>
        </ul>

        <h3>Resultado Final</h3>
        <table>
            <thead>
                <tr><th>Nome</th><th>RA</th><th>Votos</th></tr>
            </thead>
            <tbody>
                <?php foreach ($resultados as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['nome']) ?></td>
                        <td><?= htmlspecialchars($r['ra']) ?></td>
                        <td><?= htmlspecialchars($r['votos']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        $ata_html = ob_get_clean();
        $dados_votacao = ['curso' => $curso, 'semestre' => $semestre, 'ano' => $ano];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gerar ATA</title>
    <link rel="stylesheet" href="css/estilo-gerar-ata.css">
</head>
<body>
    <!-- Cabeçalho -->
    <div class="header">
        <h2>Gerar ATA de Votação</h2>
    </div>

    <header>
        <div class="logos">
            <img src="img/logo-fatec.png" alt="logo_fatec" width="250">
        </div>
    </header>

    <main>
        <div class="caixa">
            <form method="POST">
                <?= csrf_field(); ?>

                <legend>Curso:</legend>
                <select name="curso" required>
                    <option value="">Selecione</option>
                    <option value="Gestão Empresa">Gestão Empresa</option>
                    <option value="Gestão Industrial">Gestão Industrial</option>
                    <option value="Desenvolvimento de Software">Desenvolvimento de Software</option>
                </select>

                <legend>Semestre:</legend>
                <select name="semestre" required>
                    <option value="">Selecione</option>
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?>º</option>
                    <?php endfor; ?>
                </select>

                <legend>Ano:</legend>
                <input type="number" name="ano" min="2000" max="2100" required>

                <button class="btn" type="submit" name="gerar">Gerar ATA</button>
            </form>

            <?php if ($ata_html): ?>
                <div class="ata">
                    <?= $ata_html; ?>
                    <form method="POST" action="baixar_ata.php" target="_blank">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="curso" value="<?= htmlspecialchars($dados_votacao['curso']) ?>">
                        <input type="hidden" name="semestre" value="<?= htmlspecialchars($dados_votacao['semestre']) ?>">
                        <input type="hidden" name="ano" value="<?= htmlspecialchars($dados_votacao['ano']) ?>">
                        <button class="btn" type="submit">Baixar ATA em PDF</button>
                    </form>
                </div>
            <?php endif; ?>

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
</body>
</html>
