#!/bin/bash

# Script de Configuração do Cron para o Módulo Ministério & Comunicação
# Este script configura os cron jobs necessários para o funcionamento do módulo

echo "=========================================="
echo "CONFIGURAÇÃO DE CRON JOBS - MÓDULO MINISTÉRIO"
echo "=========================================="

# Detecta o sistema operacional
OS=$(uname -s)
echo "Sistema detectado: $OS"

# Define o caminho absoluto para os scripts de cron
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CRON_DIR="$(dirname "$SCRIPT_DIR")/cron"

# Verifica se os scripts existem
if [ ! -f "$CRON_DIR/mensagem_dispatcher.php" ]; then
    echo "❌ ERRO: Script mensagem_dispatcher.php não encontrado em $CRON_DIR"
    exit 1
fi

if [ ! -f "$CRON_DIR/reuniao_reminder.php" ]; then
    echo "❌ ERRO: Script reuniao_reminder.php não encontrado em $CRON_DIR"
    exit 1
fi

# Detecta o caminho do PHP
PHP_PATH=$(which php)
if [ -z "$PHP_PATH" ]; then
    echo "❌ ERRO: PHP não encontrado no sistema"
    exit 1
fi

echo "PHP encontrado em: $PHP_PATH"

# Cria as entradas do cron
MENSAGEM_JOB="*/5 * * * * $PHP_PATH $CRON_DIR/mensagem_dispatcher.php >> $CRON_DIR/mensagem_dispatcher.log 2>&1"
REUNIAO_JOB="0 * * * * $PHP_PATH $CRON_DIR/reuniao_reminder.php >> $CRON_DIR/reuniao_reminder.log 2>&1"

echo ""
echo "As seguintes entradas serão adicionadas ao crontab:"
echo "1. $MENSAGEM_JOB"
echo "2. $REUNIAO_JOB"
echo ""

# Pergunta se deseja continuar
read -p "Deseja adicionar estas entradas ao crontab? (s/n): " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Ss]$ ]]; then
    # Cria arquivo temporário com as novas entradas
    TEMP_CRON=$(mktemp)
    
    # Copia crontab existente
    crontab -l > "$TEMP_CRON" 2>/dev/null || true
    
    # Adiciona comentário e as novas entradas
    echo "" >> "$TEMP_CRON"
    echo "# ChurchCRM - Módulo Ministério & Comunicação" >> "$TEMP_CRON"
    echo "$MENSAGEM_JOB" >> "$TEMP_CRON"
    echo "$REUNIAO_JOB" >> "$TEMP_CRON"
    
    # Instala o novo crontab
    crontab "$TEMP_CRON"
    
    # Remove arquivo temporário
    rm "$TEMP_CRON"
    
    echo "✅ Cron jobs instalados com sucesso!"
    echo ""
    echo "Verificando instalação:"
    crontab -l | grep -E "(mensagem_dispatcher|reuniao_reminder)"
    
    echo ""
    echo "📋 Logs serão salvos em:"
    echo "   - $CRON_DIR/mensagem_dispatcher.log"
    echo "   - $CRON_DIR/reuniao_reminder.log"
    
else
    echo "❌ Instalação cancelada."
    echo ""
    echo "Para instalar manualmente, adicione estas linhas ao seu crontab:"
    echo "$MENSAGEM_JOB"
    echo "$REUNIAO_JOB"
fi

echo ""
echo "=========================================="
echo "INSTALAÇÃO CONCLUÍDA"
echo "=========================================="