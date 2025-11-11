<?php
session_start();
require_once 'conexao.php';
require_once 'csrf.php';
csrf_check();

// Verifica se o usuário está logado (aluno ou ADM)
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_tipo'], ['aluno', 'adm'])) {
    header("Location: index.php");
    exit;
}

$id_usuario = $_SESSION['user_id'];

// Pega dados do usuário
$stmt = $pdo->prepare('SELECT ra, foto FROM usuarios WHERE id = ?');
$stmt->execute([$id_usuario]);
$u = $stmt->fetch();

// Verifica se o POST contém arquivo
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['foto']['name'])) {
    exit('Nenhuma foto enviada.');
}

$f = $_FILES['foto'];
$erros = [];

// Limite 2MB
if ($f['size'] > 2 * 1024 * 1024) $erros[] = 'Arquivo muito grande (max 2MB)';

// Tipos permitidos
$allowed = ['image/jpeg'=>'jpg','image/png'=>'png'];
$mime = mime_content_type($f['tmp_name']);
if (!isset($allowed[$mime])) $erros[] = 'Formato inválido';

if ($erros) {
    foreach ($erros as $e) echo "<p class='erro'>".htmlspecialchars($e)."</p>";
    echo '<p><a href="editar_perfil.php">Voltar</a></p>';
    exit;
}

// Define o nome do arquivo
if (empty($u['ra'])) $u['ra'] = 'user_' . $id_usuario;
$ext = $allowed[$mime];
$filename = preg_replace('/[^a-zA-Z0-9_-]/','', $u['ra']) . '.' . $ext;

// Pasta img/
$folder = __DIR__ . '/img/';
if (!is_dir($folder)) mkdir($folder, 0777, true);

$dest = $folder . $filename;

// Move arquivo
if (!move_uploaded_file($f['tmp_name'], $dest)) {
    exit('Falha ao salvar a foto');
}

// Atualiza caminho no banco
$foto_path = 'img/' . $filename;
$stmt = $pdo->prepare('UPDATE usuarios SET foto = ? WHERE id = ?');
$stmt->execute([$foto_path, $id_usuario]);

// Redireciona de acordo com o tipo de usuário
if ($_SESSION['user_tipo'] === 'adm') {
    header("Location: menu_adm.php?msg=foto_atualizada");
} else {
    header("Location: menu_aluno.php?msg=foto_atualizada");
}
exit;
