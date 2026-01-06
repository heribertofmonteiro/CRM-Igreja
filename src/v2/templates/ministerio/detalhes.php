<?php
/**
 * Página de Detalhes do Ministério
 * URL: /v2/ministerio/{id}/detalhes
 */
use ChurchCRM\Authentication\AuthenticationManager;
use ChurchCRM\dto\SystemURLs;
?>
<!DOCTYPE html>
<html>
<head>
    <?php include __DIR__ . '/../../Include/Header-HTML-Scripts.php'; ?>
    <title><?= gettext('Detalhes do Ministério') ?> - <?= $sRootPath ?></title>
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
                            <h1><i class="fas fa-church"></i> <?= gettext('Detalhes do Ministério') ?></h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?= $sRootPath ?>/v2/dashboard"><?= gettext('Dashboard') ?></a></li>
                                <li class="breadcrumb-item"><a href="<?= $sRootPath ?>/v2/ministerio"><?= gettext('Ministério') ?></a></li>
                                <li class="breadcrumb-item active"><?= gettext('Detalhes') ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <!-- Informações do Ministério -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title" id="ministerio-nome-header">
                                        <i class="fas fa-info-circle"></i> <?= gettext('Informações') ?>
                                    </h3>
                                    <?php if (AuthenticationManager::getCurrentUser()->isAdmin() || AuthenticationManager::getCurrentUser()->isEditRecordsEnabled()): ?>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-warning btn-sm" onclick="editarMinisterio()">
                                            <i class="fas fa-edit"></i> <?= gettext('Editar') ?>
                                        </button>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body" id="ministerio-info">
                                    <div class="text-center">
                                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                                        <p><?= gettext('Carregando...') ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs: Membros, Reuniões, Mensagens -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-primary card-tabs">
                                <div class="card-header p-0 pt-1">
                                    <ul class="nav nav-tabs" id="detalhes-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="tab-membros" data-toggle="tab" href="#content-membros" role="tab">
                                                <i class="fas fa-users"></i> <?= gettext('Membros') ?>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="tab-reunioes-ministerio" data-toggle="tab" href="#content-reunioes-ministerio" role="tab">
                                                <i class="fas fa-calendar-alt"></i> <?= gettext('Reuniões') ?>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="tab-mensagens-ministerio" data-toggle="tab" href="#content-mensagens-ministerio" role="tab">
                                                <i class="fas fa-envelope"></i> <?= gettext('Mensagens') ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="detalhes-tabs-content">
                                        <!-- Tab Membros -->
                                        <div class="tab-pane fade show active" id="content-membros" role="tabpanel">
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <?php if (AuthenticationManager::getCurrentUser()->isAdmin() || AuthenticationManager::getCurrentUser()->isEditRecordsEnabled()): ?>
                                                    <button type="button" class="btn btn-primary" onclick="abrirModalAdicionarMembro()">
                                                        <i class="fas fa-plus"></i> <?= gettext('Adicionar Membro') ?>
                                                    </button>
                                                    <?php endif; ?>
                                                    <button type="button" class="btn btn-secondary" onclick="carregarMembros()">
                                                        <i class="fas fa-sync"></i> <?= gettext('Atualizar') ?>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table id="table-membros" class="table table-bordered table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th><?= gettext('Nome') ?></th>
                                                            <th><?= gettext('Email') ?></th>
                                                            <th><?= gettext('Telefone') ?></th>
                                                            <th><?= gettext('Função') ?></th>
                                                            <th><?= gettext('Data Entrada') ?></th>
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
                                        <div class="tab-pane fade" id="content-reunioes-ministerio" role="tabpanel">
                                            <div class="table-responsive">
                                                <table id="table-reunioes-ministerio" class="table table-bordered table-striped table-hover">
                                                    <thead>
                                                        <tr>
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
                                        <div class="tab-pane fade" id="content-mensagens-ministerio" role="tabpanel">
                                            <div class="table-responsive">
                                                <table id="table-mensagens-ministerio" class="table table-bordered table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th><?= gettext('Assunto') ?></th>
                                                            <th><?= gettext('Tipo') ?></th>
                                                            <th><?= gettext('Canal') ?></th>
                                                            <th><?= gettext('Status') ?></th>
                                                            <th><?= gettext('Data') ?></th>
                                                            <th><?= gettext('Ações') ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Carregado via AJAX -->
                                                    </tbody>
                                                </table>
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
    <?php include __DIR__ . '/modals/adicionar-membro-modal.php'; ?>
    
    <script nonce="<?= SystemURLs::getCSPNonce() ?>">
    const API_BASE = '<?= $sRootPath ?>/v2/ministerio';
    const MINISTERIO_ID = <?= $ministerioId ?>;
    let tableMembros, tableReunioesMinisterio, tableMensagensMinisterio;
    
    // Verificar se window.CRM está disponível
    if (typeof window.CRM === 'undefined') {
        window.CRM = {
            root: '<?= $sRootPath ?>'
        };
    }

    // Carregar informações do ministério
    function carregarInformacoes() {
        $.ajax({
            url: API_BASE + '/' + MINISTERIO_ID + '/detalhes',
            method: 'GET',
            success: function(response) {
                if (response.data) {
                    const m = response.data;
                    let html = '<div class="row">';
                    html += '<div class="col-md-6">';
                    html += '<p><strong><?= gettext("Nome:") ?></strong> ' + (m.nome || '') + '</p>';
                    html += '<p><strong><?= gettext("Descrição:") ?></strong> ' + (m.descricao || '-') + '</p>';
                    html += '<p><strong><?= gettext("Líder:") ?></strong> ' + (m.lider_nome || '') + ' ' + (m.lider_sobrenome || '') + '</p>';
                    if (m.coordenador_nome) {
                        html += '<p><strong><?= gettext("Coordenador:") ?></strong> ' + m.coordenador_nome + ' ' + (m.coordenador_sobrenome || '') + '</p>';
                    }
                    html += '</div>';
                    html += '<div class="col-md-6">';
                    html += '<p><strong><?= gettext("Status:") ?></strong> ';
                    html += m.ativo == 1 
                        ? '<span class="badge badge-success"><?= gettext("Ativo") ?></span>'
                        : '<span class="badge badge-danger"><?= gettext("Inativo") ?></span>';
                    html += '</p>';
                    html += '<p><strong><?= gettext("Total de Membros:") ?></strong> ' + (m.membros ? m.membros.length : 0) + '</p>';
                    html += '</div>';
                    html += '</div>';
                    
                    $('#ministerio-info').html(html);
                    $('#ministerio-nome-header').html('<i class="fas fa-church"></i> ' + (m.nome || ''));
                }
            },
            error: function() {
                bootbox.alert('<?= gettext("Erro ao carregar informações do ministério") ?>');
            }
        });
    }

    // Carregar membros
    function carregarMembros() {
        $.ajax({
            url: API_BASE + '/' + MINISTERIO_ID + '/detalhes',
            method: 'GET',
            success: function(response) {
                if (response.data && response.data.membros) {
                    let tbody = '';
                    response.data.membros.forEach(function(membro) {
                        tbody += '<tr>';
                        tbody += '<td>' + (membro.per_FirstName || '') + ' ' + (membro.per_LastName || '') + '</td>';
                        tbody += '<td>' + (membro.per_Email || '-') + '</td>';
                        tbody += '<td>' + (membro.per_CellPhone || membro.per_HomePhone || '-') + '</td>';
                        tbody += '<td>' + (membro.funcao || '-') + '</td>';
                        tbody += '<td>' + (membro.data_entrada || '-') + '</td>';
                        tbody += '<td>';
                        tbody += '<?php if (AuthenticationManager::getCurrentUser()->isAdmin() || AuthenticationManager::getCurrentUser()->isEditRecordsEnabled()): ?>';
                        tbody += '<button class="btn btn-sm btn-danger" onclick="removerMembro(' + membro.membro_id + ')">';
                        tbody += '<i class="fas fa-trash"></i> <?= gettext("Remover") ?></button>';
                        tbody += '<?php endif; ?>';
                        tbody += '</td>';
                        tbody += '</tr>';
                    });
                    
                    $('#table-membros tbody').html(tbody);
                    
                    if (!tableMembros) {
                        tableMembros = $('#table-membros').DataTable({
                            language: {
                                url: '<?= $sRootPath ?>/skin/v2/external/js/Portuguese-Brasil.json'
                            },
                            order: [[0, 'asc']],
                            pageLength: 25
                        });
                    } else {
                        tableMembros.draw();
                    }
                } else {
                    $('#table-membros tbody').html('<tr><td colspan="6" class="text-center text-muted"><?= gettext("Nenhum membro encontrado") ?></td></tr>');
                }
            }
        });
    }

    // Carregar reuniões do ministério
    function carregarReunioesMinisterio() {
        $.ajax({
            url: API_BASE + '/reuniao?ministerio_id=' + MINISTERIO_ID,
            method: 'GET',
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    let tbody = '';
                    response.data.forEach(function(reuniao) {
                        const dataFormatada = new Date(reuniao.data_reuniao).toLocaleString('pt-BR');
                        tbody += '<tr>';
                        tbody += '<td><strong>' + (reuniao.titulo || '') + '</strong></td>';
                        tbody += '<td>' + dataFormatada + '</td>';
                        tbody += '<td>' + (reuniao.local || '-') + '</td>';
                        tbody += '<td><span class="badge badge-info">' + (reuniao.total_participantes || 0) + '</span></td>';
                        tbody += '<td>';
                        tbody += '<button class="btn btn-sm btn-info" onclick="window.location.href=\'<?= $sRootPath ?>/v2/ministerio/reuniao/' + reuniao.id + '\'">';
                        tbody += '<i class="fas fa-eye"></i> <?= gettext("Ver") ?></button>';
                        tbody += '</td>';
                        tbody += '</tr>';
                    });
                    
                    $('#table-reunioes-ministerio tbody').html(tbody);
                    
                    if (!tableReunioesMinisterio) {
                        tableReunioesMinisterio = $('#table-reunioes-ministerio').DataTable({
                            language: {
                                url: '<?= $sRootPath ?>/skin/v2/external/js/Portuguese-Brasil.json'
                            },
                            order: [[1, 'desc']],
                            pageLength: 25
                        });
                    } else {
                        tableReunioesMinisterio.draw();
                    }
                } else {
                    $('#table-reunioes-ministerio tbody').html('<tr><td colspan="5" class="text-center text-muted"><?= gettext("Nenhuma reunião encontrada") ?></td></tr>');
                }
            }
        });
    }

    // Carregar mensagens do ministério
    function carregarMensagensMinisterio() {
        $.ajax({
            url: API_BASE + '/mensagem?ministerio_id=' + MINISTERIO_ID,
            method: 'GET',
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    let tbody = '';
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
                        
                        tbody += '<tr>';
                        tbody += '<td>' + (msg.assunto || '') + '</td>';
                        tbody += '<td>' + (msg.tipo || 'geral') + '</td>';
                        tbody += '<td>' + (msg.canal || 'email') + '</td>';
                        tbody += '<td><span class="badge badge-' + statusColor + '">' + (msg.status || '') + '</span></td>';
                        tbody += '<td>' + dataFormatada + '</td>';
                        tbody += '<td>';
                        tbody += '<button class="btn btn-sm btn-info" onclick="verDetalhesMensagem(' + msg.id + ')">';
                        tbody += '<i class="fas fa-eye"></i> <?= gettext("Ver") ?></button>';
                        tbody += '</td>';
                        tbody += '</tr>';
                    });
                    
                    $('#table-mensagens-ministerio tbody').html(tbody);
                    
                    if (!tableMensagensMinisterio) {
                        tableMensagensMinisterio = $('#table-mensagens-ministerio').DataTable({
                            language: {
                                url: '<?= $sRootPath ?>/skin/v2/external/js/Portuguese-Brasil.json'
                            },
                            order: [[4, 'desc']],
                            pageLength: 25
                        });
                    } else {
                        tableMensagensMinisterio.draw();
                    }
                } else {
                    $('#table-mensagens-ministerio tbody').html('<tr><td colspan="6" class="text-center text-muted"><?= gettext("Nenhuma mensagem encontrada") ?></td></tr>');
                }
            }
        });
    }

    // Adicionar membro
    function abrirModalAdicionarMembro() {
        $('#modal-adicionar-membro').modal('show');
    }

    function salvarMembro() {
        const dados = {
            membro_id: $('#membro-id').val(),
            funcao: $('#membro-funcao').val()
        };

        if (!dados.membro_id) {
            bootbox.alert('<?= gettext("Selecione um membro") ?>');
            return;
        }

        $.ajax({
            url: API_BASE + '/' + MINISTERIO_ID + '/membros/adicionar',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(dados),
            success: function(response) {
                bootbox.alert(response.message || '<?= gettext("Membro adicionado com sucesso") ?>', function() {
                    $('#modal-adicionar-membro').modal('hide');
                    $('#form-adicionar-membro')[0].reset();
                    carregarMembros();
                    carregarInformacoes();
                });
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.error || '<?= gettext("Erro ao adicionar membro") ?>';
                bootbox.alert(msg);
            }
        });
    }

    // Remover membro
    function removerMembro(membroId) {
        bootbox.confirm({
            message: '<?= gettext("Tem certeza que deseja remover este membro?") ?>',
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
                        url: API_BASE + '/' + MINISTERIO_ID + '/membros/' + membroId + '/remover',
                        method: 'POST',
                        success: function(response) {
                            bootbox.alert(response.message || '<?= gettext("Membro removido com sucesso") ?>', function() {
                                carregarMembros();
                                carregarInformacoes();
                            });
                        },
                        error: function(xhr) {
                            const msg = xhr.responseJSON?.error || '<?= gettext("Erro ao remover membro") ?>';
                            bootbox.alert(msg);
                        }
                    });
                }
            }
        });
    }

    function editarMinisterio() {
        // Carregar dados do ministério e abrir modal
        $.ajax({
            url: API_BASE + '/' + MINISTERIO_ID + '/detalhes',
            method: 'GET',
            success: function(response) {
                if (response.data) {
                    const m = response.data;
                    $('#ministerio-id').val(m.id);
                    $('#ministerio-nome').val(m.nome);
                    $('#ministerio-descricao').val(m.descricao || '');
                    
                    // Aguardar Select2 inicializar
                    setTimeout(function() {
                        $('#ministerio-lider-id').val(m.lider_id).trigger('change');
                        if (m.coordenador_id) {
                            $('#ministerio-coordenador-id').val(m.coordenador_id).trigger('change');
                        }
                    }, 500);
                    
                    $('#ministerio-ativo').prop('checked', m.ativo == 1);
                    $('#modal-ministerio-title').text('<?= gettext("Editar Ministério") ?>');
                    $('#modal-ministerio').modal('show');
                }
            }
        });
    }
    
    // Salvar ministério (função global do modal)
    window.salvarMinisterio = function() {
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
                    carregarInformacoes();
                    carregarMembros();
                });
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.error || '<?= gettext("Erro ao salvar ministério") ?>';
                bootbox.alert(msg);
            }
        });
    };

    function verDetalhesMensagem(id) {
        bootbox.alert('ID da mensagem: ' + id);
    }

    // Inicialização
    $(document).ready(function() {
        carregarInformacoes();
        carregarMembros();
        
        // Carregar quando tabs forem clicadas
        $('#tab-reunioes-ministerio').on('shown.bs.tab', function() {
            carregarReunioesMinisterio();
        });
        
        $('#tab-mensagens-ministerio').on('shown.bs.tab', function() {
            carregarMensagensMinisterio();
        });
    });
    </script>
</body>
</html>

