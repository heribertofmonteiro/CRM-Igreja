<?php
require_once __DIR__ . '/../../../Include/Header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">
                    <i class="fas fa-church"></i> 
                    <?= gettext('Ministérios') ?>
                </h3>
                <div class="card-tools">
                    <a href="create.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> 
                        <?= gettext('Novo Ministério') ?>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i>
                        <?= $_SESSION['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= $_SESSION['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="ministerios-table">
                        <thead class="table-dark">
                            <tr>
                                <th><?= gettext('Nome') ?></th>
                                <th><?= gettext('Líder') ?></th>
                                <th><?= gettext('Coordenador') ?></th>
                                <th><?= gettext('Status') ?></th>
                                <th><?= gettext('Criado em') ?></th>
                                <th class="text-center"><?= gettext('Ações') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ministerios)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p><?= gettext('Nenhum ministério encontrado.') ?></p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ministerios as $ministerio): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($ministerio->getNome()) ?></strong>
                                            <?php if ($ministerio->getDescricao()): ?>
                                                <br>
                                                <small class="text-muted"><?= htmlspecialchars($ministerio->getDescricao()) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($ministerio->lider_nome ?? 'Não definido') ?></td>
                                        <td><?= htmlspecialchars($ministerio->coordenador_nome ?? 'Não definido') ?></td>
                                        <td><?= $ministerio->getStatusBadge() ?></td>
                                        <td><?= $ministerio->getFormattedCreationDate() ?></td>
                                        <td class="text-center"><?= $ministerio->getActionsHtml() ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
$(document).ready(function() {
    // Inicializar DataTable
    $('#ministerios-table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json'
        },
        responsive: true,
        order: [[0, 'asc']]
    });
});

function editMinisterio(id) {
    window.location.href = 'edit.php?id=' + id;
}

function viewMinisterio(id) {
    window.location.href = 'show.php?id=' + id;
}

function deleteMinisterio(id) {
    if (confirm('<?= gettext('Tem certeza que deseja excluir este ministério?') ?>')) {
        $.post('destroy.php', { id: id }, function(response) {
            window.location.reload();
        });
    }
}
</script>

<?php require_once __DIR__ . '/../../../Include/Footer.php'; ?>
