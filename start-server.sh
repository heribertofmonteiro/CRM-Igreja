#!/bin/bash

# Script simplificado para iniciar o ChurchCRM local
# Este script inicia o servidor PHP na porta 8080

echo "🚀 Iniciando ChurchCRM Local..."

# Verificar se o banco de dados está acessível
if ! mysql -u churchcrm -pchurchcrm123 churchcrm -e "SELECT 1;" >/dev/null 2>&1; then
    echo "❌ Banco de dados não acessível. Execute o setup primeiro:"
    echo "   sudo mysql -e \"CREATE DATABASE IF NOT EXISTS churchcrm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\""
    echo "   sudo mysql -e \"CREATE USER IF NOT EXISTS 'churchcrm'@'localhost' IDENTIFIED BY 'churchcrm123';\""
    echo "   sudo mysql -e \"GRANT ALL PRIVILEGES ON churchcrm.* TO 'churchcrm'@'localhost'; FLUSH PRIVILEGES;\""
    exit 1
fi

# Verificar se a porta 8080 está em uso
if lsof -i :8080 >/dev/null 2>&1; then
    echo "⚠️  Porta 8080 já está em uso"
    echo "   Para parar: pkill -f 'php -S localhost:8080'"
    exit 1
fi

# Iniciar servidor PHP
echo "🌐 Iniciando servidor web em http://localhost:8080"
echo "📱 Acesse com: admin/changeme"
echo "⛔ Pressione Ctrl+C para parar"
echo ""

cd src && php -d display_errors=1 -d error_reporting=E_ALL -S localhost:8080 -t .
