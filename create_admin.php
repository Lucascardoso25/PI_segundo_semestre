<?php
require_once 'conexao.php';

$nome = 'ADM Sistema';
$ra = 'ADM000';
$email = 'admin@fatec.sp.gov.br';
$senha = 'admin123';
$hash = password_hash($senha, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    echo "ADM já existe.\n";
    exit;
}

$stmt = $pdo->prepare("INSERT INTO usuarios (nome, ra, email, senha, tipo) VALUES (?, ?, ?, ?, 'adm')");
$stmt->execute([$nome, $ra, $email, $hash]);

echo "ADM criado com sucesso. Email: $email | Senha: $senha\n";
?>
