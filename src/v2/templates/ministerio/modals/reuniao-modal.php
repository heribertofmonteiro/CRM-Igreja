<!-- Modal Criar/Editar Reunião -->
<div class="modal fade" id="modal-reuniao" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title" id="modal-reuniao-title"><?= gettext('Nova Reunião') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-reuniao" onsubmit="salvarReuniao(); return false;">
                <div class="modal-body">
                    <input type="hidden" id="reuniao-id" name="id">
                    
                    <div class="form-group">
                        <label for="reuniao-ministerio-id"><?= gettext('Ministério') ?> <span class="text-danger">*</span></label>
                        <select class="form-control" id="reuniao-ministerio-id" name="ministerio_id" required>
                            <option value=""><?= gettext('Selecione um ministério') ?></option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="reuniao-titulo"><?= gettext('Título') ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="reuniao-titulo" name="titulo" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reuniao-descricao"><?= gettext('Descrição') ?></label>
                        <textarea class="form-control" id="reuniao-descricao" name="descricao" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reuniao-data"><?= gettext('Data e Hora') ?> <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="reuniao-data" name="data_reuniao" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reuniao-local"><?= gettext('Local') ?></label>
                                <input type="text" class="form-control" id="reuniao-local" name="local">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group" id="grupo-ativo-reuniao" style="display: none;">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="reuniao-ativo" name="ativo" checked>
                            <label class="custom-control-label" for="reuniao-ativo"><?= gettext('Reunião Ativa') ?></label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= gettext('Cancelar') ?></button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> <?= gettext('Salvar') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script nonce="<?= SystemURLs::getCSPNonce() ?>">
function carregarMinisteriosSelect() {
    $.ajax({
        url: API_BASE + '/api',
        method: 'GET',
        success: function(response) {
            if (response.data) {
                let options = '<option value=""><?= gettext("Selecione um ministério") ?></option>';
                response.data.forEach(function(m) {
                    if (m.ativo == 1) {
                        options += '<option value="' + m.id + '">' + m.nome + '</option>';
                    }
                });
                $('#reuniao-ministerio-id').html(options);
            }
        }
    });
}

function salvarReuniao() {
    const id = $('#reuniao-id').val();
    const dados = {
        ministerio_id: $('#reuniao-ministerio-id').val(),
        titulo: $('#reuniao-titulo').val(),
        descricao: $('#reuniao-descricao').val(),
        data_reuniao: $('#reuniao-data').val(),
        local: $('#reuniao-local').val()
    };
    
    if (id) {
        dados.ativo = $('#reuniao-ativo').is(':checked') ? 1 : 0;
    }

    if (!dados.ministerio_id || !dados.titulo || !dados.data_reuniao) {
        bootbox.alert('<?= gettext("Preencha todos os campos obrigatórios") ?>');
        return;
    }

    const url = id ? API_BASE + '/reuniao/' + id + '/atualizar' : API_BASE + '/reuniao/criar';
    const method = 'POST';

    $.ajax({
        url: url,
        method: method,
        contentType: 'application/json',
        data: JSON.stringify(dados),
        success: function(response) {
            bootbox.alert(response.message || '<?= gettext("Reunião salva com sucesso") ?>', function() {
                $('#modal-reuniao').modal('hide');
                $('#form-reuniao')[0].reset();
                $('#reuniao-id').val('');
                $('#modal-reuniao-title').text('<?= gettext("Nova Reunião") ?>');
                $('#grupo-ativo-reuniao').hide();
                if (typeof carregarReunioes === 'function') {
                    carregarReunioes();
                }
            });
        },
        error: function(xhr) {
            const msg = xhr.responseJSON?.error || '<?= gettext("Erro ao salvar reunião") ?>';
            bootbox.alert(msg);
        }
    });
}

// Função global para editar reunião (chamada do dashboard)
window.editarReuniao = function(id) {
    $.ajax({
        url: API_BASE + '/reuniao',
        method: 'GET',
        success: function(response) {
            if (response.data) {
                const reuniao = response.data.find(r => r.id == id);
                if (reuniao) {
                    $('#reuniao-id').val(reuniao.id);
                    $('#reuniao-titulo').val(reuniao.titulo);
                    $('#reuniao-descricao').val(reuniao.descricao || '');
                    
                    // Format datetime-local (remover segundos se necessário)
                    const dataReuniao = new Date(reuniao.data_reuniao);
                    const dataFormatada = dataReuniao.toISOString().slice(0, 16);
                    $('#reuniao-data').val(dataFormatada);
                    
                    $('#reuniao-local').val(reuniao.local || '');
                    $('#reuniao-ativo').prop('checked', reuniao.ativo == 1);
                    
                    // Carregar ministérios primeiro
                    carregarMinisteriosSelect();
                    
                    // Aguardar carregar e então selecionar o ministério
                    setTimeout(function() {
                        $('#reuniao-ministerio-id').val(reuniao.ministerio_id);
                        $('#grupo-ativo-reuniao').show();
                        $('#modal-reuniao-title').text('<?= gettext("Editar Reunião") ?>');
                        $('#modal-reuniao').modal('show');
                    }, 500);
                }
            }
        },
        error: function() {
            bootbox.alert('<?= gettext("Erro ao carregar dados da reunião") ?>');
        }
    });
};

$(document).ready(function() {
    $('#modal-reuniao').on('show.bs.modal', function() {
        const id = $('#reuniao-id').val();
        if (!id) {
            carregarMinisteriosSelect();
            $('#grupo-ativo-reuniao').hide();
        }
    });
    
    $('#modal-reuniao').on('hidden.bs.modal', function() {
        $('#form-reuniao')[0].reset();
        $('#reuniao-id').val('');
        $('#modal-reuniao-title').text('<?= gettext("Nova Reunião") ?>');
        $('#grupo-ativo-reuniao').hide();
    });
});
</script>

