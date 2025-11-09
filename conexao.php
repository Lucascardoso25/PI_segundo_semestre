<?php
// conexao.php - configurado para XAMPP localhost
// conexao.php - configurado para XAMPP localhost
$host = 'localhost';
$db   = 'webvote';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo 'Conexão falhou: ' . $e->getMessage();
    exit;
}

// 🔐 Criação automática do admin
try {
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute(['admin@fatec.sp.gov.br']);
    $existe = $stmt->fetch();

    if (!$existe) {
        $senhaHash = password_hash('admin123', PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nome, ra, email, senha, tipo, curso, semestre, ano, foto)
                VALUES ('Administrador', '000000', 'admin@fatec.sp.gov.br', ?, 'adm', NULL, NULL, NULL, NULL)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$senhaHash]);

        error_log('Administrador padrão criado: admin@fatec.sp.gov.br / senha: admin123');
    }
} catch (PDOException $e) {
    error_log('Erro ao verificar/criar admin: ' . $e->getMessage());
}

?>
