# 📘 Módulo Ministério & Comunicação - Documentação Técnica

## 🎯 Visão Geral

O **Módulo Ministério & Comunicação** é uma extensão completa para o ChurchCRM que permite gerenciar ministérios, membros, reuniões e comunicação interna da igreja.

---

## 📋 Estrutura do Módulo

### **Arquitetura MVC**
```
src/modules/ministerio/
├── controllers/
│   ├── MinisterioController.php     # Gestão de ministérios
│   ├── MensagemController.php      # Sistema de mensagens
│   └── ReuniaoController.php       # Gestão de reuniões
├── models/
│   ├── MinisterioModel.php        # Model de dados
│   ├── MensagemModel.php         # Model de mensagens
│   └── ReuniaoModel.php         # Model de reuniões
├── views/
│   ├── ministerio/               # Templates de ministérios
│   ├── mensagem/                 # Templates de mensagens
│   └── reuniao/                 # Templates de reuniões
├── Security.php                   # Sistema RBAC
└── config.php                    # Configurações do módulo
```

---

## 🗄️ Banco de Dados

### **Tabelas Criadas**

#### **1. ministerios**
Armazena informações dos ministérios da igreja.

| Campo | Tipo | Descrição |
|-------|-------|------------|
| id | BIGINT UNSIGNED AUTO_INCREMENT | PK |
| nome | VARCHAR(255) NOT NULL | Nome do ministério |
| descricao | TEXT | Descrição detalhada |
| lider_id | INT(11) UNSIGNED NOT NULL | FK para usuário líder |
| coordenador_id | INT(11) UNSIGNED | FK para coordenador (opcional) |
| ativo | TINYINT(1) DEFAULT 1 | Status do ministério |
| criado_em | DATETIME NOT NULL | Data de criação |
| atualizado_em | DATETIME | Última atualização |

#### **2. ministerio_membros**
Gerencia membros de cada ministério.

| Campo | Tipo | Descrição |
|-------|-------|------------|
| id | BIGINT UNSIGNED AUTO_INCREMENT | PK |
| ministerio_id | INT(11) UNSIGNED NOT NULL | FK para ministerios |
| membro_id | INT(11) UNSIGNED NOT NULL | FK para usuários |
| funcao | VARCHAR(100) | Função no ministério |
| data_entrada | DATE NOT NULL | Data de entrada |
| data_saida | DATE | Data de saída |
| ativo | TINYINT(1) DEFAULT 1 | Status do membro |
| criado_em | DATETIME NOT NULL | Data de criação |

#### **3. ministerio_reunioes**
Controla reuniões dos ministérios.

| Campo | Tipo | Descrição |
|-------|-------|------------|
| id | BIGINT UNSIGNED AUTO_INCREMENT | PK |
| ministerio_id | INT(11) UNSIGNED NOT NULL | FK para ministerios |
| titulo | VARCHAR(255) NOT NULL | Título da reunião |
| descricao | TEXT | Descrição detalhada |
| data_reuniao | DATETIME NOT NULL | Data/hora da reunião |
| local | VARCHAR(255) | Local da reunião |
| criado_por | INT(11) UNSIGNED NOT NULL | FK para usuários |
| ativo | TINYINT(1) DEFAULT 1 | Status |
| criado_em | DATETIME NOT NULL | Data de criação |
| atualizado_em | DATETIME | Última atualização |

#### **4. ministerio_reunioes_participantes**
Gerencia presença em reuniões.

| Campo | Tipo | Descrição |
|-------|-------|------------|
| id | BIGINT UNSIGNED AUTO_INCREMENT | PK |
| reuniao_id | INT(11) UNSIGNED NOT NULL | FK para reuniões |
| membro_id | INT(11) UNSIGNED NOT NULL | FK para usuários |
| status | ENUM('pendente','confirmado','cancelado','presente','ausente') | Status |
| token_rsvp | VARCHAR(64) | Token para confirmação |
| data_confirmacao | DATETIME | Data de confirmação |
| observacoes | TEXT | Observações |
| criado_em | DATETIME NOT NULL | Data de criação |

#### **5. ministerio_mensagens**
Sistema de comunicação interna.

| Campo | Tipo | Descrição |
|-------|-------|------------|
| id | BIGINT UNSIGNED AUTO_INCREMENT | PK |
| ministerio_id | INT(11) UNSIGNED NOT NULL | FK para ministerios |
| reuniao_id | INT(11) UNSIGNED | FK para reuniões (opcional) |
| tipo | ENUM('geral','reuniao','lembrete','aniversario') | Tipo |
| assunto | VARCHAR(255) NOT NULL | Assunto da mensagem |
| conteudo | TEXT NOT NULL | Conteúdo da mensagem |
| canal | ENUM('email','whatsapp','sms','interno') | Canal de envio |
| status | ENUM('rascunho','agendado','enviando','enviado','falhou') | Status |
| data_agendamento | DATETIME | Data de agendamento |
| data_envio | DATETIME | Data de envio |
| criado_por | INT(11) UNSIGNED NOT NULL | FK para usuários |
| criado_em | DATETIME NOT NULL | Data de criação |

#### **6. ministerio_mensagens_envio**
Controle individual de envios.

| Campo | Tipo | Descrição |
|-------|-------|------------|
| id | BIGINT UNSIGNED AUTO_INCREMENT | PK |
| mensagem_id | INT(11) UNSIGNED NOT NULL | FK para mensagens |
| destinatario_id | INT(11) UNSIGNED NOT NULL | FK para usuários |
| canal | ENUM('email','whatsapp','sms','interno') NOT NULL | Canal |
| status | ENUM('pendente','enviando','enviado','falhou','cancelado') | Status |
| tentativas | INT(3) DEFAULT 0 | Número de tentativas |
| erro | TEXT | Mensagem de erro |
| data_envio | DATETIME | Data de envio |
| data_tentativa | DATETIME | Data da tentativa |
| criado_em | DATETIME NOT NULL | Data de criação |

#### **7. ministerio_logs**
Auditoria de ações do sistema.

| Campo | Tipo | Descrição |
|-------|-------|------------|
| id | BIGINT UNSIGNED AUTO_INCREMENT | PK |
| usuario_id | INT(11) UNSIGNED | FK para usuários |
| acao | VARCHAR(100) NOT NULL | Ação executada |
| tabela | VARCHAR(50) NOT NULL | Tabela afetada |
| registro_id | INT(11) UNSIGNED | ID do registro |
| dados_antigos | JSON | Dados antes da alteração |
| dados_novos | JSON | Dados após alteração |
| ip_origem | VARCHAR(45) | IP de origem |
| user_agent | TEXT | User agent |
| criado_em | DATETIME NOT NULL | Data de criação |

---

## 🔐 Sistema de RBAC (Role-Based Access Control)

### **Permissões Definidas**

| Permissão | Código | Descrição |
|-----------|---------|------------|
| ministerio_ver | VER_MINISTERIOS | Visualizar lista de ministérios |
| ministerio_criar | CRIAR_MINISTERIO | Criar novos ministérios |
| ministerio_editar | EDITAR_MINISTERIO | Editar ministérios existentes |
| ministerio_excluir | EXCLUIR_MINISTERIO | Excluir ministérios |
| ministerio_membros | GERENCIAR_MEMBROS | Gerenciar membros |
| ministerio_adicionar_membro | ADICIONAR_MEMBRO | Adicionar membros |
| ministerio_remover_membro | REMOVER_MEMBRO | Remover membros |
| ministerio_reunioes | GERENCIAR_REUNIOES | Gerenciar reuniões |
| ministerio_mensagens | ENVIAR_MENSAGENS | Enviar mensagens |
| ministerio_dashboard | VER_DASHBOARD | Visualizar dashboard |

### **Papéis do Sistema**

| Papel | Código | Permissões |
|-------|---------|------------|
| admin | Administrador | Todas as permissões |
| lider | Líder | Ver, editar, gerenciar membros, reuniões, mensagens |
| coordenador | Coordenador | Ver, gerenciar membros, reuniões, mensagens |
| membro | Membro | Ver, dashboard |
| convidado | Convidado | Ver, dashboard |

---

## 🎛️ Controllers

### **MinisterioController**

**Métodos Principais:**
- `index()` - Listar todos os ministérios
- `create()` - Formulário de criação
- `store()` - Salvar novo ministério
- `show($id)` - Visualizar detalhes
- `edit($id)` - Formulário de edição
- `update($id)` - Atualizar ministério
- `destroy($id)` - Excluir ministério
- `membros($id)` - Gerenciar membros
- `adicionarMembro($id)` - Adicionar membro
- `removerMembro($id, $membroId)` - Remover membro
- `dashboard()` - Dashboard com estatísticas

### **MensagemController**

**Métodos Principais:**
- `index()` - Listar mensagens
- `create()` - Formulário de criação
- `store()` - Salvar mensagem
- `show($id)` - Visualizar mensagem
- `enviar($id)` - Enviar imediatamente
- `cancelar($id)` - Cancelar agendamento
- `destroy($id)` - Excluir mensagem
- `apiDestinatarios()` - API para destinatários
- `apiPreview()` - API para preview

---

## 🎨 Views (Templates)

### **Tecnologias Utilizadas**
- **Bootstrap 5.3.8** - Framework CSS
- **AdminLTE 4.0.0-rc6** - Template administrativo
- **jQuery 3.7.1** - Interações JavaScript
- **Font Awesome 6.7.2** - Ícones
- **DataTables** - Tabelas interativas
- **Chart.js** - Gráficos e dashboards

### **Templates Principais**

#### **ministerio/index.php**
- Listagem de todos os ministérios
- DataTables com busca e ordenação
- Ações de CRUD inline
- Status badges (Ativo/Inativo)

#### **ministerio/create.php**
- Formulário de criação com validação
- Selects para líderes e coordenadores
- Campos obrigatórios marcados
- Validação Bootstrap 5

#### **ministerio/dashboard.php**
- Cards com estatísticas principais
- Gráfico Chart.js com distribuição
- Atividades recentes
- Ações rápidas

#### **mensagem/index.php**
- Listagem de mensagens enviadas
- Filtros por ministério e status
- Preview do conteúdo
- Status de envio individual

---

## 🔧 Models

### **MinisterioModel**

**Métodos Principais:**
- `list()` - Listar todos os ministérios
- `buscarPorId($id)` - Buscar por ID
- `criar($dados)` - Criar novo
- `atualizar($id, $dados)` - Atualizar
- `excluir($id)` - Soft delete
- `listarMembros($id)` - Listar membros
- `adicionarMembro($id, $membroId, $funcao)` - Adicionar membro
- `removerMembro($id, $membroId)` - Remover membro

### **MensagemModel**

**Métodos Principais:**
- `listar($filtros)` - Listar com filtros
- `buscarPorId($id)` - Buscar por ID
- `criar($dados)` - Criar mensagem
- `processarEnvio($id)` - Processar envio
- `listarDestinatarios($id)` - Listar destinatários
- `gerarPreview($conteudo, $canal)` - Gerar preview
- `cancelar($id)` - Cancelar agendamento

---

## 🔐 Segurança

### **Implementações de Segurança**

1. **RBAC Completo**
   - Verificação de permissões em todas as ações
   - Middleware de proteção de rotas
   - Controle de acesso a ministérios específicos

2. **Validação de Dados**
   - Sanitização de inputs
   - Validação server-side
   - Prevenção contra XSS e SQL Injection

3. **CSRF Protection**
   - Tokens em formulários
   - Verificação em ações críticas

4. **Logging de Auditoria**
   - Registro de todas as ações
   - IP e User Agent
   - Dados antes/depois das alterações

---

## 📊 APIs

### **Endpoints Disponíveis**

#### **GET /api/ministerio/destinatarios**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nome": "João Silva",
      "email": "joao@exemplo.com"
    }
  ]
}
```

#### **POST /api/ministerio/preview**
```json
{
  "success": true,
  "preview": "<p>Contúdo formatado...</p>"
}
```

---

## 🚀 Instalação e Configuração

### **Pré-requisitos**
- PHP 8.2+
- MySQL/MariaDB 5.7+
- ChurchCRM instalado
- Extensões PHP: PDO, JSON, mbstring

### **Passos de Instalação**

1. **Criar Tabelas**
   ```bash
   mysql -u usuario -p churchcrm < mysql/upgrade/ministerio-module.sql
   ```

2. **Configurar Permissões**
   - Ajustar sistema RBAC conforme necessidade
   - Definir papéis e permissões

3. **Integrar com Menu**
   - Adicionar itens ao menu principal
   - Configurar navegação

4. **Ajustar Rotas**
   - Configurar URLs amigáveis
   - Definir middlewares de segurança

---

## 🧪 Testes

### **Testes Unitários**
- Testes de Models
- Testes de Controllers
- Testes de RBAC
- Testes de validação

### **Testes de Integração**
- Fluxo completo de CRUD
- Envio de mensagens
- Gestão de membros
- Dashboard e relatórios

---

## 📈 Performance

### **Otimizações Implementadas**

1. **Índices Database**
   - Índices em colunas de busca
   - Índices compostos para joins
   - Índices para filtros comuns

2. **Cache de Consultas**
   - Cache de ministérios ativos
   - Cache de permissões de usuário
   - Cache de estatísticas

3. **Lazy Loading**
   - Carregamento sob demanda de dados
   - Paginação em listagens grandes
   - AJAX para atualizações

---

## 🔮 Roadmap Futuro

### **Versão 2.0 (Planejada)**
- [ ] Integração com WhatsApp Business API
- [ ] Sistema de notificações push
- [ ] Relatórios avançados
- [ ] Exportação de dados
- [ ] Integração com calendário externo
- [ ] Mobile app companion

### **Versão 3.0 (Futuro)**
- [ ] IA para sugestão de conteúdo
- [ ] Análise de engajamento
- [ ] Gamificação de participação
- [ ] Integração com redes sociais
- [ ] Videoconferências integradas

---

## 📞 Suporte e Manutenção

### **Logs Importantes**
- `/logs/ministerio.log` - Logs de operações
- `/logs/mensagens.log` - Logs de envio
- `/logs/audit.log` - Logs de auditoria

### **Monitoramento**
- Taxa de sucesso de envio
- Tempo de resposta das APIs
- Uso de recursos do sistema
- Erros mais comuns

### **Backup**
- Backup automático das tabelas
- Retenção de 90 dias
- Compressão de logs antigos
- Restauração pontual

---

## 📝 Conclusão

O **Módulo Ministério & Comunicação** representa uma solução completa e robusta para gestão da vida ministerial da igreja, integrando-se perfeitamente ao ecossistema ChurchCRM existente.

**Principais Benefícios:**
- ✅ Gestão centralizada de ministérios
- ✅ Comunicação eficiente com membros
- ✅ Controle de acesso granular (RBAC)
- ✅ Interface moderna com Bootstrap 5
- ✅ Relatórios e estatísticas detalhadas
- ✅ Auditoria completa de ações
- ✅ API para integrações futuras
- ✅ Performance otimizada
- ✅ Segurança reforçada

**Status:** ✅ **MÓDULO COMPLETO E FUNCIONAL**
