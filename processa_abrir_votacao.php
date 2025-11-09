<?php
session_start();
require_once 'conexao.php';
require_once 'csrf.php';

// Validação CSRF
csrf_check();

// Somente ADM pode abrir votação
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'adm') {
    header('Location: index.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $curso = trim($_POST['curso']);
    $semestre = intval($_POST['semestre']);
    $ano = intval($_POST['ano']);
    $inicio = $_POST['inicio'];
    $fim = $_POST['fim'];

    try {
        // Desativa qualquer votação anterior do mesmo curso, semestre e ano
        $stmt = $pdo->prepare("UPDATE votacoes 
                               SET ativo = 0 
                               WHERE curso = ? AND semestre = ? AND ano = ?");
        $stmt->execute([$curso, $semestre, $ano]);

        // Cria nova votação ativa
        $stmt = $pdo->prepare("INSERT INTO votacoes (curso, semestre, ano, inicio, fim, ativo)
                               VALUES (?, ?, ?, ?, ?, 1)");
        $stmt->execute([$curso, $semestre, $ano, $inicio, $fim]);

        echo "<script>
            alert('Votação aberta com sucesso!');
            window.location.href='menu_adm.php';
        </script>";
    } catch (PDOException $e) {
        echo "Erro ao abrir votação: " . $e->getMessage();
    }
} else {
    echo "Método inválido.";
}
?>
