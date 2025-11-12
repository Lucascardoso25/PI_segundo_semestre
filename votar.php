<?php
session_start();
require_once 'conexao.php';
require_once 'csrf.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Buscar curso, semestre e ano do usuário logado
$stmt = $pdo->prepare('SELECT curso, semestre, ano FROM usuarios WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$msg = '';

// -------------------------------
// PROCESSAMENTO DO VOTO
// -------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['candidato_id'])) {
    csrf_check();

    $candidato_id = intval($_POST['candidato_id']);
    $votacao_id = intval($_POST['votacao_id']);

    // Verifica se a votação está ativa
    $stmt = $pdo->prepare('SELECT * FROM votacoes 
        WHERE id = ? AND curso = ? AND semestre = ? AND ano = ? 
        AND inicio <= NOW() AND fim >= NOW() AND ativo = 1');
    $stmt->execute([$votacao_id, $user['curso'], $user['semestre'], $user['ano']]);
    $votacao = $stmt->fetch();

    if (!$votacao) {
        $msg = '⚠️ Votação inválida para o seu curso, semestre ou ano.';
    } else {
        // Verifica se o aluno já votou
        $stmt = $pdo->prepare('SELECT id FROM votos WHERE votante_id = ? AND votacao_id = ?');
        $stmt->execute([$_SESSION['user_id'], $votacao_id]);

        if ($stmt->fetch()) {
            $msg = '⚠️ Você já votou nesta votação.';
        } else {
            // Confere candidato válido
            $stmt = $pdo->prepare('SELECT * FROM representantes 
                WHERE id = ? AND curso = ? AND semestre = ? AND ano = ?');
            $stmt->execute([$candidato_id, $user['curso'], $user['semestre'], $user['ano']]);
            $candidato = $stmt->fetch();

            if (!$candidato) {
                $msg = '❌ Candidato inválido para o seu curso ou semestre.';
            } else {
                // Registra o voto
                $stmt = $pdo->prepare('INSERT INTO votos (votante_id, candidato_id, votacao_id) VALUES (?, ?, ?)');
                $stmt->execute([$_SESSION['user_id'], $candidato_id, $votacao_id]);
                $msg = '✅ Voto registrado com sucesso!';
            }
        }
    }
}

// Buscar votações ativas
$stmt = $pdo->prepare('SELECT * FROM votacoes 
    WHERE inicio <= NOW() AND fim >= NOW() AND ativo = 1 
    AND curso = ? AND semestre = ? AND ano = ?');
$stmt->execute([$user['curso'], $user['semestre'], $user['ano']]);
$votacoes_ativas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Votação - Eleição Representante de Sala</title>
  <link rel="stylesheet" href="css/estilo-votacao.css">
  <style>
    /* Travar o rodapé e manter layout responsivo */
    html, body {
      height: 100%;
      margin: 0;
      display: flex;
      flex-direction: column;
      font-family: Arial, sans-serif;
      background-color: #fff;
    }

    main {
      flex: 1;
    }

    footer {
      background-color: #0d0d0d;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 40px;
      bottom: 0;
      width: 100%;
    }

    /* Centralizar botões de votação */
    .botoes-voto {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 20px;
      margin-top: 25px;
    }

    .botoes-voto a {
      text-decoration: none;
    }
  </style>
</head>
<body>

  <!-- Cabeçalho -->
  <div class="header">
      <h2>Tela de Votação</h2>
  </div>

  <header>
      <div class="logos">
          <img src="img/logo-fatec.png" alt="logo_fatec" width="250">
      </div>        
  </header>

  <!-- Conteúdo Principal -->
  <main class="container">
    <div class="card">
      <h2>Votar</h2>

      <?php if (!empty($msg)): ?>
        <p style="color:#b71c1c; font-weight:bold;"><?= htmlspecialchars($msg) ?></p>
      <?php endif; ?>

      <?php if (empty($votacoes_ativas)): ?>
        <p>Não há votações ativas no seu curso, semestre ou ano no momento.</p>
      <?php else: ?>
        <?php foreach ($votacoes_ativas as $v): ?>
          <section style="margin-top: 25px;">
            <h3><?= htmlspecialchars($v['curso']) ?> - <?= htmlspecialchars($v['semestre']) ?>º Semestre (<?= htmlspecialchars($v['ano']) ?>)</h3>
            <p><strong>Período:</strong> <?= htmlspecialchars($v['inicio']) ?> até <?= htmlspecialchars($v['fim']) ?></p>

            <?php
            $stmt = $pdo->prepare('SELECT r.*, u.foto 
                                   FROM representantes r 
                                   LEFT JOIN usuarios u ON r.usuario_id = u.id 
                                   WHERE r.curso = ? AND r.semestre = ? AND r.ano = ?');
            $stmt->execute([$v['curso'], $v['semestre'], $v['ano']]);
            $candidatos = $stmt->fetchAll();
            ?>

            <?php if (!$candidatos): ?>
              <p>Sem candidatos cadastrados para esta votação.</p>
            <?php else: ?>
              <form method="POST" class="form-voto" onsubmit="return confirmarVoto();">
                <?= csrf_field(); ?>
                <input type="hidden" name="votacao_id" value="<?= htmlspecialchars($v['id']) ?>">

                <div class="candidatos-grid">
                  <?php foreach ($candidatos as $c): ?>
                    <label class="candidato-card">
                      <input type="radio" name="candidato_id" value="<?= htmlspecialchars($c['id']) ?>" required>
                      <div class="foto-container">
                        <?php if (!empty($c['foto'])): ?>
                          <img src="<?= htmlspecialchars($c['foto']) ?>" class="foto-candidato" alt="Foto do candidato">
                        <?php else: ?>
                          <div class="foto-candidato" style="display:flex;align-items:center;justify-content:center;background:#ddd;">Sem Foto</div>
                        <?php endif; ?>
                      </div>
                      <div class="nome-candidato"><?= htmlspecialchars($c['nome']) ?></div>
                      <div class="ra-candidato">RA: <?= htmlspecialchars($c['ra']) ?></div>
                    </label>
                  <?php endforeach; ?>
                </div>

                <!-- Botões centralizados -->
                <div class="botoes-voto">
                  <button class="btn" type="submit">Confirmar Voto</button>
                  <a href="menu_aluno.php" class="btn btn-secondary">Voltar</a>
                </div>
              </form>
            <?php endif; ?>
          </section>
        <?php endforeach; ?>
      <?php endif; ?>
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

  <script>
    function confirmarVoto() {
      return confirm("Tem certeza de que deseja confirmar seu voto?");
    }
  </script>

</body>
</html>
