# Módulo Ministério & Comunicação - Instruções de Configuração

Este documento fornece instruções detalhadas para configurar o módulo Ministério & Comunicação no ChurchCRM.

## 📋 Pré-requisitos

- ChurchCRM instalado e configurado
- PHP 8.0 ou superior
- MySQL 5.7+ ou MariaDB 10.2+
- Acesso ao servidor (SSH ou painel de controle)
- Permissões para configurar cron jobs

## 🔧 Instalação do Módulo

### 1. Estrutura de Diretórios

O módulo já está estruturado com os seguintes diretórios principais:

```
src/
├── api/                    # Endpoints REST
├── model/                  # Models PHP (Ministerio, Reuniao, Mensagem)
├── scripts/                # Scripts de cron
├── templates/              # Templates de email
├── tests/                  # Testes unitários
└── views/                  # Interfaces frontend
```

### 2. Configuração do Banco de Dados

Execute o script SQL para criar as tabelas necessárias:

```bash
mysql -u seu_usuario -p seu_banco_churchcrm < src/scripts/install/ministerio_schema.sql
```

### 3. Configuração de Permissões

Certifique-se de que os seguintes diretórios tenham permissões de escrita:

```bash
chmod 755 src/logs/
chmod 755 src/scripts/
```

## ⚙️ Configuração dos Cron Jobs

### 📅 Cron Jobs Necessários

O módulo requer 2 cron jobs para funcionamento automático:

#### 1. Dispatcher de Mensagens (a cada 5 minutos)
```bash
*/5 * * * * cd /caminho/para/churchcrm && /usr/bin/php src/scripts/mensagem_dispatcher.php >> src/logs/app.log 2>&1
```

#### 2. Lembretes de Reuniões (a cada hora)
```bash
0 * * * * cd /caminho/para/churchcrm && /usr/bin/php src/scripts/reuniao_reminder.php >> src/logs/app.log 2>&1
```

### 🔧 Script de Instalação Automática

Use o script de instalação automática para configurar os cron jobs:

```bash
# Torne o script executável
chmod +x src/scripts/install/cron_setup.sh

# Execute o script
./src/scripts/install/cron_setup.sh
```

O script irá:
- Detectar automaticamente o sistema operacional
- Encontrar o caminho correto do PHP
- Verificar se os scripts existem
- Adicionar as entradas ao crontab
- Fornecer opções de verificação

### 📝 Configuração Manual (Alternativa)

Se preferir configurar manualmente:

1. **Edite o crontab:**
   ```bash
   crontab -e
   ```

2. **Adicione as linhas:**
   ```bash
   # ChurchCRM - Módulo Ministério & Comunicação
   # Dispatcher de mensagens (a cada 5 minutos)
   */5 * * * * cd /var/www/html/churchcrm && /usr/bin/php src/scripts/mensagem_dispatcher.php >> src/logs/app.log 2>&1
   
   # Lembretes de reuniões (a cada hora)
   0 * * * * cd /var/www/html/churchcrm && /usr/bin/php src/scripts/reuniao_reminder.php >> src/logs/app.log 2>&1
   ```

3. **Substitua os caminhos:**
   - `/var/www/html/churchcrm` pelo caminho real da instalação
   - `/usr/bin/php` pelo caminho correto do PHP (verifique com `which php`)

## 📧 Configuração de Email

### SMTP Configuration

Configure as configurações de SMTP no arquivo de configuração do ChurchCRM:

```php
// Configurações de email (exemplo)
$mail_config = [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_user' => 'seu-email@dominio.com',
    'smtp_pass' => 'sua-senha-app',
    'smtp_secure' => 'tls',
    'from_email' => 'nao-responda@seu-dominio.com',
    'from_name' => 'ChurchCRM - Ministérios'
];
```

### Templates de Email

Os templates de email estão localizados em `src/templates/`:

- `email_lembrete_reuniao.html` - Lembrete de reunião
- `email_nova_mensagem.html` - Nova mensagem
- `email_convite_ministerio.html` - Convite para ministério

## 🧪 Testes

### Executar Testes Unitários

```bash
# Instalar dependências de teste (se necessário)
composer install --dev

# Executar todos os testes do módulo
./vendor/bin/phpunit src/tests/MinisterioTest.php
./vendor/bin/phpunit src/tests/ReuniaoTest.php
./vendor/bin/phpunit src/tests/MensagemTest.php
```

### Verificar Logs

Monitore os logs para garantir que tudo está funcionando:

```bash
# Ver logs de aplicação
tail -f src/logs/app.log

# Ver logs de autenticação
tail -f src/logs/auth.log

# Ver logs de CSP
tail -f src/logs/csp.log
```

## 🔍 Troubleshooting

### Problemas Comuns

#### 1. Cron Jobs Não Executando

**Verifique:**
- Se o cron está ativado: `service cron status`
- Se os caminhos estão corretos
- Se os scripts têm permissão de execução
- Logs de erro: `grep CRON /var/log/syslog`

#### 2. Emails Não Enviando

**Verifique:**
- Configurações SMTP no arquivo de config
- Logs de email em `src/logs/app.log`
- Se o PHPMailer está instalado: `composer show phpmailer/phpmailer`

#### 3. Erros de Permissão

**Corrija:**
```bash
chmod 755 src/scripts/*.php
chmod 644 src/logs/*.log
```

#### 4. Banco de Dados Não Encontrado

**Verifique:**
- Se as tabelas foram criadas corretamente
- Se o config do ChurchCRM aponta para o banco correto
- Se o usuário do banco tem permissões adequadas

### Comandos Úteis

```bash
# Testar script manualmente
php src/scripts/mensagem_dispatcher.php

# Testar script de lembrete
php src/scripts/reuniao_reminder.php

# Verificar cron jobs atuais
crontab -l

# Verificar versão do PHP
php --version

# Verificar módulos PHP necessários
php -m | grep -E "(pdo|mysql|json)"
```

## 📊 Monitoramento

### Dashboard de Status

Acesse o dashboard do módulo em:
```
https://seu-dominio.com/churchcrm/ministerio/dashboard
```

### Métricas Disponíveis

- Total de ministérios ativos
- Reuniões agendadas
- Mensagens pendentes/enviadas
- Membros por ministério
- Status dos cron jobs

## 🔄 Manutenção

### Backup

Inclua os seguintes itens no backup:
- Tabelas do módulo (`ministerio_*`)
- Arquivos de log (`src/logs/`)
- Templates customizados (`src/templates/`)

### Atualizações

Para atualizar o módulo:
1. Faça backup do banco de dados
2. Execute novos scripts SQL (se houver)
3. Atualize os arquivos do módulo
4. Teste os cron jobs
5. Verifique os logs

## 📞 Suporte

Se encontrar problemas:

1. **Verifique os logs** primeiro
2. **Teste manualmente** os scripts
3. **Confirme as configurações** de cron e email
4. **Consulte a documentação** do ChurchCRM
5. **Crie um issue** no repositório do projeto

---

**Nota:** Este módulo está em produção e funcionando. Mantenha os logs monitorados e execute os testes regularmente para garantir o funcionamento adequado.