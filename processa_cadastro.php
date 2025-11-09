<?php
// Inicia a sessão de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexao.php';
require_once 'csrf.php';

// Verifica token CSRF
csrf_check();

// Bloqueia qualquer método que não seja POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit('Método inválido');

// Coleta e valida os dados
$nome = trim($_POST['nome']);
$ra = trim($_POST['ra']);
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$senha = $_POST['senha'];
$curso = $_POST['curso'] ?? null;
$semestre = intval($_POST['semestre'] ?? 0);
$ano = intval($_POST['ano'] ?? 0);

$erros = [];

// Validações básicas
if (strlen($nome) < 3) $erros[] = 'Nome deve ter no mínimo 3 caracteres';
if (!$email) $erros[] = 'Email inválido';
if (strlen($senha) < 6) $erros[] = 'Senha muito curta';

// Validação de curso e semestre
$valid_cursos = ['Gestão Empresa', 'Gestão Industrial', 'Desenvolvimento de Software'];
if (!in_array($curso, $valid_cursos)) $erros[] = 'Curso inválido';
if ($semestre < 1 || $semestre > 6) $erros[] = 'Semestre inválido';
if ($ano < 2000 || $ano > 2100) $erros[] = 'Ano inválido';

// Verifica duplicidade de RA ou Email
$stmt = $pdo->prepare('SELECT id FROM usuarios WHERE ra = ? OR email = ?');
$stmt->execute([$ra, $email]);
if ($stmt->fetch()) $erros[] = 'RA ou Email já cadastrado';

// Diretório de uploads
$upload_dir = __DIR__ . '/uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$foto_path = null;

// Upload da foto
if (!empty($_FILES['foto']['name'])) {
    $f = $_FILES['foto'];

    if ($f['size'] > 2 * 1024 * 1024) {
        $erros[] = 'Arquivo de foto muito grande (max 2MB)';
    } else {
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
        $mime = mime_content_type($f['tmp_name']);
        if (!isset($allowed[$mime])) {
            $erros[] = 'Formato de imagem inválido (use JPG ou PNG)';
        } else {
            $ext = $allowed[$mime];
            $filename = preg_replace('/[^a-zA-Z0-9_-]/', '', $ra) . '.' . $ext;
            $dest = $upload_dir . $filename;

            if (!move_uploaded_file($f['tmp_name'], $dest)) {
                $erros[] = 'Falha ao salvar a foto';
            } else {
                $foto_path = 'uploads/' . $filename;
            }
        }
    }
}

// Caso existam erros, exibe e interrompe
if ($erros) {
    foreach ($erros as $e) {
        echo "<p class='erro'>" . htmlspecialchars($e) . "</p>";
    }
    echo '<p><a href="cadastro.php">Voltar</a></p>';
    exit;
}

// Cria hash da senha e salva no banco
$hash = password_hash($senha, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO usuarios (nome, ra, email, senha, tipo, curso, semestre, ano, foto) VALUES (?, ?, ?, ?, "aluno", ?, ?, ?, ?)');
$stmt->execute([$nome, $ra, $email, $hash, $curso, $semestre, $ano, $foto_path]);

// Inicia sessão do usuário
$id = $pdo->lastInsertId();
$_SESSION['user_id'] = $id;
$_SESSION['user_nome'] = $nome;
$_SESSION['user_tipo'] = 'aluno';

// Redireciona para o menu do aluno
header('Location: menu_aluno.php');
exit;
?>
