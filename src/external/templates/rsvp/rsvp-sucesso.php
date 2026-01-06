<?php
/**
 * Página de Sucesso após RSVP
 */
$statusText = [
    'confirmado' => gettext('Presença Confirmada'),
    'cancelado' => gettext('Presença Cancelada'),
    'presente' => gettext('Você está presente!'),
    'ausente' => gettext('Ausência Registrada')
];
$statusIcon = [
    'confirmado' => 'check-circle',
    'cancelado' => 'times-circle',
    'presente' => 'user-check',
    'ausente' => 'user-times'
];
$statusColor = [
    'confirmado' => '#28a745',
    'cancelado' => '#dc3545',
    'presente' => '#28a745',
    'ausente' => '#ffc107'
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $statusText[$status] ?? gettext('Sucesso') ?></title>
    <?php $rootPath = $sRootPath ?? '/'; ?>
    <link rel="stylesheet" href="<?= $rootPath ?>/skin/v2/external/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .success-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 500px;
            width: 100%;
            padding: 50px;
            text-align: center;
        }
        .success-icon {
            font-size: 5rem;
            color: <?= $statusColor[$status] ?? '#28a745' ?>;
            margin-bottom: 20px;
            animation: bounce 1s;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        .success-card h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .success-card p {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        .btn-back {
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="success-icon">
            <i class="fas fa-<?= $statusIcon[$status] ?? 'check-circle' ?>"></i>
        </div>
        <h1><?= $statusText[$status] ?? gettext('Sucesso') ?></h1>
        <p>
            <?php if ($status === 'confirmado'): ?>
                <?= gettext('Sua presença foi confirmada! Esperamos você na reunião.') ?>
            <?php elseif ($status === 'cancelado'): ?>
                <?= gettext('Sua ausência foi registrada. Caso mude de ideia, você pode confirmar sua presença novamente.') ?>
            <?php else: ?>
                <?= gettext('Informação atualizada com sucesso!') ?>
            <?php endif; ?>
        </p>
        <p class="text-muted">
            <strong><?= htmlspecialchars($participante['titulo'] ?? '') ?></strong><br>
            <?= isset($participante['data_reuniao']) ? date('d/m/Y H:i', strtotime($participante['data_reuniao'])) : '' ?>
        </p>
        <a href="<?= $rootPath ?>/external/rsvp/<?= htmlspecialchars($participante['token_rsvp'] ?? '') ?>" class="btn-back">
            <i class="fas fa-arrow-left"></i> <?= gettext('Voltar') ?>
        </a>
    </div>
</body>
</html>

