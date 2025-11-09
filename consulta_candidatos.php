<?php
session_start();
require_once 'conexao.php';
require_once 'csrf.php';

// Verifica se o usuário é administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'adm') {
    header('Location: index.php');
    exit;
}

$candidatos = [];

// Se o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar'])) {
    $curso = trim($_POST['curso'] ?? '');
    $semestre = intval($_POST['semestre'] ?? 0);
    $ano = intval($_POST['ano'] ?? 0);

    try {
        $stmt = $pdo->prepare('
            SELECT r.*, u.email, u.foto 
            FROM representantes r 
            LEFT JOIN usuarios u ON r.usuario_id = u.id 
            WHERE r.curso = ? AND r.semestre = ? AND r.ano = ?
        ');
        $stmt->execute([$curso, $semestre, $ano]);
        $candidatos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erro ao consultar candidatos: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Consulta de Candidatos</title>
    <link rel="stylesheet" href="css/estilo-consulta-candidatos.css">
</head>
<body>
    <!-- Cabeçalho -->
    <div class="header">
        <h2>Eleição Representante de Sala</h2>
    </div>

    <header>
        <div class="logos">
            <img src="img/logo-fatec.png" alt="logo_fatec" width="250">
        </div>
    </header>

    <!-- Conteúdo principal -->
    <main>
        <div class="container">
            <div class="card">
                <h2>Consultar Candidatos</h2>

                <form method="POST">
                    <?= csrf_field(); ?>

                    <label for="curso">Curso</label>
                    <select name="curso" id="curso" required>
                        <option value="">Selecione</option>
                        <option value="Gestão Empresa">Gestão Empresa</option>
                        <option value="Gestão Industrial">Gestão Industrial</option>
                        <option value="Desenvolvimento de Software">Desenvolvimento de Software</option>
                    </select>

                    <label for="semestre">Semestre</label>
                    <input type="number" name="semestre" id="semestre" min="1" max="6" required>

                    <label for="ano">Ano</label>
                    <input type="number" name="ano" id="ano" min="2000" max="2100" required>

                    <div class="botoes">
                        <button class="btn" type="submit" name="buscar">Buscar</button>
                        <a class="btn btn-secondary" href="menu_adm.php">Voltar</a>
                    </div>
                </form>

                <?php if (!empty($candidatos)): ?>
                    <table class="tabela">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Nome</th>
                                <th>RA</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($candidatos as $c): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($c['foto'])): ?>
                                            <img src="<?= htmlspecialchars($c['foto'], ENT_QUOTES, 'UTF-8'); ?>" 
                                                 class="avatar-small" 
                                                 alt="Foto do candidato">
                                        <?php else: ?>
                                            (sem foto)
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($c['ra'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <form action="excluir_candidato.php" method="POST" 
                                              onsubmit="return confirm('Confirmar exclusão do candidato?');">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="id" value="<?= intval($c['id']); ?>">
                                            <button class="btn" type="submit">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                    <p class="mensagem-erro">Nenhum candidato encontrado para os filtros informados.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Rodapé -->
    <footer>
        <img class="logo-sp" src="img/logo-saopaulo.png" alt="Governo SP">
        <div class="desenvolvido">
            <p><i>Desenvolvido por</i></p>
            <img src="img/webvote.png" alt="WebVote">
        </div>
    </footer>
</body>
</html>
