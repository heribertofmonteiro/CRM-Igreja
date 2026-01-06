<!-- Modal Criar Mensagem -->
<div class="modal fade" id="modal-mensagem" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title"><?= gettext('Nova Mensagem') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-mensagem" onsubmit="salvarMensagem(); return false;">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="mensagem-ministerio-id"><?= gettext('Ministério') ?> <span class="text-danger">*</span></label>
                        <select class="form-control" id="mensagem-ministerio-id" name="ministerio_id" required>
                            <option value=""><?= gettext('Selecione um ministério') ?></option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="mensagem-tipo"><?= gettext('Tipo') ?></label>
                        <select class="form-control" id="mensagem-tipo" name="tipo">
                            <option value="geral"><?= gettext('Geral') ?></option>
                            <option value="lembrete"><?= gettext('Lembrete de Reunião') ?></option>
                            <option value="anuncio"><?= gettext('Anúncio') ?></option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="mensagem-assunto"><?= gettext('Assunto') ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="mensagem-assunto" name="assunto" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="mensagem-conteudo"><?= gettext('Conteúdo') ?> <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="mensagem-conteudo" name="conteudo" rows="5" required></textarea>
                        <small class="form-text text-muted">
                            <?= gettext('Você pode usar placeholders: {{nome}}, {{titulo_reuniao}}, {{data_reuniao}}, {{local}}, {{link_rsvp}}') ?>
                        </small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mensagem-canal"><?= gettext('Canal') ?></label>
                                <select class="form-control" id="mensagem-canal" name="canal">
                                    <option value="email"><?= gettext('E-mail') ?></option>
                                    <option value="whatsapp"><?= gettext('WhatsApp') ?></option>
                                    <option value="sms"><?= gettext('SMS') ?></option>
                                    <option value="interno"><?= gettext('Mensagem Interna') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mensagem-status"><?= gettext('Status') ?></label>
                                <select class="form-control" id="mensagem-status" name="status">
                                    <option value="rascunho"><?= gettext('Rascunho') ?></option>
                                    <option value="agendado"><?= gettext('Agendado') ?></option>
                                    <option value="enviando"><?= gettext('Enviar Agora') ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group" id="grupo-agendamento" style="display: none;">
                        <label for="mensagem-data-agendamento"><?= gettext('Data de Agendamento') ?></label>
                        <input type="datetime-local" class="form-control" id="mensagem-data-agendamento" name="data_agendamento">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= gettext('Cancelar') ?></button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-paper-plane"></i> <?= gettext('Enviar') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script nonce="<?= SystemURLs::getCSPNonce() ?>">
function carregarMinisteriosSelectMensagem() {
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
                $('#mensagem-ministerio-id').html(options);
            }
        }
    });
}

function salvarMensagem() {
    const dados = {
        ministerio_id: $('#mensagem-ministerio-id').val(),
        tipo: $('#mensagem-tipo').val(),
        assunto: $('#mensagem-assunto').val(),
        conteudo: $('#mensagem-conteudo').val(),
        canal: $('#mensagem-canal').val(),
        status: $('#mensagem-status').val()
    };

    if ($('#mensagem-status').val() === 'agendado') {
        dados.data_agendamento = $('#mensagem-data-agendamento').val();
    }

    if (!dados.ministerio_id || !dados.assunto || !dados.conteudo) {
        bootbox.alert('<?= gettext("Preencha todos os campos obrigatórios") ?>');
        return;
    }

    $.ajax({
        url: API_BASE + '/mensagem/enviar',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(dados),
        success: function(response) {
            bootbox.alert(response.message || '<?= gettext("Mensagem criada com sucesso") ?>', function() {
                $('#modal-mensagem').modal('hide');
                $('#form-mensagem')[0].reset();
            });
        },
        error: function(xhr) {
            const msg = xhr.responseJSON?.error || '<?= gettext("Erro ao criar mensagem") ?>';
            bootbox.alert(msg);
        }
    });
}

$(document).ready(function() {
    $('#modal-mensagem').on('show.bs.modal', function() {
        carregarMinisteriosSelectMensagem();
    });
    
    $('#mensagem-status').on('change', function() {
        if ($(this).val() === 'agendado') {
            $('#grupo-agendamento').show();
        } else {
            $('#grupo-agendamento').hide();
        }
    });
});
</script>








