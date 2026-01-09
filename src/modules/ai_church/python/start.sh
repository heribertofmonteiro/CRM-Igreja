#!/bin/bash

# 🚀 AI Church API - Script de Inicialização

echo "🤖 Iniciando AI Church API..."
echo "================================"

# Verificar Python
if ! command -v python3 &> /dev/null; then
    echo "❌ Python 3 não encontrado. Instale Python 3.11+"
    exit 1
fi

# Verificar se estamos no diretório correto
if [ ! -f "app.py" ]; then
    echo "❌ app.py não encontrado. Execute este script do diretório python/"
    exit 1
fi

# Criar ambiente virtual se não existir
if [ ! -d "venv" ]; then
    echo "📦 Criando ambiente virtual..."
    python3 -m venv venv
fi

# Ativar ambiente virtual
echo "🔧 Ativando ambiente virtual..."
source venv/bin/activate

# Instalar dependências
echo "📚 Instalando dependências..."
pip install -r requirements.txt

# Criar diretórios necessários
echo "📁 Criando diretórios..."
mkdir -p models data cache logs

# Iniciar API
echo "🚀 Iniciando API na porta 5000..."
echo "📍 Acesse: http://localhost:5000"
echo "📍 Health check: http://localhost:5000/health"
echo "📍 Previsão: http://localhost:5000/predict/attendance"
echo ""
echo "Pressione Ctrl+C para parar"
echo "================================"

# Iniciar com gunicorn para produção
if command -v gunicorn &> /dev/null; then
    gunicorn --bind 0.0.0.0:5000 --workers 4 --timeout 120 app:app
else
    python3 app.py
fi
