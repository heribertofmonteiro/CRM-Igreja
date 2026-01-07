<?php
require_once __DIR__ . '/../../../Include/Header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-plus"></i> 
                    <?= gettext('Novo Ministério') ?>
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="store.php" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nome" class="form-label">
                                    <?= gettext('Nome do Ministério') ?> <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nome" 
                                       name="nome" 
                                       required
                                       maxlength="150"
                                       placeholder="<?= gettext('Ex: Ministério de Louvor') ?>">
                                <div class="invalid-feedback">
                                    <?= gettext('Por favor, informe o nome do ministério.') ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">
                                    <?= gettext('Status') ?>
                                </label>
                                <select class="form-select" id="status" name="status">
                                    <option value="ativo" selected>
                                        <?= gettext('Ativo') ?>
                                    </option>
                                    <option value="inativo">
                                        <?= gettext('Inativo') ?>
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="descricao" class="form-label">
                                    <?= gettext('Descrição') ?>
                                </label>
                                <textarea class="form-control" 
                                          id="descricao" 
                                          name="descricao" 
                                          rows="4"
                                          placeholder="<?= gettext('Descreva as atividades e objetivos deste ministério...') ?>"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="lider_id" class="form-label">
                                    <?= gettext('Líder do Ministério') ?> <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="lider_id" name="lider_id" required>
                                    <option value="">
                                        <?= gettext('Selecione um líder...') ?>
                                    </option>
                                    <?php
                                    // Lista de usuários disponíveis (simulado)
                                    $usuarios = [
                                        1 => 'Administrador',
                                        2 => 'Pastor Principal',
                                        3 => 'Líder de Louvor'
                                    ];
                                    foreach ($usuarios as $id => $nome):
                                    ?>
                                        <option value="<?= $id ?>"><?= htmlspecialchars($nome) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= gettext('Por favor, selecione um líder.') ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="coordenador_id" class="form-label">
                                    <?= gettext('Coordenador') ?>
                                </label>
                                <select class="form-select" id="coordenador_id" name="coordenador_id">
                                    <option value="">
                                        <?= gettext('Selecione um coordenador (opcional)...') ?>
                                    </option>
                                    <?php
                                    foreach ($usuarios as $id => $nome):
                                    ?>
                                        <option value="<?= $id ?>"><?= htmlspecialchars($nome) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">
                                    <small class="text-muted">
                                        <?= gettext('Coordenador é opcional e pode ajudar na organização.') ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> 
                                        <?= gettext('Criar Ministério') ?>
                                    </button>
                                    <a href="index.php" class="btn btn-secondary ms-2">
                                        <i class="fas fa-times"></i> 
                                        <?= gettext('Cancelar') ?>
                                    </a>
                                </div>
                                <div>
                                    <small class="text-muted">
                                        <span class="text-danger">*</span> 
                                        <?= gettext('Campos obrigatórios') ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
$(document).ready(function() {
    // Validação do formulário
    'use strict';
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
});
</script>

<?php require_once __DIR__ . '/../../../Include/Footer.php'; ?>
