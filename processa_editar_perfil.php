<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'aluno') { header('Location: index.php'); exit; }
require_once 'conexao.php'; require_once 'csrf.php'; csrf_check();
$stmt = $pdo->prepare('SELECT ra, foto FROM usuarios WHERE id = ?'); $stmt->execute([$_SESSION['user_id']]); $u = $stmt->fetch();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit('Método inválido');
if (empty($_FILES['foto']['name'])) { echo '<p class="erro">Nenhuma foto enviada.</p><p><a href="editar_perfil.php">Voltar</a></p>'; exit; }
$f = $_FILES['foto'];
$erros = [];
if ($f['size'] > 2 * 1024 * 1024) $erros[] = 'Arquivo muito grande (max 2MB)';
$allowed = ['image/jpeg'=>'jpg','image/png'=>'png']; $mime = mime_content_type($f['tmp_name']);
if (!isset($allowed[$mime])) $erros[] = 'Formato inválido';
if ($erros) { foreach ($erros as $e) echo "<p class='erro'>".htmlspecialchars($e)."</p>"; echo '<p><a href="editar_perfil.php">Voltar</a></p>'; exit; }
$ext = $allowed[$mime]; $filename = preg_replace('/[^a-zA-Z0-9_-]/','', $u['ra']) . '.' . $ext; $dest = __DIR__ . '/uploads/' . $filename;
if (!move_uploaded_file($f['tmp_name'], $dest)) { echo '<p class="erro">Falha ao salvar a foto</p><p><a href="editar_perfil.php">Voltar</a></p>'; exit; }
$foto_path = 'uploads/' . $filename;
$stmt = $pdo->prepare('UPDATE usuarios SET foto = ? WHERE id = ?'); $stmt->execute([$foto_path, $_SESSION['user_id']]);
echo '<p>Foto atualizada com sucesso.</p><p><a href="menu_aluno.php">Voltar</a></p>';
?>
