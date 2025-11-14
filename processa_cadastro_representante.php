<?php
session_start();
require_once 'conexao.php';
require_once 'csrf.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    csrf_check();

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

    // ❗ VERIFICA SE O RA JÁ ESTÁ CADASTRADO
    $stmt = $pdo->prepare("SELECT id FROM representantes WHERE ra = ?");
    $stmt->execute([$ra]);
    if ($stmt->fetch()) {
        echo "<script>
            alert('❌ Já existe um representante cadastrado com esse RA!');
            window.location.href='" . ($tipo_usuario === 'adm' ? 'cadastrar_representante_adm.php' : 'cadastrar_representante.php') . "';
        </script>";
        exit;
    }

    try {
        // Inserir novo representante
        $stmt = $pdo->prepare("INSERT INTO representantes (nome, curso, semestre, ra, ano, id_usuario)
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $curso, $semestre, $ra, $ano, $id_usuario]);

        // Redirecionamento correto
        if ($tipo_usuario === 'adm') {
            echo "<script>
                alert('Representante cadastrado com sucesso pelo administrador!');
                window.location.href='cadastrar_representante_adm.php';
            </script>";
        } else {
            echo "<script>
                alert('Cadastro realizado com sucesso!');
                window.location.href='cadastrar_representante.php';
            </script>";
        }

    } catch (PDOException $e) {
        echo "Erro ao cadastrar representante: " . $e->getMessage();
    }
} else {
    echo "Método inválido.";
}
?>
