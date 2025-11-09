<?php require_once 'conexao.php'; require_once 'vendor/autoload.php'; use Dompdf\Dompdf;
if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit('Envie via POST');
$curso = $_POST['curso']; $semestre = intval($_POST['semestre']); $ano = intval($_POST['ano']);
$stmt = $pdo->prepare('SELECT * FROM votacoes WHERE curso = ? AND semestre = ? AND ano = ?'); $stmt->execute([$curso,$semestre,$ano]); $v = $stmt->fetch();
if (!$v) exit('Votação não encontrada');
$stmt = $pdo->prepare('SELECT u.nome, u.ra, v.criado_em FROM votos v JOIN usuarios u ON v.votante_id = u.id WHERE v.votacao_id = ?'); $stmt->execute([$v['id']]); $votantes = $stmt->fetchAll();
$stmt = $pdo->prepare('SELECT r.nome, r.ra, COUNT(v.id) votos FROM representantes r LEFT JOIN votos v ON r.id = v.candidato_id AND v.votacao_id = ? WHERE r.curso = ? AND r.semestre = ? AND r.ano = ? GROUP BY r.id ORDER BY votos DESC'); $stmt->execute([$v['id'],$curso,$semestre,$ano]); $resultados = $stmt->fetchAll();
ob_start(); ?><h2>ATA — <?= htmlspecialchars($curso.' - Sem '.$semestre.' - Ano '.$ano) ?></h2><?php foreach ($votantes as $vt) echo '<p>'.htmlspecialchars($vt['nome'].' (RA: '.$vt['ra'].') — '. $vt['criado_em']).'</p>'; foreach ($resultados as $r) echo '<p>'.htmlspecialchars($r['nome'].' - '. $r['votos']).'</p>'; $html = ob_get_clean();
$dompdf = new Dompdf(); $dompdf->loadHtml($html); $dompdf->setPaper('A4','portrait'); $dompdf->render(); $dompdf->stream('ata.pdf',['Attachment'=>true]); ?>
