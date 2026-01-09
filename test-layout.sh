#!/bin/bash

# Script para testar o layout do ChurchCRM
echo "🔧 Testando layout do ChurchCRM..."

# Verificar se o servidor está rodando
if ! curl -s http://localhost:8080 > /dev/null; then
    echo "❌ Servidor não está rodando. Iniciando..."
    ./start-server.sh &
    sleep 5
fi

# Testar login
echo "🔐 Fazendo login..."
curl -c cookies.txt -X POST -d "User=admin&Password=0631" http://localhost:8080/session/begin -L > /dev/null

# Testar página principal
echo "📄 Testando página principal..."
RESPONSE=$(curl -b cookies.txt -s http://localhost:8080/v2/dashboard)

# Verificar elementos HTML
echo "🔍 Verificando estrutura HTML..."

if echo "$RESPONSE" | grep -q "main-header"; then
    echo "✅ Header encontrado"
else
    echo "❌ Header não encontrado"
fi

if echo "$RESPONSE" | grep -q "main-sidebar"; then
    echo "✅ Sidebar encontrada"
else
    echo "❌ Sidebar não encontrada"
fi

if echo "$RESPONSE" | grep -q "content-wrapper"; then
    echo "✅ Content wrapper encontrado"
else
    echo "❌ Content wrapper não encontrado"
fi

if echo "$RESPONSE" | grep -q "main-footer"; then
    echo "✅ Footer encontrado"
else
    echo "❌ Footer não encontrado"
fi

# Verificar CSS
echo "🎨 Verificando CSS..."
if curl -s -I http://localhost:8080/skin/v2/churchcrm.min.css | grep -q "200 OK"; then
    echo "✅ CSS carregando corretamente"
else
    echo "❌ CSS não está carregando"
fi

# Limpar cookies
rm -f cookies.txt

echo "✅ Teste concluído!"
echo "🌐 Acesse: http://localhost:8080"
echo "👤 Login: admin/0631"
