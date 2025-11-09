<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
    $id = $_SESSION['user_id'];
    $foto = $_FILES['foto'];

    // Verifica se o upload é válido
    if ($foto['error'] === 0 && $foto['size'] < 5 * 1024 * 1024) { // até 5MB
        $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $novoNome = 'uploads/foto_' . $id . '.' . $ext;

        // Cria a pasta se não existir
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        // Move a imagem
        if (move_uploaded_file($foto['tmp_name'], $novoNome)) {
            $stmt = $pdo->prepare("UPDATE usuarios SET foto = ? WHERE id = ?");
            $stmt->execute([$novoNome, $id]);
            header('Location: menu_aluno.php');
            exit;
        } else {
            echo "Erro ao mover a imagem.";
        }
    } else {
        echo "Erro: arquivo inválido ou muito grande.";
    }
} else {
    header('Location: menu_aluno.php');
    exit;
}
?>
