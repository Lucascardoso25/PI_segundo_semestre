<?php
session_start();
require_once 'conexao.php';
require_once 'csrf.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validação CSRF
    csrf_check();

    // Recebe dados do formulário
    $nome = trim($_POST['nome']);
    $curso = trim($_POST['curso']);
    $semestre = trim($_POST['semestre']);
    $ra = trim($_POST['ra']);
    $ano = trim($_POST['ano']);
    $id_usuario = $_SESSION['user_id'] ?? null;
    $tipo_usuario = $_SESSION['user_tipo'] ?? null;

    if (!$id_usuario) {
        die("Usuário não autenticado.");
    }

    try {
        // Inserir o novo representante
        $stmt = $pdo->prepare("INSERT INTO representantes (nome, curso, semestre, ra, ano, id_usuario)
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $curso, $semestre, $ra, $ano, $id_usuario]);

        // Redireciona conforme o tipo de usuário
        if ($tipo_usuario === 'adm') {
            echo "<script>
                alert('Representante cadastrado com sucesso pelo administrador!');
                window.location.href='cadastrar_representante_adm.php';
            </script>";
        } else {
            echo "<script>
                alert('Cadastro realizado com sucesso!');
                window.location.href='menu_aluno.php';
            </script>";
        }

    } catch (PDOException $e) {
        echo "Erro ao cadastrar representante: " . $e->getMessage();
    }
} else {
    echo "Método inválido.";
}
?>
