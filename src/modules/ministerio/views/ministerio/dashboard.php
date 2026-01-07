<?php
require_once __DIR__ . '/../../../Include/Header.php';
?>

<div class="row">
    <!-- Cards de Estatísticas -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= $estatisticas['total_ministerios'] ?></h3>
                <p><?= gettext('Ministérios Ativos') ?></p>
            </div>
            <div class="icon">
                <i class="fas fa-church"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= $estatisticas['total_membros'] ?></h3>
                <p><?= gettext('Membros Totais') ?></p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= $estatisticas['proximas_reunioes'] ?></h3>
                <p><?= gettext('Próximas Reuniões') ?></p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?= $estatisticas['mensagens_pendentes'] ?></h3>
                <p><?= gettext('Mensagens Pendentes') ?></p>
            </div>
            <div class="icon">
                <i class="fas fa-envelope"></i>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos e Relatórios -->
<div class="row mt-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar"></i> 
                    <?= gettext('Visão Geral') ?>
                </h3>
            </div>
            <div class="card-body">
                <canvas id="ministeriosChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> 
                    <?= gettext('Ações Rápidas') ?>
                </h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="index.php" class="btn btn-primary btn-block">
                        <i class="fas fa-church"></i> 
                        <?= gettext('Gerenciar Ministérios') ?>
                    </a>
                    <a href="../mensagem/index.php" class="btn btn-info btn-block">
                        <i class="fas fa-envelope"></i> 
                        <?= gettext('Enviar Mensagens') ?>
                    </a>
                    <a href="../reuniao/index.php" class="btn btn-warning btn-block">
                        <i class="fas fa-calendar"></i> 
                        <?= gettext('Agendar Reuniões') ?>
                    </a>
                    <a href="membros.php" class="btn btn-success btn-block">
                        <i class="fas fa-users"></i> 
                        <?= gettext('Gerenciar Membros') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Atividades Recentes -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clock"></i> 
                    <?= gettext('Atividades Recentes') ?>
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><?= gettext('Data') ?></th>
                                <th><?= gettext('Atividade') ?></th>
                                <th><?= gettext('Ministério') ?></th>
                                <th><?= gettext('Usuário') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Dados simulados de atividades recentes
                            $atividades = [
                                [
                                    'data' => date('d/m/Y H:i', strtotime('-2 hours')),
                                    'atividade' => 'Novo ministério criado',
                                    'ministerio' => 'Ministério de Jovens',
                                    'usuario' => 'Administrador'
                                ],
                                [
                                    'data' => date('d/m/Y H:i', strtotime('-5 hours')),
                                    'atividade' => 'Membro adicionado',
                                    'ministerio' => 'Ministério de Louvor',
                                    'usuario' => 'Líder de Louvor'
                                ],
                                [
                                    'data' => date('d/m/Y H:i', strtotime('-1 day')),
                                    'atividade' => 'Reunião agendada',
                                    'ministerio' => 'Ministério de Ensino',
                                    'usuario' => 'Professor'
                                ]
                            ];
                            
                            foreach ($atividades as $atividade):
                            ?>
                                <tr>
                                    <td><?= $atividade['data'] ?></td>
                                    <td><?= $atividade['atividade'] ?></td>
                                    <td><?= $atividade['ministerio'] ?></td>
                                    <td><?= $atividade['usuario'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Gráfico de ministérios
    var ctx = document.getElementById('ministeriosChart').getContext('2d');
    var ministeriosChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['<?= gettext("Louvor") ?>', '<?= gettext("Ensino") ?>', '<?= gettext("Ação Social") ?>', '<?= gettext("Jovens") ?>', '<?= gettext("Mulheres") ?>'],
            datasets: [{
                label: '<?= gettext("Número de Membros") ?>',
                data: [12, 8, 15, 6, 10, 9],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../../../Include/Footer.php'; ?>
