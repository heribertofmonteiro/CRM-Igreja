<?php
/**
 * Dashboard do Módulo Ministério
 * URL: /v2/ministerio
 */
use ChurchCRM\Authentication\AuthenticationManager;
use ChurchCRM\dto\SystemURLs;
?>
<!DOCTYPE html>
<html>
<head>
    <?php include __DIR__ . '/../../Include/Header-HTML-Scripts.php'; ?>
    <title><?= gettext('Ministério & Comunicação') ?> - <?= $sRootPath ?></title>
    <link rel="stylesheet" href="<?= $sRootPath ?>/skin/v2/external/css/dataTables.bootstrap4.min.css">
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include __DIR__ . '/../../Include/Header.php'; ?>
        <?php include __DIR__ . '/../../Include/Left-Sidebar.php'; ?>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1><i class="fas fa-hands-helping"></i> <?= gettext('Ministério & Comunicação') ?></h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?= $sRootPath ?>/v2/dashboard"><?= gettext('Dashboard') ?></a></li>
                                <li class="breadcrumb-item active"><?= gettext('Ministério') ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <!-- Cards de Estatísticas -->
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3 id="total-ministerios">0</h3>
                                    <p><?= gettext('Ministérios') ?></p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-church"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3 id="total-reunioes">0</h3>
                                    <p><?= gettext('Reuniões Agendadas') ?></p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3 id="total-membros">0</h3>
                                    <p><?= gettext('Membros Ativos') ?></p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3 id="mensagens-pendentes">0</h3>
                                    <p><?= gettext('Mensagens Pendentes') ?></p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                            </div>
                        </div>
                                    </div>

                    <!-- Tabs -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-primary card-tabs">
                                <div class="card-header p-0 pt-1">
                                    <ul class="nav nav-tabs" id="ministerio-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="tab-ministerios" data-toggle="tab" href="#content-ministerios" role="tab">
                                                <i class="fas fa-church"></i> <?= gettext('Ministérios') ?>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="tab-reunioes" data-toggle="tab" href="#content-reunioes" role="tab">
                                                <i class="fas fa-calendar-alt"></i> <?= gettext('Reuniões') ?>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="tab-mensagens" data-toggle="tab" href="#content-mensagens" role="tab">
                                                <i class="fas fa-envelope"></i> <?= gettext('Mensagens') ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="ministerio-tabs-content">
                                        <!-- Tab Ministérios -->
                                        <div class="tab-pane fade show active" id="content-ministerios" role="tabpanel">
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <?php if (AuthenticationManager::getCurrentUser()->isAdmin() || AuthenticationManager::getCurrentUser()->isEditRecordsEnabled()): ?>
                                                    <button type="button" class="btn btn-primary" onclick="abrirModalCriarMinisterio()">
                                                        <i class="fas fa-plus"></i> <?= gettext('Novo Ministério') ?>
                                                    </button>
                                                    <?php endif; ?>
                                                    <button type="button" class="btn btn-secondary" onclick="carregarMinisterios()">
                                                        <i class="fas fa-sync"></i> <?= gettext('Atualizar') ?>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table id="table-ministerios" class="table table-bordered table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th><?= gettext('Nome') ?></th>
                                                            <th><?= gettext('Descrição') ?></th>
                                                            <th><?= gettext('Líder') ?></th>
                                                            <th><?= gettext('Coordenador') ?></th>
                                                            <th><?= gettext('Membros') ?></th>
                                                            <th><?= gettext('Status') ?></th>
                                                            <th><?= gettext('Ações') ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Carregado via AJAX -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Tab Reuniões -->
                                        <div class="tab-pane fade" id="content-reunioes" role="tabpanel">
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <?php if (AuthenticationManager::getCurrentUser()->isAdmin() || AuthenticationManager::getCurrentUser()->isEditRecordsEnabled()): ?>
                                                    <button type="button" class="btn btn-primary" onclick="abrirModalCriarReuniao()">
                                                        <i class="fas fa-plus"></i> <?= gettext('Nova Reunião') ?>
                                                    </button>
                                                    <?php endif; ?>
                                                    <button type="button" class="btn btn-secondary" onclick="carregarReunioes()">
                                                        <i class="fas fa-sync"></i> <?= gettext('Atualizar') ?>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table id="table-reunioes" class="table table-bordered table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th><?= gettext('Ministério') ?></th>
                                                            <th><?= gettext('Título') ?></th>
                                                            <th><?= gettext('Data') ?></th>
                                                            <th><?= gettext('Local') ?></th>
                                                            <th><?= gettext('Participantes') ?></th>
                                                            <th><?= gettext('Ações') ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Carregado via AJAX -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                        <!-- Tab Mensagens -->
                        <div class="tab-pane fade" id="content-mensagens" role="tabpanel">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <?php if (AuthenticationManager::getCurrentUser()->isAdmin() || AuthenticationManager::getCurrentUser()->isEditRecordsEnabled()): ?>
                                    <button type="button" class="btn btn-primary" onclick="abrirModalCriarMensagem()">
                                        <i class="fas fa-plus"></i> <?= gettext('Nova Mensagem') ?>
                                    </button>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-secondary" onclick="carregarMensagens()">
                                        <i class="fas fa-sync"></i> <?= gettext('Atualizar') ?>
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-inline float-right">
                                        <div class="form-group me-2">
                                            <select class="form-control form-control-sm" id="filtro-status-mensagem" onchange="aplicarFiltrosMensagens()">
                                                <option value=""><?= gettext('Todos os Status') ?></option>
                                                <option value="rascunho"><?= gettext('Rascunho') ?></option>
                                                <option value="agendado"><?= gettext('Agendado') ?></option>
                                                <option value="enviando"><?= gettext('Enviando') ?></option>
                                                <option value="enviado"><?= gettext('Enviado') ?></option>
                                                <option value="falhou"><?= gettext('Falhou') ?></option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select class="form-control form-control-sm" id="filtro-tipo-mensagem" onchange="aplicarFiltrosMensagens()">
                                                <option value=""><?= gettext('Todos os Tipos') ?></option>
                                                <option value="geral"><?= gettext('Geral') ?></option>
                                                <option value="lembrete"><?= gettext('Lembrete') ?></option>
                                                <option value="anuncio"><?= gettext('Anúncio') ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                <?= gettext('As mensagens são processadas automaticamente pela fila de envio.') ?>
                            </div>
                            <div class="table-responsive">
                                <!-- Carregado via AJAX -->
                            </div>
                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include __DIR__ . '/../../Include/Footer.php'; ?>
    </div>
    
    <?php include __DIR__ . '/../../Include/Footer-HTML-Scripts.php'; ?>
    <script src="<?= $sRootPath ?>/skin/v2/external/js/jquery.dataTables.min.js"></script>
    <script src="<?= $sRootPath ?>/skin/v2/external/js/dataTables.bootstrap4.min.js"></script>
    
    <!-- Modais -->
    <?php include __DIR__ . '/modals/ministerio-modal.php'; ?>
    <?php include __DIR__ . '/modals/reuniao-modal.php'; ?>
    <?php include __DIR__ . '/modals/mensagem-modal.php'; ?>
    
    <script nonce="<?= SystemURLs::getCSPNonce() ?>">
    const API_BASE = '<?= $sRootPath ?>/v2/ministerio';
    let tableMinisterios, tableReunioes;
    
    // Verificar se window.CRM está disponível
    if (typeof window.CRM === 'undefined') {
        window.CRM = {
            root: '<?= $sRootPath ?>'
        };
    }

    // Carregar estatísticas
    function carregarEstatisticas() {
        $.ajax({
            url: API_BASE + '/api',
            method: 'GET',
            success: function(response) {
                if (response.data) {
                    $('#total-ministerios').text(response.data.length);
                    // Calcular outras estatísticas
                    let totalMembros = 0;
                    response.data.forEach(function(m) {
                        if (m.total_membros) totalMembros += parseInt(m.total_membros);
                    });
                    $('#total-membros').text(totalMembros);
                }
            }
        });
    }

    // Carregar lista de ministérios
    function carregarMinisterios() {
        $.ajax({
            url: API_BASE + '/api',
            method: 'GET',
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    let tbody = '';
                    response.data.forEach(function(ministerio) {
                        const status = ministerio.ativo == 1 
                            ? '<span class="badge badge-success"><?= gettext("Ativo") ?></span>'
                            : '<span class="badge badge-danger"><?= gettext("Inativo") ?></span>';
                        
                        tbody += '<tr>';
                        tbody += '<td><strong>' + (ministerio.nome || '') + '</strong></td>';
                        tbody += '<td>' + (ministerio.descricao || '-').substring(0, 50) + '...</td>';
                        tbody += '<td>' + (ministerio.lider_nome || '') + ' ' + (ministerio.lider_sobrenome || '') + '</td>';
                        tbody += '<td>' + (ministerio.coordenador_nome || '-') + ' ' + (ministerio.coordenador_sobrenome || '') + '</td>';
                        tbody += '<td><span class="badge badge-info">' + (ministerio.total_membros || 0) + '</span></td>';
                        tbody += '<td>' + status + '</td>';
                        tbody += '<td>';
                        tbody += '<button class="btn btn-sm btn-info" onclick="verDetalhesMinisterio(' + ministerio.id + ')">';
                        tbody += '<i class="fas fa-eye"></i> <?= gettext("Ver") ?></button> ';
                        tbody += '<?php if (AuthenticationManager::getCurrentUser()->isAdmin() || AuthenticationManager::getCurrentUser()->isEditRecordsEnabled()): ?>';
                        tbody += '<button class="btn btn-sm btn-warning" onclick="editarMinisterio(' + ministerio.id + ')">';
                        tbody += '<i class="fas fa-edit"></i> <?= gettext("Editar") ?></button> ';
                        tbody += '<?php endif; ?>';
                        tbody += '</td>';
                        tbody += '</tr>';
                    });
                    
                    $('#table-ministerios tbody').html(tbody);
                    
                    if (!tableMinisterios) {
                        tableMinisterios = $('#table-ministerios').DataTable({
                            language: {
                                url: '<?= $sRootPath ?>/skin/v2/external/js/Portuguese-Brasil.json'
                            },
                            order: [[0, 'asc']],
                            pageLength: 25
                        });
                    } else {
                        tableMinisterios.draw();
                    }
                } else {
                    $('#table-ministerios tbody').html('<tr><td colspan="7" class="text-center text-muted"><?= gettext("Nenhum ministério encontrado") ?></td></tr>');
                }
                carregarEstatisticas();
            },
            error: function(xhr) {
                console.error('Erro ao carregar ministérios:', xhr);
                bootbox.alert('<?= gettext("Erro ao carregar ministérios") ?>');
            }
        });
    }

    // Carregar reuniões
    function carregarReunioes() {
        $.ajax({
            url: API_BASE + '/reuniao',
            method: 'GET',
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    let tbody = '';
                    response.data.forEach(function(reuniao) {
                        const dataFormatada = new Date(reuniao.data_reuniao).toLocaleString('pt-BR');
                        const status = reuniao.ativo == 1 
                            ? '<span class="badge badge-success"><?= gettext("Ativa") ?></span>'
                            : '<span class="badge badge-danger"><?= gettext("Cancelada") ?></span>';
                        
                        tbody += '<tr>';
                        tbody += '<td>' + (reuniao.ministerio_nome || '') + '</td>';
                        tbody += '<td><strong>' + (reuniao.titulo || '') + '</strong></td>';
                        tbody += '<td>' + dataFormatada + '</td>';
                        tbody += '<td>' + (reuniao.local || '-') + '</td>';
                        tbody += '<td><span class="badge badge-info">' + (reuniao.total_participantes || 0) + '</span></td>';
                        tbody += '<td>' + status + '</td>';
                        tbody += '<td>';
                        tbody += '<button class="btn btn-sm btn-info" onclick="verDetalhesReuniao(' + reuniao.id + ')">';
                        tbody += '<i class="fas fa-eye"></i> <?= gettext("Ver") ?></button> ';
                        tbody += '<?php if (AuthenticationManager::getCurrentUser()->isAdmin() || AuthenticationManager::getCurrentUser()->isEditRecordsEnabled()): ?>';
                        tbody += '<button class="btn btn-sm btn-warning" onclick="editarReuniao(' + reuniao.id + ')">';
                        tbody += '<i class="fas fa-edit"></i> <?= gettext("Editar") ?></button> ';
                        tbody += '<button class="btn btn-sm btn-danger" onclick="excluirReuniao(' + reuniao.id + ')">';
                        tbody += '<i class="fas fa-trash"></i> <?= gettext("Excluir") ?></button>';
                        tbody += '<?php endif; ?>';
                        tbody += '</td>';
                        tbody += '</tr>';
                    });
                    
                    $('#table-reunioes tbody').html(tbody);
                    
                    if (!tableReunioes) {
                        tableReunioes = $('#table-reunioes').DataTable({
                            language: {
                                url: '<?= $sRootPath ?>/skin/v2/external/js/Portuguese-Brasil.json'
                            },
                            order: [[2, 'desc']],
                            pageLength: 25
                        });
                    } else {
                        tableReunioes.draw();
                    }
                } else {
                    $('#table-reunioes tbody').html('<tr><td colspan="6" class="text-center text-muted"><?= gettext("Nenhuma reunião encontrada") ?></td></tr>');
                }
            },
            error: function(xhr) {
                console.error('Erro ao carregar reuniões:', xhr);
                bootbox.alert('<?= gettext("Erro ao carregar reuniões") ?>');
            }
        });
    }
    
    // Editar reunião - função agora está no modal
    
    // Excluir reunião
    function excluirReuniao(id) {
        bootbox.confirm({
            message: '<?= gettext("Tem certeza que deseja excluir esta reunião?") ?>',
            buttons: {
                confirm: {
                    label: '<?= gettext("Sim") ?>',
                    className: 'btn-danger'
                },
                cancel: {
                    label: '<?= gettext("Não") ?>',
                    className: 'btn-secondary'
                }
            },
            callback: function(result) {
                if (result) {
                    $.ajax({
                        url: API_BASE + '/reuniao/' + id + '/excluir',
                        method: 'POST',
                        success: function(response) {
                            bootbox.alert(response.message || '<?= gettext("Reunião excluída com sucesso") ?>', function() {
                                carregarReunioes();
                            });
                        },
                        error: function(xhr) {
                            const msg = xhr.responseJSON?.error || '<?= gettext("Erro ao excluir reunião") ?>';
                            bootbox.alert(msg);
                        }
                    });
                }
            }
        });
    }
    
    // Ver detalhes da reunião
    function verDetalhesReuniao(id) {
        // Implementar quando página de detalhes estiver pronta
        bootbox.alert('ID da reunião: ' + id);
    }

    // Abrir modal criar ministério
    function abrirModalCriarMinisterio() {
        $('#ministerio-id').val('');
        $('#ministerio-nome').val('');
        $('#ministerio-descricao').val('');
        $('#ministerio-lider-id').val('').trigger('change');
        $('#ministerio-coordenador-id').val('').trigger('change');
        $('#ministerio-ativo').prop('checked', true);
        $('#modal-ministerio-title').text('<?= gettext("Novo Ministério") ?>');
        $('#modal-ministerio').modal('show');
    }

    // Editar ministério
    function editarMinisterio(id) {
        $.ajax({
            url: API_BASE + '/' + id + '/detalhes',
            method: 'GET',
            success: function(response) {
                if (response.data) {
                    const m = response.data;
                    $('#ministerio-id').val(m.id);
                    $('#ministerio-nome').val(m.nome);
                    $('#ministerio-descricao').val(m.descricao);
                    $('#ministerio-lider-id').val(m.lider_id).trigger('change');
                    $('#ministerio-coordenador-id').val(m.coordenador_id).trigger('change');
                    $('#ministerio-ativo').prop('checked', m.ativo == 1);
                    $('#modal-ministerio-title').text('<?= gettext("Editar Ministério") ?>');
                    $('#modal-ministerio').modal('show');
                }
            },
            error: function() {
                bootbox.alert('<?= gettext("Erro ao carregar dados do ministério") ?>');
            }
        });
    }

    // Ver detalhes do ministério
    function verDetalhesMinisterio(id) {
        window.location.href = '<?= $sRootPath ?>/v2/ministerio/' + id + '/detalhes';
    }

    // Salvar ministério
    function salvarMinisterio() {
        const dados = {
            nome: $('#ministerio-nome').val(),
            descricao: $('#ministerio-descricao').val(),
            lider_id: $('#ministerio-lider-id').val(),
            coordenador_id: $('#ministerio-coordenador-id').val(),
            ativo: $('#ministerio-ativo').is(':checked') ? 1 : 0
        };

        if (!dados.nome || !dados.lider_id) {
            bootbox.alert('<?= gettext("Preencha todos os campos obrigatórios") ?>');
            return;
        }

        const id = $('#ministerio-id').val();
        const url = id ? API_BASE + '/' + id + '/atualizar' : API_BASE + '/criar';
        const method = 'POST';

        $.ajax({
            url: url,
            method: method,
            contentType: 'application/json',
            data: JSON.stringify(dados),
            success: function(response) {
                bootbox.alert(response.message || '<?= gettext("Ministério salvo com sucesso") ?>', function() {
                    $('#modal-ministerio').modal('hide');
                    carregarMinisterios();
                });
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.error || '<?= gettext("Erro ao salvar ministério") ?>';
                bootbox.alert(msg);
            }
        });
    }
    
    // Abrir modal criar reunião
    function abrirModalCriarReuniao() {
        $('#modal-reuniao').modal('show');
    }

    // Abrir modal criar mensagem
    function abrirModalCriarMensagem() {
        $('#modal-mensagem').modal('show');
    }

    // Carregar mensagens
    function carregarMensagens() {
        const status = $('#filtro-status-mensagem').val() || '';
        const tipo = $('#filtro-tipo-mensagem').val() || '';
        let url = API_BASE + '/mensagem';
        const params = [];
        if (status) params.push('status=' + status);
        if (tipo) params.push('tipo=' + tipo);
        if (params.length > 0) url += '?' + params.join('&');
        
        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    // Criar tabela de mensagens
                    let html = '<table id="table-mensagens" class="table table-bordered table-striped table-hover">';
                    html += '<thead><tr>';
                    html += '<th><?= gettext("Assunto") ?></th>';
                    html += '<th><?= gettext("Ministério") ?></th>';
                    html += '<th><?= gettext("Tipo") ?></th>';
                    html += '<th><?= gettext("Canal") ?></th>';
                    html += '<th><?= gettext("Status") ?></th>';
                    html += '<th><?= gettext("Data") ?></th>';
                    html += '<th><?= gettext("Ações") ?></th>';
                    html += '</tr></thead><tbody>';
                    
                    response.data.forEach(function(msg) {
                        const statusColors = {
                            'rascunho': 'secondary',
                            'agendado': 'info',
                            'enviando': 'warning',
                            'enviado': 'success',
                            'falhou': 'danger'
                        };
                        const statusColor = statusColors[msg.status] || 'secondary';
                        const dataFormatada = new Date(msg.criado_em).toLocaleDateString('pt-BR');
                        
                        html += '<tr>';
                        html += '<td>' + (msg.assunto || '') + '</td>';
                        html += '<td>' + (msg.ministerio_nome || '') + '</td>';
                        html += '<td>' + (msg.tipo || 'geral') + '</td>';
                        html += '<td>' + (msg.canal || 'email') + '</td>';
                        html += '<td><span class="badge badge-' + statusColor + '">' + (msg.status || '') + '</span></td>';
                        html += '<td>' + dataFormatada + '</td>';
                        html += '<td>';
                        html += '<button class="btn btn-sm btn-info" onclick="verDetalhesMensagem(' + msg.id + ')">';
                        html += '<i class="fas fa-eye"></i> <?= gettext("Ver") ?></button>';
                        html += '</td>';
                        html += '</tr>';
                    });
                    
                    html += '</tbody></table>';
                    $('#content-mensagens .table-responsive').html(html);
                } else {
                    $('#content-mensagens .table-responsive').html('<p class="text-center text-muted"><?= gettext("Nenhuma mensagem encontrada") ?></p>');
                }
            },
            error: function(xhr) {
                console.error('Erro ao carregar mensagens:', xhr);
            }
        });
    }
    
    function verDetalhesMensagem(id) {
        bootbox.alert('ID da mensagem: ' + id);
    }
    
    function aplicarFiltrosMensagens() {
        carregarMensagens();
    }
    
    // Inicialização
    $(document).ready(function() {
        carregarMinisterios();
        carregarEstatisticas();
        
        // Carregar reuniões quando a tab for clicada
        $('#tab-reunioes').on('shown.bs.tab', function() {
            if (!$('#table-reunioes tbody').children().length || $('#table-reunioes tbody').text().includes('Carregando')) {
                carregarReunioes();
            }
        });
        
        // Carregar mensagens quando a tab for clicada
        $('#tab-mensagens').on('shown.bs.tab', function() {
            carregarMensagens();
        });
    });
    </script>
</body>
</html>
