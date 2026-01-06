<!-- Modal Adicionar Membro -->
<div class="modal fade" id="modal-adicionar-membro" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title"><?= gettext('Adicionar Membro ao Ministério') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-adicionar-membro" onsubmit="salvarMembro(); return false;">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="membro-id"><?= gettext('Pessoa') ?> <span class="text-danger">*</span></label>
                        <select class="form-control personSearch" id="membro-id" name="membro_id" style="width: 100%;" required></select>
                    </div>
                    
                    <div class="form-group">
                        <label for="membro-funcao"><?= gettext('Função') ?></label>
                        <input type="text" class="form-control" id="membro-funcao" name="funcao" placeholder="<?= gettext('Ex: Membro, Líder Auxiliar, etc.') ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= gettext('Cancelar') ?></button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?= gettext('Adicionar') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script nonce="<?= SystemURLs::getCSPNonce() ?>">
$(document).ready(function() {
    // Inicializar Select2 para busca de pessoas
    $('#membro-id').select2({
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





