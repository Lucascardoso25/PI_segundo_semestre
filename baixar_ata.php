<?php
session_start();
require_once 'conexao.php';
require_once 'csrf.php';
require_once 'vendor/autoload.php'; // se usar Composer (para reportlab/dompdf)

// Verifica CSRF e permissão
csrf_check();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'adm') {
    die("Acesso negado.");
}

$curso = $_POST['curso'] ?? '';
$semestre = intval($_POST['semestre'] ?? 0);
$ano = intval($_POST['ano'] ?? 0);

header("Content-type: application/pdf");
header("Content-Disposition: attachment; filename=ATA_{$curso}_{$semestre}S_{$ano}.pdf");

require_once 'vendor/autoload.php';
use Dompdf\Dompdf;

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->prepare('SELECT * FROM votacoes WHERE curso = ? AND semestre = ? AND ano = ?');
$stmt->execute([$curso, $semestre, $ano]);
$v = $stmt->fetch();

if (!$v) die("Nenhuma votação encontrada.");

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
<h2 style="text-align:center; color:#b71c1c;">ATA DE VOTAÇÃO</h2>
<p><strong>Curso:</strong> <?= htmlspecialchars($curso) ?><br>
<strong>Semestre:</strong> <?= htmlspecialchars($semestre) ?>º<br>
<strong>Ano:</strong> <?= htmlspecialchars($ano) ?><br>
<strong>Período:</strong> <?= htmlspecialchars($v['inicio']) ?> a <?= htmlspecialchars($v['fim']) ?></p>

<h3>Votantes (<?= count($votantes) ?>)</h3>
<ul>
<?php foreach ($votantes as $vt): ?>
<li><?= htmlspecialchars($vt['nome'] . ' (RA: ' . $vt['ra'] . ') — ' . $vt['criado_em']) ?></li>
<?php endforeach; ?>
</ul>

<h3>Resultado Final</h3>
<table border="1" cellspacing="0" cellpadding="6" width="100%">
<thead><tr><th>Nome</th><th>RA</th><th>Votos</th></tr></thead>
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

<p style="margin-top:40px;">_________________________<br>
Assinatura do Responsável</p>
<?php
$html = ob_get_clean();

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream();
exit;
