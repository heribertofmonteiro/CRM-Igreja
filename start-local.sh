#!/bin/bash

# Script para iniciar o ChurchCRM em ambiente local (sem Docker)
# Este script inicia o servidor web e serviços necessários

echo "🚀 Iniciando ChurchCRM em ambiente local..."

# Cores para saída
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Função para verificar se um comando existe
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Função para verificar se um serviço está rodando na porta
is_port_in_use() {
    lsof -i :$1 >/dev/null 2>&1
}

# Verificar dependências
echo -e "${BLUE}📋 Verificando serviços...${NC}"

# Verificar MySQL/MariaDB
if command_exists mysql; then
    if mysql -u churchcrm -pchurchcrm123 -e "USE churchcrm;" 2>/dev/null; then
        echo -e "${GREEN}✅ Banco de dados acessível${NC}"
    else
        echo -e "${RED}❌ Não foi possível conectar ao banco de dados${NC}"
        echo -e "${YELLOW}Verifique se o MySQL/MariaDB está rodando e se o banco churchcrm existe${NC}"
        echo -e "${YELLOW}Execute: mysql -u root -p${NC}"
        echo -e "${YELLOW}E depois os comandos SQL do setup-local.sh${NC}"
        exit 1
    fi
else
    echo -e "${RED}❌ MySQL/MariaDB não encontrado${NC}"
    exit 1
fi

# Verificar se a porta 8080 está em uso
if is_port_in_use 8080; then
    echo -e "${YELLOW}⚠️  Porta 8080 já está em uso${NC}"
    echo -e "${YELLOW}Verificando se é outro processo do ChurchCRM...${NC}"
    
    # Tentar identificar o processo
    PID=$(lsof -ti :8080)
    if [ ! -z "$PID" ]; then
        PROCESS_NAME=$(ps -p $PID -o comm=)
        echo -e "${YELLOW}Processo na porta 8080: $PROCESS_NAME (PID: $PID)${NC}"
        
        read -p "Deseja encerrar o processo e iniciar o ChurchCRM? (s/n): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Ss]$ ]]; then
            kill $PID
            echo -e "${GREEN}✅ Processo encerrado${NC}"
            sleep 2
        else
            echo -e "${RED}❌ Operação cancelada${NC}"
            exit 1
        fi
    fi
fi

# Verificar arquivos de configuração
echo -e "${BLUE}📋 Verificando arquivos de configuração...${NC}"

if [ ! -f ".env.local" ]; then
    echo -e "${RED}❌ Arquivo .env.local não encontrado${NC}"
    echo -e "${YELLOW}Execute ./setup-local.sh primeiro${NC}"
    exit 1
fi

if [ ! -f "src/Include/Config.php" ]; then
    echo -e "${RED}❌ Arquivo src/Include/Config.php não encontrado${NC}"
    echo -e "${YELLOW}Execute ./setup-local.sh primeiro${NC}"
    exit 1
fi

if [ ! -d "src/vendor" ]; then
    echo -e "${RED}❌ Dependências PHP não instaladas${NC}"
    echo -e "${YELLOW}Execute ./setup-local.sh primeiro${NC}"
    exit 1
fi

if [ ! -d "node_modules" ]; then
    echo -e "${RED}❌ Dependências Node.js não instaladas${NC}"
    echo -e "${YELLOW}Execute ./setup-local.sh primeiro${NC}"
    exit 1
fi

# Carregar variáveis de ambiente
export $(grep -v '^#' .env.local | xargs)

# Iniciar servidor web
echo -e "${BLUE}🌐 Iniciando servidor web...${NC}"

# Criar um script para iniciar o servidor PHP
cat > start-server.php << 'EOF'
<?php
// Carregar variáveis de ambiente
$envFile = __DIR__ . '/.env.local';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
        $_SERVER[trim($key)] = trim($value);
        putenv(trim($key) . '=' . trim($value));
    }
}

// Configurar timezone
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'America/Sao_Paulo');

// Configurar display errors
if ($_ENV['APP_DEBUG'] === 'true') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

// Configurar log
ini_set('log_errors', 1);
ini_set('error_log', $_ENV['LOG_PATH'] ?? __DIR__ . '/src/logs/php-error.log');

echo "ChurchCRM Server iniciado em " . date('Y-m-d H:i:s') . "\n";
echo "Acessível em: http://localhost:8080\n";
echo "Pressione Ctrl+C para parar\n\n";

// Iniciar servidor PHP
$host = 'localhost';
$port = 8080;
$docRoot = __DIR__ . '/src';

$command = "php -S $host:$port -t $docRoot";
echo "Executando: $command\n";
system($command);
EOF

# Iniciar servidor em background
php start-server.php &
SERVER_PID=$!

# Aguardar um momento para o servidor iniciar
sleep 3

# Verificar se o servidor está rodando
if is_port_in_use 8080; then
    echo -e "${GREEN}✅ Servidor web iniciado com sucesso${NC}"
    echo -e "${GREEN}🌐 ChurchCRM acessível em: http://localhost:8080${NC}"
else
    echo -e "${RED}❌ Falha ao iniciar o servidor web${NC}"
    kill $SERVER_PID 2>/dev/null
    exit 1
fi

# Salvar PID para poder parar depois
echo $SERVER_PID > .server.pid

# Mostrar informações úteis
echo -e ""
echo -e "${GREEN}🎉 ChurchCRM iniciado com sucesso!${NC}"
echo -e ""
echo -e "${BLUE}📋 Informações:${NC}"
echo -e "- URL: http://localhost:8080"
echo -e "- PID do servidor: $SERVER_PID"
echo -e "- Logs em: src/logs/"
echo -e "- Uploads em: src/Uploads/"
echo -e ""
echo -e "${BLUE}🔧 Comandos úteis:${NC}"
echo -e "- Parar o servidor: ./stop-local.sh"
echo -e "- Ver logs: tail -f src/logs/php-error.log"
echo -e "- Reiniciar: ./stop-local.sh && ./start-local.sh"
echo -e ""
echo -e "${YELLOW}⚠️  Importante:${NC}"
echo -e "- Mantenha esta janela aberta ou use nohup para rodar em background"
echo -e "- Para desenvolvimento, considere usar um servidor web completo (Apache/Nginx)"
echo -e "- Configure o virtual host para produção"

# Manter o script rodando
echo -e "${BLUE}Pressione Ctrl+C para parar o servidor...${NC}"
trap "echo -e '\n${YELLOW}Parando servidor...${NC}'; kill $SERVER_PID 2>/dev/null; rm -f .server.pid; echo -e '${GREEN}✅ Servidor parado${NC}'; exit 0" INT

# Aguardar o processo do servidor
wait $SERVER_PID
