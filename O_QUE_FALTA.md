# 📋 Lista do Que Ainda Falta Implementar

## 🔴 PRIORIDADE ALTA (Funcionalidades Essenciais)

### 1. API - Endpoint de Histórico de Mensagens ❌
**Solicitado:** `GET /ministerio/mensagens/{id}` → histórico mensagens
**Status:** ❌ **NÃO IMPLEMENTADO**

**O que falta:**
- Rota para listar mensagens de um ministério ou reunião
- Retornar histórico completo de mensagens enviadas

**Arquivo:** `src/modules/ministerio/v2/routes/ministerio.php`

---

### 2. API - Listar Reuniões ❌
**Solicitado:** Endpoint para listar reuniões no dashboard
**Status:** ❌ **NÃO IMPLEMENTADO**

**O que falta:**
- `GET /v2/ministerio/reuniao` ou `/reuniao/listar` - Listar todas as reuniões
- Possibilidade de filtrar por ministério ou data

**Arquivo:** `src/modules/ministerio/v2/routes/ministerio.php`

---

### 3. Frontend - Carregar Reuniões na Tabela ❌
**Status:** ❌ **APENAS ESQUELETO**

**O que falta:**
- Implementar `carregarReunioes()` no JavaScript
- Conectar com API de reuniões
- Popular tabela `#table-reunioes` com dados reais

**Arquivo:** `src/v2/templates/ministerio/dashboard.php` (linha ~304)

---

### 4. Frontend - Página de Detalhes do Ministério ❌
**Status:** ❌ **NÃO IMPLEMENTADO**

**O que falta:**
- Criar template `src/v2/templates/ministerio/detalhes.php`
- Mostrar informações do ministério
- Lista de membros
- Lista de reuniões do ministério
- Lista de mensagens enviadas
- Formulário para adicionar membros
- Botões de ação (editar, excluir)

**Arquivo:** Novo arquivo necessário

---

### 5. API - Atualizar Reunião ❌
**Status:** ❌ **NÃO IMPLEMENTADO**

**O que falta:**
- `PUT/POST /v2/ministerio/reuniao/{id}/atualizar` - Editar reunião
- Validar dados de entrada
- Atualizar no banco

---

### 6. API - Excluir Ministério ❌
**Status:** ❌ **NÃO IMPLEMENTADO**

**O que falta:**
- `DELETE /v2/ministerio/{id}` - Excluir ministério (soft delete)
- Atualizar status `ativo = 0`

---

### 7. API - Excluir/Atualizar Reunião ❌
**Status:** ❌ **NÃO IMPLEMENTADO**

**O que falta:**
- `DELETE /v2/ministerio/reuniao/{id}` - Cancelar reunião
- `PUT/POST /v2/ministerio/reuniao/{id}/atualizar` - Atualizar reunião

---

### 8. API - Remover Membro do Ministério ❌
**Status:** ❌ **NÃO IMPLEMENTADO**

**O que falta:**
- `DELETE /v2/ministerio/{id}/membros/{membro_id}` - Remover membro
- Atualizar status `ativo = 0` em `ministerio_membros`

---

## 🟡 PRIORIDADE MÉDIA (Funcionalidades Importantes)

### 9. Frontend - Interface de Mensagens Completa ❌
**Status:** ⚠️ **PARCIALMENTE IMPLEMENTADO**

**O que falta:**
- Carregar lista de mensagens na tabela
- Mostrar status das mensagens (pendente, enviando, enviado, falhou)
- Filtrar mensagens por status, data, ministério
- Visualizar detalhes de uma mensagem
- Ver histórico de envios por mensagem

**Arquivo:** `src/v2/templates/ministerio/dashboard.php` (tab mensagens)

---

### 10. Frontend - Modal de Edição de Reunião ❌
**Status:** ❌ **NÃO IMPLEMENTADO**

**O que falta:**
- Modal para editar reunião existente
- Preencher formulário com dados atuais
- Atualizar via API

**Arquivo:** `src/v2/templates/ministerio/modals/reuniao-modal.php`

---

### 11. API - Listar Mensagens ❌
**Status:** ❌ **NÃO IMPLEMENTADO**

**O que falta:**
- `GET /v2/ministerio/mensagem` - Listar mensagens
- Filtrar por ministério, status, data
- Paginação

---

### 12. Frontend - Melhorar Funcionalidade de Adicionar Membros ❌
**Status:** ⚠️ **APENAS API, SEM UI**

**O que falta:**
- Interface no frontend para adicionar membros
- Modal ou formulário na página de detalhes
- Select2 para buscar pessoas
- Campo para função do membro

---

## 🟢 PRIORIDADE BAIXA (Opcional/Melhoria)

### 13. Testes Unitários ❌
**Solicitado:** PHPUnit tests
**Status:** ❌ **NÃO IMPLEMENTADO**

**O que falta:**
- `tests/MinisterioTest.php` - Testar CRUD de ministérios
- `tests/ReuniaoTest.php` - Testar CRUD de reuniões e RSVP
- `tests/MensagemTest.php` - Testar criação e envio de mensagens
- Testes de permissões e segurança

**Arquivo:** Criar diretório `tests/` e arquivos de teste

---

### 14. API - Endpoints Adicionais para RSVP ❌
**Status:** ⚠️ **BÁSICO IMPLEMENTADO**

**O que falta:**
- Página HTML para RSVP público (não apenas API)
- Template bonito para confirmar presença via link
- Mostrar detalhes da reunião

**Arquivo:** Novo template necessário

---

### 15. Frontend - Relatórios e Estatísticas ❌
**Status:** ⚠️ **ESTATÍSTICAS BÁSICAS IMPLEMENTADAS**

**O que falta:**
- Gráficos de participação em reuniões
- Estatísticas de envio de mensagens
- Relatório de presença por ministério
- Exportar dados (CSV, PDF)

---

### 16. API - Endpoints de Busca/Filtro ❌
**Status:** ❌ **NÃO IMPLEMENTADO**

**O que falta:**
- Buscar ministérios por nome
- Filtrar reuniões por data
- Buscar mensagens por assunto/conteúdo

---

## 📝 RESUMO POR CATEGORIA

### API Endpoints Faltantes:
1. ❌ `GET /v2/ministerio/reuniao` - Listar reuniões
2. ❌ `GET /v2/ministerio/mensagem` - Listar mensagens
3. ❌ `GET /v2/ministerio/mensagens/{id}` - Histórico de mensagens
4. ❌ `PUT /v2/ministerio/reuniao/{id}/atualizar` - Atualizar reunião
5. ❌ `DELETE /v2/ministerio/reuniao/{id}` - Excluir reunião
6. ❌ `DELETE /v2/ministerio/{id}` - Excluir ministério
7. ❌ `DELETE /v2/ministerio/{id}/membros/{membro_id}` - Remover membro

### Frontend Faltante:
1. ❌ Página de detalhes do ministério (`detalhes.php`)
2. ❌ Funcionalidade completa de listar reuniões
3. ❌ Funcionalidade completa de listar mensagens
4. ❌ Modal para editar reunião
5. ❌ Interface para adicionar/remover membros
6. ❌ Página pública de RSVP

### Testes:
1. ❌ Todos os testes unitários

### Documentação:
1. ⚠️ Instruções de configuração de cron jobs (documentar)

---

## 🎯 TOTAL DE ITENS FALTANTES

- **API Endpoints:** 7 rotas
- **Frontend/Templates:** 6 páginas/funcionalidades
- **Testes:** 3 arquivos de teste
- **Documentação:** 1 item

**Total:** ~17 itens principais faltando

---

## ✅ ITENS QUE NÃO SÃO NECESSÁRIOS (Por Design)

- **Controllers separados** - O projeto usa padrão Slim Framework (rotas diretas)
- **Views em `/views/`** - Templates estão em `/v2/templates/` (padrão do projeto)
- **MembroMinisterio.php separado** - Lógica está em `Ministerio.php`

---

## 🚀 SUGESTÃO DE ORDEM DE IMPLEMENTAÇÃO

1. **Primeiro:** API para listar reuniões e mensagens (necessário para frontend)
2. **Segundo:** Página de detalhes do ministério (funcionalidade principal)
3. **Terceiro:** Completar frontend das tabs (reuniões e mensagens)
4. **Quarto:** Endpoints de atualização/exclusão
5. **Quinto:** Testes unitários (se necessário)





