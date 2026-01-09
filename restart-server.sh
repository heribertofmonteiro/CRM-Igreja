#!/bin/bash

# Script para reiniciar o servidor PHP com as correções aplicadas

echo "🔄 Reiniciando servidor PHP com correções..."

# Parar servidor anterior
echo "⏹️  Parando servidor anterior..."
pkill -f "php -S localhost:8080" 2>/dev/null || true

# Aguardar um momento
sleep 2

# Iniciar servidor novamente
echo "🚀 Iniciando servidor PHP..."
php -S localhost:8080 -t src > /dev/null 2>&1 &
SERVER_PID=$!

# Aguardar servidor iniciar
sleep 3

# Verificar se o servidor está rodando
if curl -s http://localhost:8080 > /dev/null; then
    echo "✅ Servidor reiniciado com sucesso!"
    echo "🌐 URL: http://localhost:8080"
    echo "🔐 Login: admin/0631"
    echo ""
    echo "🎨 Correções aplicadas:"
    echo "  ✅ CSP atualizado para Google Translate"
    echo "  ✅ Font data: permitido"
    echo "  ✅ MenuRendererImproved integrado"
    echo "  ✅ Tema moderno aplicado"
    echo ""
    echo "🎯 Teste o dashboard:"
    echo "  1. Acesse: http://localhost:8080"
    echo "  2. Faça login com admin/0631"
    echo "  3. Verifique o dashboard e o menu"
    echo ""
    echo "📊 Para testes completos:"
    echo "  ./test-dashboard.sh"
else
    echo "❌ Falha ao iniciar servidor"
    echo "🔍 Verificando erros..."
    php -S localhost:8080 -t src
fi
