<?php session_start(); require_once 'conexao.php'; require_once 'csrf.php'; csrf_check(); if (!isset($_SESSION['user_id'])||$_SESSION['user_tipo']!=='adm') header('Location: index.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') { $id = intval($_POST['id']); $stmt = $pdo->prepare('DELETE FROM representantes WHERE id = ?'); $stmt->execute([$id]); }
header('Location: consulta_candidatos.php'); exit; ?>
