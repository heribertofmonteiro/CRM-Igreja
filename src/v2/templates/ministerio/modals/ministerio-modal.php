<!-- Modal Criar/Editar Ministério -->
<div class="modal fade" id="modal-ministerio" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title" id="modal-ministerio-title"><?= gettext('Novo Ministério') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-ministerio" onsubmit="salvarMinisterio(); return false;">
                <div class="modal-body">
                    <input type="hidden" id="ministerio-id" name="id">
                    
                    <div class="form-group">
                        <label for="ministerio-nome"><?= gettext('Nome do Ministério') ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="ministerio-nome" name="nome" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="ministerio-descricao"><?= gettext('Descrição') ?></label>
                        <textarea class="form-control" id="ministerio-descricao" name="descricao" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ministerio-lider-id"><?= gettext('Líder') ?> <span class="text-danger">*</span></label>
                                <select class="form-control personSearch" id="ministerio-lider-id" name="lider_id" style="width: 100%;" required></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ministerio-coordenador-id"><?= gettext('Coordenador') ?></label>
                                <select class="form-control personSearch" id="ministerio-coordenador-id" name="coordenador_id" style="width: 100%;"></select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="ministerio-ativo" name="ativo" checked>
                            <label class="custom-control-label" for="ministerio-ativo"><?= gettext('Ministério Ativo') ?></label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= gettext('Cancelar') ?></button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?= gettext('Salvar') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script nonce="<?= SystemURLs::getCSPNonce() ?>">
$(document).ready(function() {
    // Inicializar Select2 para busca de pessoas
    $('#ministerio-lider-id, #ministerio-coordenador-id').select2({
        minimumInputLength: 2,
        language: 'pt-BR',
        ajax: {
            url: function(params) {
                return window.CRM.root + '/api/persons/search/' + params.term;
            },
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    page: params.page
                };
            },
            processResults: function(data) {
                return {
                    results: data.map(function(person) {
                        return {
                            id: person.id,
                            text: person.displayName || (person.firstName + ' ' + person.lastName)
                        };
                    })
                };
            },
            cache: true
        },
        placeholder: '<?= gettext("Buscar pessoa...") ?>',
        allowClear: true
    });
});
</script>








