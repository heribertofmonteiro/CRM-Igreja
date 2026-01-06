GERAR MÓDULO MINISTÉRIO & COMUNICAÇÃO COMPLETO – CRM ECLESIÁSTICO

Objetivo: Criar automaticamente todo o módulo "ministerio" para o CRM eclesiástico, totalmente funcional, integrado ao projeto existente, compatível com PHP puro, MariaDB, Docker Compose, mantendo o mesmo tema visual do sistema principal. O módulo deve incluir TUDO da documentação técnica.

1️⃣ BANCO DE DADOS (MariaDB)
- Criar tabelas: ministerios, ministerio_membros, ministerio_reunioes, ministerio_reunioes_participantes, ministerio_mensagens, ministerio_logs
- Incluir todas FK, índices, constraints, tipos corretos, InnoDB e charset utf8mb4
- Compatível com Docker Compose do projeto

2️⃣ ESTRUTURA DE DIRETÓRIOS
/app/modules/ministerio/
 ├── controllers/
 │     ├── MinisterioController.php
 │     ├── ReuniaoController.php
 │     └── MensagemController.php
 ├── models/
 │     ├── Ministerio.php
 │     ├── MembroMinisterio.php
 │     ├── Reuniao.php
 │     └── Mensagem.php
 ├── views/
 │     ├── list.php
 │     ├── form.php
 │     ├── reunioes.php
 │     └── mensagens.php
 ├── scripts/
 │     ├── reuniao_reminder.php
 │     └── mensagem_dispatcher.php
 ├── routes.php
 └── config.php

3️⃣ CONTROLLERS
- MinisterioController: CRUD de ministérios, listagem de membros
- ReuniaoController: CRUD de reuniões, gerenciamento de participantes, RSVP via token
- MensagemController: criação, envio, agendamento, histórico de mensagens

4️⃣ MODELS
- Classes PHP correspondentes às tabelas com métodos CRUD, filtros, joins e relacionamentos
- Validação de dados e conversão de datas

5️⃣ SCRIPTS AUTOMÁTICOS
- reuniao_reminder.php: envia lembretes de reuniões futuras (24h antes)
- mensagem_dispatcher.php: processa mensagens agendadas
- Scripts usam fila (queue) para processamento assíncrono

6️⃣ FILA E MENSAGERIA
- QueueManager envia mensagens via SMTP, WhatsApp (Twilio/Zenvia) ou interno
- Retry automático para falhas
- Logs detalhados em ministerio_logs

7️⃣ TEMPLATE ENGINE
- Substituição de placeholders em mensagens: {{nome}}, {{titulo_reuniao}}, {{data_reuniao}}, {{link_rsvp}}
- Templates dinâmicos integrados ao tema do projeto

8️⃣ API INTERNA
- /ministerio/listar → lista ministérios
- /ministerio/criar → cria ministério
- /ministerio/{id}/detalhes → detalhes do ministério
- /ministerio/{id}/membros/adicionar → adiciona membro
- /ministerio/reuniao/criar → cria reunião
- /ministerio/reuniao/{id}/participantes → lista participantes
- /ministerio/mensagem/enviar → envia mensagem
- /ministerio/mensagens/{id} → histórico mensagens
- /ministerio/reuniao/rsvp/{token} → confirma presença via token

9️⃣ SEGURANÇA E PERMISSÕES
- Apenas líderes e pastores auxiliares podem criar reuniões e mensagens
- Respeitar RBAC do projeto
- Rate limit: 50 mensagens/minuto
- Campos sensíveis criptografados (tokens RSVP, telefones)
- Logs de auditoria completos

🔟 FRONT-END
- Views integradas ao tema do projeto (header.php, footer.php, CSS/JS)
- Suporte AJAX para API interna
- Interface para líderes e pastores auxiliares: lista de reuniões, membros, mensagens

1️⃣1️⃣ CRON JOBS
- reuniao_reminder.php: a cada hora
- mensagem_dispatcher.php: a cada 5 minutos

1️⃣2️⃣ LOGS E AUDITORIA
- Tabela ministerio_logs: usuario_id, acao, dados_antigos, dados_novos, ip_origem
- Logs de fila, envio de mensagens e erros em arquivos separados

1️⃣3️⃣ TESTES UNITÁRIOS
- PHPUnit: /tests/MinisterioTest.php, /tests/ReuniaoTest.php, /tests/MensagemTest.php
- Testes: criação de ministério, envio de mensagem, RSVP, restrição de acesso

1️⃣4️⃣ DOCKER
- Módulo como volume no container PHP
- Scripts cron dentro do container
- Compatível com MariaDB do projeto
- Exemplo volume: ./modules/ministerio:/var/www/html/modules/ministerio

1️⃣5️⃣ CRITÉRIOS DE ACEITAÇÃO
- CRUD completo (ministerios, membros, reuniões, mensagens)
- Envio de mensagens via fila e cron
- RSVP funcional
- Interface compatível com tema
- Permissões funcionando
- Logs e auditoria funcionando
- Testes unitários passando

⚡ AÇÃO FINAL PARA WINDSURF:
- Gerar automaticamente **todo o módulo completo**
- SQL, PHP (controllers, models, services), views, templates, scripts, cron, fila, logs, permissões e testes
- Tudo pronto para rodar imediatamente no CRM eclesiástico dentro do Docker Compose com MariaDB
- Respeitar completamente a documentação técnica fornecida
