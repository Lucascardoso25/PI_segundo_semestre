<?php session_start(); require_once 'conexao.php'; require_once 'csrf.php'; csrf_check(); if (!isset($_SESSION['user_id'])||$_SESSION['user_tipo']!=='adm') header('Location: index.php');
$curso = $_POST['curso'] ?? null; $semestre = intval($_POST['semestre'] ?? 0); $ano = intval($_POST['ano'] ?? 0);
if ($curso) { $stmt = $pdo->prepare('UPDATE votacoes SET ativo = 0 WHERE curso = ? AND semestre = ? AND ano = ?'); $stmt->execute([$curso,$semestre,$ano]); echo '<p>Votação encerrada.</p>'; }
echo '<p><a href="menu_adm.php">Voltar</a></p>'; ?>
