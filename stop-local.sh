#!/bin/bash

# Script para parar o ChurchCRM em ambiente local (sem Docker)
# Este script para o servidor web e serviços

echo "🛑 Parando ChurchCRM em ambiente local..."

# Cores para saída
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Verificar se existe arquivo com PID do servidor
if [ -f ".server.pid" ]; then
    SERVER_PID=$(cat .server.pid)
    echo -e "${BLUE}📋 Verificando servidor com PID: $SERVER_PID${NC}"
    
    # Verificar se o processo ainda existe
    if kill -0 $SERVER_PID 2>/dev/null; then
        echo -e "${YELLOW}⚠️  Encerrando processo $SERVER_PID...${NC}"
        kill $SERVER_PID
        
        # Aguardar um momento
        sleep 2
        
        # Verificar se o processo foi encerrado
        if kill -0 $SERVER_PID 2>/dev/null; then
            echo -e "${YELLOW}⚠️  Processo não respondeu. Forçando encerramento...${NC}"
            kill -9 $SERVER_PID
            sleep 1
        fi
        
        echo -e "${GREEN}✅ Servidor web encerrado${NC}"
    else
        echo -e "${YELLOW}⚠️  Servidor com PID $SERVER_PID não está rodando${NC}"
    fi
    
    # Remover arquivo PID
    rm -f .server.pid
else
    echo -e "${YELLOW}⚠️  Arquivo .server.pid não encontrado${NC}"
fi

# Verificar se há outros processos PHP na porta 8080
echo -e "${BLUE}📋 Verificando outros processos na porta 8080...${NC}"

# Usar lsof para encontrar processos na porta 8080
if command_exists lsof; then
    PIDS=$(lsof -ti :8080 2>/dev/null)
    
    if [ ! -z "$PIDS" ]; then
        echo -e "${YELLOW}⚠️  Encontrados processos na porta 8080:${NC}"
        for PID in $PIDS; do
            PROCESS_NAME=$(ps -p $PID -o comm= 2>/dev/null)
            echo -e "${YELLOW}   - PID: $PID ($PROCESS_NAME)${NC}"
            
            read -p "Deseja encerrar este processo? (s/n): " -n 1 -r
            echo
            if [[ $REPLY =~ ^[Ss]$ ]]; then
                kill $PID 2>/dev/null
                if kill -0 $PID 2>/dev/null; then
                    kill -9 $PID 2>/dev/null
                fi
                echo -e "${GREEN}✅ Processo $PID encerrado${NC}"
            fi
        done
    else
        echo -e "${GREEN}✅ Nenhum processo encontrado na porta 8080${NC}"
    fi
else
    echo -e "${YELLOW}⚠️  Comando lsof não disponível para verificar portas${NC}"
fi

# Limpar arquivos temporários se necessário
echo -e "${BLUE}🧹 Limpando arquivos temporários...${NC}"

# Remover arquivo start-server.php se existir
if [ -f "start-server.php" ]; then
    rm -f start-server.php
    echo -e "${GREEN}✅ Arquivo temporário removido${NC}"
fi

# Verificar logs antigos
if [ -d "src/logs" ]; then
    LOG_COUNT=$(find src/logs -name "*.log" -mtime +7 2>/dev/null | wc -l)
    if [ $LOG_COUNT -gt 0 ]; then
        echo -e "${YELLOW}⚠️  Encontrados $LOG_COUNT arquivos de log com mais de 7 dias${NC}"
        read -p "Deseja remover logs antigos? (s/n): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Ss]$ ]]; then
            find src/logs -name "*.log" -mtime +7 -delete 2>/dev/null
            echo -e "${GREEN}✅ Logs antigos removidos${NC}"
        fi
    fi
fi

echo -e ""
echo -e "${GREEN}🎉 ChurchCRM parado com sucesso!${NC}"
echo -e ""
echo -e "${BLUE}📋 Resumo:${NC}"
echo -e "- Servidor web encerrado"
echo -e "- Porta 8080 liberada"
echo -e "- Arquivos temporários limpos"
echo -e ""
echo -e "${BLUE}🚀 Para reiniciar:${NC}"
echo -e "./start-local.sh"
