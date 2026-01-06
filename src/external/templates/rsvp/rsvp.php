<?php
/**
 * Página Pública de RSVP - Confirmação de Presença
 * URL: /external/rsvp/{token}
 */
use ChurchCRM\dto\SystemURLs;

$participante = $participante ?? [];
$nomePessoa = ($participante['per_FirstName'] ?? '') . ' ' . ($participante['per_LastName'] ?? '');
$tituloReuniao = $participante['titulo'] ?? '';
$dataReuniao = isset($participante['data_reuniao']) ? date('d/m/Y H:i', strtotime($participante['data_reuniao'])) : '';
$local = $participante['local'] ?? '';
$ministerioNome = $participante['ministerio_nome'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= gettext('Confirmação de Presença') ?> - <?= gettext('Reunião') ?></title>
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
        .rsvp-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }
        .rsvp-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .rsvp-header h1 {
            color: #667eea;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .rsvp-header .icon {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 20px;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .info-box h3 {
            margin-top: 0;
            color: #333;
            font-size: 1.3rem;
        }
        .info-item {
            margin: 10px 0;
            display: flex;
            align-items: center;
        }
        .info-item i {
            width: 30px;
            color: #667eea;
            margin-right: 10px;
        }
        .btn-group-rsvp {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        .btn-rsvp {
            flex: 1;
            padding: 15px 30px;
            font-size: 1.1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: bold;
        }
        .btn-confirmar {
            background: #28a745;
            color: white;
        }
        .btn-confirmar:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        .btn-cancelar {
            background: #dc3545;
            color: white;
        }
        .btn-cancelar:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }
        .btn-indeciso {
            background: #ffc107;
            color: #333;
        }
        .btn-indeciso:hover {
            background: #e0a800;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
            margin-top: 10px;
        }
        .status-pendente {
            background: #ffc107;
            color: #333;
        }
        .status-confirmado {
            background: #28a745;
            color: white;
        }
        .status-cancelado {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="rsvp-card">
        <div class="rsvp-header">
            <div class="icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <h1><?= gettext('Confirmação de Presença') ?></h1>
            <p class="text-muted"><?= gettext('Olá') ?>, <strong><?= htmlspecialchars($nomePessoa) ?></strong>!</p>
        </div>

        <div class="info-box">
            <h3><i class="fas fa-hands-helping"></i> <?= htmlspecialchars($ministerioNome) ?></h3>
            <div class="info-item">
                <i class="fas fa-heading"></i>
                <strong><?= gettext('Reunião:') ?></strong> <?= htmlspecialchars($tituloReuniao) ?>
            </div>
            <div class="info-item">
                <i class="fas fa-calendar-alt"></i>
                <strong><?= gettext('Data:') ?></strong> <?= htmlspecialchars($dataReuniao) ?>
            </div>
            <?php if ($local): ?>
            <div class="info-item">
                <i class="fas fa-map-marker-alt"></i>
                <strong><?= gettext('Local:') ?></strong> <?= htmlspecialchars($local) ?>
            </div>
            <?php endif; ?>
            
            <?php if (isset($participante['status'])): ?>
            <div class="info-item">
                <i class="fas fa-info-circle"></i>
                <strong><?= gettext('Status Atual:') ?></strong>
                <span class="status-badge status-<?= $participante['status'] ?>">
                    <?php
                    $statusText = [
                        'pendente' => gettext('Pendente'),
                        'confirmado' => gettext('Confirmado'),
                        'cancelado' => gettext('Cancelado'),
                        'presente' => gettext('Presente'),
                        'ausente' => gettext('Ausente')
                    ];
                    echo $statusText[$participante['status']] ?? $participante['status'];
                    ?>
                </span>
            </div>
            <?php endif; ?>
        </div>

        <?php 
        $token = $participante['token_rsvp'] ?? ($token ?? '');
        ?>
        <form id="form-rsvp" method="POST" action="<?= $rootPath ?>/external/rsvp/<?= htmlspecialchars($token) ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            
            <div class="btn-group-rsvp">
                <button type="submit" name="status" value="confirmado" class="btn-rsvp btn-confirmar">
                    <i class="fas fa-check"></i> <?= gettext('Confirmar Presença') ?>
                </button>
                <button type="submit" name="status" value="cancelado" class="btn-rsvp btn-cancelar">
                    <i class="fas fa-times"></i> <?= gettext('Cancelar') ?>
                </button>
            </div>
        </form>

        <div class="text-center mt-4">
            <small class="text-muted">
                <?= gettext('Esta confirmação é válida apenas para esta reunião específica.') ?>
            </small>
        </div>
    </div>

    <script src="<?= $rootPath ?>/skin/v2/external/js/jquery.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#form-rsvp').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const status = $('button[type="submit"]:focus').val() || 'confirmado';
            
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: {
                    status: status,
                    token: form.find('input[name="token"]').val()
                },
                success: function(response) {
                    if (typeof response === 'string') {
                        // Se retornar HTML (página de sucesso)
                        $('body').html(response);
                    } else if (response.success) {
                        alert('<?= gettext("Presença confirmada com sucesso!") ?>');
                        location.reload();
                    }
                },
                error: function() {
                    alert('<?= gettext("Erro ao confirmar presença. Tente novamente.") ?>');
                }
            });
        });
    });
    </script>
</body>
</html>

