#!/bin/bash

# Script para testar o dashboard e identificar erros

echo "🔍 Testando Dashboard do ChurchCRM..."

# Verificar se o servidor está rodando
echo "📡 Verificando servidor..."
if ! curl -s http://localhost:8080 > /dev/null; then
    echo "❌ Servidor não está rodando na porta 8080"
    exit 1
fi

echo "✅ Servidor está rodando"

# Testar página de login
echo "🔐 Testando página de login..."
LOGIN_RESPONSE=$(curl -s -c /tmp/cookies.txt http://localhost:8080/session/begin)
if [[ $LOGIN_RESPONSE == *"Invalid login or password"* ]]; then
    echo "⚠️  Página de login carregada, mas com erro de autenticação"
fi

# Tentar login
echo "🔑 Tentando fazer login..."
LOGIN_RESULT=$(curl -s -X POST \
    -d "User=admin&Password=0631" \
    -c /tmp/cookies.txt \
    -b /tmp/cookies.txt \
    -L \
    http://localhost:8080/session/begin)

# Verificar se login foi bem-sucedido
if [[ $LOGIN_RESULT == *"Invalid login or password"* ]]; then
    echo "❌ Falha no login: usuário ou senha incorretos"
    echo "🔍 Verificando configuração do banco de dados..."
    
    # Verificar se o arquivo de configuração existe
    if [ ! -f "src/Include/Config.php" ]; then
        echo "❌ Arquivo de configuração não encontrado"
        echo "🔧 Execute a configuração inicial em: http://localhost:8080/setup"
        exit 1
    fi
    
    echo "✅ Arquivo de configuração encontrado"
    
    # Verificar se há erros de sintaxe no PHP
    echo "🔍 Verificando erros de sintaxe PHP..."
    php -l src/Include/Config.php
    php -l src/ChurchCRM/view/MenuRendererImproved.php
    
    # Verificar logs de erro
    echo "🔍 Procurando logs de erro..."
    find . -name "*.log" -type f -exec echo "📄 {}" \; -exec tail -5 {} \; 2>/dev/null
    
else
    echo "✅ Login bem-sucedido"
    
    # Testar dashboard
    echo "📊 Testando dashboard..."
    DASHBOARD_RESPONSE=$(curl -s -b /tmp/cookies.txt http://localhost:8080/v2/dashboard)
    
    if [[ $DASHBOARD_RESPONSE == *"500"* ]]; then
        echo "❌ Erro 500 no dashboard"
        
        # Verificar logs de erro do PHP
        echo "🔍 Verificando logs de erro PHP..."
        php -l src/v2/routes/root.php
        php -l src/v2/templates/root/dashboard.php
        
        # Verificar dependências
        echo "🔍 Verificando dependências..."
        php -m | grep -E "(pdo|mysql|mysqli)"
        
    elif [[ $DASHBOARD_RESPONSE == *"Families"* ]]; then
        echo "✅ Dashboard carregado com sucesso"
        echo "📊 Estatísticas encontradas no dashboard"
        
        # Contar elementos do dashboard
        FAMILIES=$(echo "$DASHBOARD_RESPONSE" | grep -o "Families" | wc -l)
        PEOPLE=$(echo "$DASHBOARD_RESPONSE" | grep -o "People" | wc -l)
        echo "📈 Famílias: $FAMILIES, Pessoas: $PEOPLE"
        
    else
        echo "⚠️  Resposta inesperada do dashboard"
        echo "📄 Primeiras 500 caracteres:"
        echo "$DASHBOARD_RESPONSE" | head -c 500
    fi
fi

# Verificar CSP
echo "🔒 Verificando CSP..."
CSP_HEADER=$(curl -s -I http://localhost:8080 | grep -i "content-security-policy")
if [[ $CSP_HEADER == *"translate.googleapis.com"* ]]; then
    echo "✅ CSP configurado corretamente para Google Translate"
else
    echo "⚠️  CSP pode precisar de ajuste para Google Translate"
fi

# Limpar cookies
rm -f /tmp/cookies.txt

echo "🎯 Teste concluído"
