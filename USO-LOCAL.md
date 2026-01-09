# 🚀 ChurchCRM - Guia Rápido de Uso Local

## ✅ Status: CONFIGURADO E FUNCIONANDO

O ChurchCRM está configurado para uso local sem Docker e funcionando corretamente!

## 🌐 Acesso Imediato

### 1. Iniciar o Servidor
```bash
./start-server.sh
```

### 2. Acessar a Aplicação
Abra no navegador: **http://localhost:8080**

### 3. Login Padrão
- **Usuário:** `admin`
- **Senha:** `changeme`

## 🔧 Comandos Essenciais

### Iniciar Servidor
```bash
./start-server.sh
# Ou manualmente:
cd src && php -S localhost:8080
```

### Parar Servidor
```bash
pkill -f "php -S localhost:8080"
```

### Verificar Status
```bash
lsof -i :8080
```

## 📋 Resumo da Configuração

### ✅ Configurado:
- **Banco de Dados:** MySQL/MariaDB local
- **Usuário DB:** `churchcrm` / `churchcrm123`
- **Servidor Web:** PHP built-in (porta 8080)
- **Assets:** Compilados e funcionando
- **Dependências:** PHP e Node.js instaladas

### 📁 Arquivos Importantes:
- `src/Include/Config.php` - Configuração principal
- `.env.local` - Variáveis de ambiente
- `start-server.sh` - Script para iniciar
- `database-setup.sql` - Script SQL

## 🗄️ Banco de Dados

O banco de dados está configurado com:
- **Database:** `churchcrm`
- **User:** `churchcrm`
- **Password:** `churchcrm123`
- **Host:** `localhost`
- **Port:** `3306`

### Para recriar o banco (se necessário):
```bash
sudo mysql -e "DROP DATABASE IF EXISTS churchcrm;"
sudo mysql -e "CREATE DATABASE churchcrm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'churchcrm'@'localhost' IDENTIFIED BY 'churchcrm123';"
sudo mysql -e "GRANT ALL PRIVILEGES ON churchcrm.* TO 'churchcrm'@'localhost'; FLUSH PRIVILEGES;"
```

## 🐛 Solução de Problemas

### Servidor não inicia:
```bash
# Verificar se a porta está ocupada
lsof -i :8080

# Matar processo antigo
pkill -f "php -S localhost:8080"

# Verificar permissões
ls -la src/
```

### Erro de banco de dados:
```bash
# Testar conexão
mysql -u churchcrm -pchurchcrm123 churchcrm -e "SELECT 1;"

# Recriar usuário
sudo mysql -e "GRANT ALL PRIVILEGES ON churchcrm.* TO 'churchcrm'@'localhost'; FLUSH PRIVILEGES;"
```

### Página não carrega:
```bash
# Verificar servidor
curl -I http://localhost:8080

# Verificar logs
cd src && php -d display_errors=1 -d error_reporting=E_ALL -S localhost:8080
```

## 🔄 Uso Diário

### Para começar a trabalhar:
```bash
# 1. Iniciar o servidor
./start-server.sh

# 2. Abrir navegador
# http://localhost:8080

# 3. Login
# admin/changeme
```

### Para parar:
```bash
# Ctrl+C no terminal ou
pkill -f "php -S localhost:8080"
```

## 📝 Próximos Passos

1. **Alterar senha padrão** após primeiro login
2. **Configurar módulos** necessários
3. **Importar dados** se tiver backup
4. **Configurar e-mail** para notificações
5. **Personalizar tema** se desejar

## 🎯 Dicas

- **Desenvolvimento:** Use `./start-server.sh` para facilitar
- **Produção:** Configure Apache/Nginx (veja arquivos .conf)
- **Backup:** Faça backup regular do banco de dados
- **Atualizações:** Mantenha dependências atualizadas

---

**🎉 O ChurchCRM está pronto para uso!**
