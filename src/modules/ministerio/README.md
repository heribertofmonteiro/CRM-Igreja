# Módulo Ministério & Comunicação

Módulo completo de gestão de ministérios, reuniões e comunicação para ChurchCRM.

## 📋 Funcionalidades

- ✅ CRUD completo de Ministérios
- ✅ Gerenciamento de membros por ministério
- ✅ Criação e gestão de reuniões
- ✅ Sistema de RSVP (confirmação de presença) via token
- ✅ Envio de mensagens (email, WhatsApp, SMS, interno)
- ✅ Sistema de fila para processamento de mensagens
- ✅ Lembretes automáticos de reuniões (24h antes)
- ✅ Logs e auditoria completos
- ✅ API REST integrada

## 📁 Estrutura

```
src/modules/ministerio/
├── models/
│   ├── Ministerio.php       # Model de ministérios
│   ├── Reuniao.php          # Model de reuniões
│   ├── Mensagem.php         # Model de mensagens
│   └── Log.php              # Model de logs
├── scripts/
│   ├── reuniao_reminder.php # Lembrete automático (cron: a cada hora)
│   └── mensagem_dispatcher.php # Processador de mensagens (cron: a cada 5 min)
├── v2/
│   └── routes/
│       └── ministerio.php   # Rotas API REST
├── config.php               # Configurações do módulo
└── README.md                # Este arquivo
```

## 🗄️ Banco de Dados

Execute o script SQL para criar as tabelas:
```bash
mysql -u churchcrm -p churchcrm < src/mysql/upgrade/ministerio-module.sql
```

Ou execute diretamente no MariaDB:
```sql
source src/mysql/upgrade/ministerio-module.sql;
```

### Tabelas Criadas:
- `ministerios` - Ministérios
- `ministerio_membros` - Membros dos ministérios
- `ministerio_reunioes` - Reuniões agendadas
- `ministerio_reunioes_participantes` - Participantes e RSVP
- `ministerio_mensagens` - Mensagens enviadas
- `ministerio_mensagens_envio` - Fila de envio
- `ministerio_logs` - Logs de auditoria

## 🔌 API Endpoints

### Ministérios
- `GET /v2/ministerio` - Listar ministérios
- `POST /v2/ministerio/criar` - Criar ministério
- `GET /v2/ministerio/{id}/detalhes` - Detalhes do ministério
- `POST /v2/ministerio/{id}/membros/adicionar` - Adicionar membro

### Reuniões
- `POST /v2/ministerio/reuniao/criar` - Criar reunião
- `GET /v2/ministerio/reuniao/{id}/participantes` - Listar participantes
- `GET /v2/ministerio/reuniao/rsvp/{token}` - Ver detalhes do RSVP
- `POST /v2/ministerio/reuniao/rsvp/{token}` - Confirmar presença

### Mensagens
- `POST /v2/ministerio/mensagem/enviar` - Enviar mensagem
- `GET /v2/ministerio/mensagens/{id}` - Detalhar mensagem por ID
- `GET /v2/ministerio/mensagens/historico?ministerio_id=...&reuniao_id=...` - Histórico agregando envios

## 🔄 Scripts Automáticos (Cron)

### Lembrete de Reuniões
```bash
# Executar a cada hora
0 * * * * php /caminho/para/src/modules/ministerio/scripts/reuniao_reminder.php
```

### Processador de Mensagens
```bash
# Executar a cada 5 minutos
*/5 * * * * php /caminho/para/src/modules/ministerio/scripts/mensagem_dispatcher.php
```

### Configuração no Docker

Monte o módulo como volume no container PHP (exemplo):

```yaml
services:
  php:
    volumes:
      - ./src/modules/ministerio:/var/www/html/src/modules/ministerio
```

Agende cron jobs dentro do container. Uma abordagem simples é usar `crontab`:

```bash
docker exec -it churchcrm-php bash -lc 'crontab -l | { cat; echo "0 * * * * php /var/www/html/src/modules/ministerio/scripts/reuniao_reminder.php"; echo "*/5 * * * * php /var/www/html/src/modules/ministerio/scripts/mensagem_dispatcher.php"; } | crontab -'
```

Garanta conectividade com o banco conforme `.env` do Docker. Exemplo:

- `DEV_DATABASE_PORT=3307`
- Ajuste credenciais em `src/Include/Config.php` conforme seu ambiente.

## 🎨 Interface

Acesse via menu lateral: **Ministério** → **Ministérios**

URL: `http://localhost/v2/ministerio`

## 🔒 Permissões

- **Criar ministérios**: Administradores e usuários com permissão de edição
- **Criar reuniões**: Líderes de ministério e pastores auxiliares
- **Enviar mensagens**: Líderes de ministério e pastores auxiliares

## 📝 Placeholders de Template

Nas mensagens, você pode usar:
- `{{nome}}` - Nome do destinatário
- `{{titulo_reuniao}}` - Título da reunião
- `{{data_reuniao}}` - Data da reunião
- `{{local}}` - Local da reunião
- `{{link_rsvp}}` - Link para confirmar presença

## 🚀 Instalação

1. Execute o SQL das tabelas
2. Acesse `http://localhost/v2/ministerio`
3. Configure os cron jobs (opcional, mas recomendado)

## 📚 Documentação

Para mais detalhes, consulte o arquivo `ministerio.md` na raiz do projeto.











