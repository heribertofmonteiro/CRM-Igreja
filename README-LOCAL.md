# ChurchCRM - Configuração Local (Sem Docker)

Este guia mostra como configurar e executar o ChurchCRM em ambiente local sem usar Docker.

## 📋 Pré-requisitos

### Software Necessário
- **PHP 8.2+** - Linguagem principal
- **Composer 2.0+** - Gerenciador de pacotes PHP
- **Node.js 18+** - Para build do frontend
- **npm 9+** - Gerenciador de pacotes Node.js
- **MySQL 8.0+** ou **MariaDB 10.5+** - Banco de dados
- **Git** - Controle de versão

### Extensões PHP Obrigatórias
```bash
# Extensões necessárias
php-pdo
php-mysql
php-bcmath
php-curl
php-exif
php-fileinfo
php-filter
php-gd
php-gettext
php-iconv
php-mbstring
php-session
php-sodium
php-zip
php-zlib
```

## 🚀 Configuração Rápida

### 1. Clonar o Projeto
```bash
git clone <URL-DO-REPOSITORIO> CRM
cd CRM
```

### 2. Executar Script de Configuração
```bash
./setup-local.sh
```

Este script irá:
- ✅ Verificar dependências
- ✅ Criar diretórios necessários
- ✅ Instalar dependências PHP e Node.js
- ✅ Compilar assets
- ✅ Configurar permissões

### 3. Configurar Banco de Dados
```sql
-- Conecte-se ao MySQL/MariaDB como root
mysql -u root -p

-- Execute os comandos abaixo
CREATE DATABASE churchcrm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'churchcrm'@'localhost' IDENTIFIED BY 'churchcrm123';
GRANT ALL PRIVILEGES ON churchcrm.* TO 'churchcrm'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 4. Iniciar o Servidor
```bash
./start-local.sh
```

### 5. Acessar a Aplicação
Abra seu navegador e acesse: **http://localhost:8080**

## ⚙️ Configuração Manual

Se preferir configurar manualmente:

### 1. Variáveis de Ambiente
Copie e edite o arquivo `.env.local`:
```bash
cp .env.local.example .env.local
```

Configure as seguintes variáveis:
```env
# Banco de Dados
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=churchcrm
DB_USER=churchcrm
DB_PASSWORD=churchcrm123

# Aplicação
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8080
```

### 2. Instalar Dependências
```bash
# Dependências PHP
cd src
composer install
cd ..

# Dependências Node.js
npm install

# Compilar assets
npm run build:frontend
```

### 3. Criar Diretórios
```bash
mkdir -p src/logs src/sessions src/cache src/Uploads
chmod -R 777 src/logs src/sessions src/cache src/Uploads
```

### 4. Configuração do Sistema
Copie o arquivo de configuração:
```bash
cp docker/Config.php src/Include/Config.php
```

## 🗂️ Estrutura de Diretórios

```
CRM/
├── .env.local              # Configurações de ambiente
├── config-local.php        # Configurações PHP
├── setup-local.sh          # Script de configuração
├── start-local.sh          # Script para iniciar
├── stop-local.sh           # Script para parar
├── src/                    # Código fonte
│   ├── Include/
│   │   └── Config.php      # Configuração principal
│   ├── vendor/             # Dependências PHP
│   ├── logs/               # Logs da aplicação
│   ├── sessions/           # Arquivos de sessão
│   ├── cache/              # Cache
│   └── Uploads/            # Uploads de arquivos
├── node_modules/           # Dependências Node.js
└── README-LOCAL.md         # Este arquivo
```

## 🛠️ Comandos Úteis

### Gerenciamento do Servidor
```bash
# Iniciar o servidor
./start-local.sh

# Parar o servidor
./stop-local.sh

# Verificar logs
tail -f src/logs/php-error.log

# Verificar processos na porta 8080
lsof -i :8080
```

### Desenvolvimento
```bash
# Instalar dependências PHP
cd src && composer install && cd ..

# Instalar dependências Node.js
npm install

# Compilar assets
npm run build:frontend

# Compilar assets em modo desenvolvimento
npm run build:dev

# Executar testes
npm test

# Verificar qualidade do código
npm run qa
```

### Banco de Dados
```bash
# Conectar ao banco
mysql -u churchcrm -pchurchcrm123 churchcrm

# Fazer backup
mysqldump -u churchcrm -pchurchcrm123 churchcrm > backup.sql

# Restaurar backup
mysql -u churchcrm -pchurchcrm123 churchcrm < backup.sql
```

## 🔧 Configuração Avançada

### Servidor Web Completo (Apache)

Para produção, configure um servidor web completo:

**Apache Virtual Host:**
```apache
<VirtualHost *:80>
    ServerName churchcrm.local
    DocumentRoot /home/heriberto/projetos/PHP/Laravel/CRM/src
    
    <Directory /home/heriberto/projetos/PHP/Laravel/CRM/src>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/churchcrm_error.log
    CustomLog ${APACHE_LOG_DIR}/churchcrm_access.log combined
</VirtualHost>
```

### Nginx

**Nginx Server Block:**
```nginx
server {
    listen 80;
    server_name churchcrm.local;
    root /home/heriberto/projetos/PHP/Laravel/CRM/src;
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 🐛 Solução de Problemas

### Banco de Dados
**Erro: "Connection refused"**
- Verifique se o MySQL/MariaDB está rodando: `sudo systemctl status mysql`
- Verifique se o banco `churchcrm` existe: `mysql -u root -p -e "SHOW DATABASES;"`
- Verifique se o usuário tem permissões: `mysql -u churchcrm -pchurchcrm123 -e "SHOW DATABASES;"`

### Permissões
**Erro: "Permission denied"**
```bash
# Corrigir permissões dos diretórios
chmod -R 755 src/
chmod -R 777 src/logs src/sessions src/cache src/Uploads
```

### Porta Ocupada
**Erro: "Port 8080 already in use"**
```bash
# Verificar o processo usando a porta
lsof -i :8080

# Matar o processo
kill -9 <PID>

# Ou usar outra porta
php -S localhost:8081 -t src/
```

### Dependências
**Erro: "Composer install failed"**
```bash
# Limpar cache do Composer
composer clear-cache

# Reinstalar
rm -rf src/vendor/
cd src && composer install && cd ..
```

**Erro: "npm install failed"**
```bash
# Limpar cache do npm
npm cache clean --force

# Reinstalar
rm -rf node_modules/
npm install
```

## 📝 Notas Importantes

### Segurança
- 🔐 Altere as senhas padrão em produção
- 🔐 Mantenha o arquivo `.env.local` seguro
- 🔐 Configure HTTPS em produção
- 🔐 Restrinja o acesso aos diretórios sensíveis

### Performance
- ⚡ Use OPcache para melhor performance PHP
- ⚡ Configure um servidor web completo (Apache/Nginx)
- ⚡ Use Redis ou Memcached para cache em produção
- ⚡ Otimize o MySQL/MariaDB

### Backup
- 💾 Faça backup regular do banco de dados
- 💾 Backup dos arquivos de upload
- 💾 Backup do arquivo de configuração

## 🆘 Suporte

Se encontrar problemas:

1. Verifique os logs em `src/logs/`
2. Verifique os erros do PHP: `tail -f src/logs/php-error.log`
3. Verifique a documentação oficial: https://churchcrm.io
4. Abra uma issue no repositório GitHub

## 🔄 Atualizações

Para atualizar o sistema:

```bash
# Parar o servidor
./stop-local.sh

# Atualizar código
git pull origin main

# Atualizar dependências
cd src && composer update && cd ..
npm update

# Compilar assets
npm run build:frontend

# Iniciar o servidor
./start-local.sh
```
