#!/bin/bash

# Script de configuração do ChurchCRM para uso sem Docker
# Este script prepara o ambiente local para desenvolvimento

echo "🚀 Configurando ChurchCRM para uso local (sem Docker)..."

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

# Verificar dependências
echo -e "${BLUE}📋 Verificando dependências...${NC}"

# Verificar PHP
if command_exists php; then
    PHP_VERSION=$(php -r "echo PHP_VERSION;")
    echo -e "${GREEN}✅ PHP encontrado: $PHP_VERSION${NC}"
    
    # Verificar versão mínima do PHP (8.2)
    if php -r "exit(version_compare(PHP_VERSION, '8.2.0', '<') ? 1 : 0);"; then
        echo -e "${GREEN}✅ Versão do PHP compatível${NC}"
    else
        echo -e "${RED}❌ PHP 8.2+ é requerido. Versão atual: $PHP_VERSION${NC}"
        exit 1
    fi
else
    echo -e "${RED}❌ PHP não encontrado. Por favor, instale PHP 8.2+${NC}"
    exit 1
fi

# Verificar Composer
if command_exists composer; then
    COMPOSER_VERSION=$(composer --version | head -n1 | cut -d' ' -f3)
    echo -e "${GREEN}✅ Composer encontrado: $COMPOSER_VERSION${NC}"
else
    echo -e "${RED}❌ Composer não encontrado. Por favor, instale o Composer${NC}"
    exit 1
fi

# Verificar Node.js
if command_exists node; then
    NODE_VERSION=$(node --version)
    echo -e "${GREEN}✅ Node.js encontrado: $NODE_VERSION${NC}"
else
    echo -e "${RED}❌ Node.js não encontrado. Por favor, instale o Node.js${NC}"
    exit 1
fi

# Verificar npm
if command_exists npm; then
    NPM_VERSION=$(npm --version)
    echo -e "${GREEN}✅ npm encontrado: $NPM_VERSION${NC}"
else
    echo -e "${RED}❌ npm não encontrado. Por favor, instale o npm${NC}"
    exit 1
fi

# Verificar MySQL/MariaDB
if command_exists mysql; then
    echo -e "${GREEN}✅ MySQL/MariaDB encontrado${NC}"
else
    echo -e "${YELLOW}⚠️  MySQL/MariaDB não encontrado no PATH. Verifique se está instalado${NC}"
fi

# Criar diretórios necessários
echo -e "${BLUE}📁 Criando diretórios necessários...${NC}"

DIRECTORIES=(
    "src/logs"
    "src/sessions"
    "src/cache"
    "src/Uploads"
    "src/Uploads/family"
    "src/Uploads/person"
    "src/Uploads/church"
    "src/Uploads/temp"
)

for dir in "${DIRECTORIES[@]}"; do
    if [ ! -d "$dir" ]; then
        mkdir -p "$dir"
        echo -e "${GREEN}✅ Diretório criado: $dir${NC}"
    else
        echo -e "${YELLOW}⚠️  Diretório já existe: $dir${NC}"
    fi
done

# Definir permissões
echo -e "${BLUE}🔐 Configurando permissões...${NC}"
chmod -R 755 src/logs src/sessions src/cache src/Uploads
chmod -R 777 src/logs src/sessions src/cache src/Uploads

# Instalar dependências PHP
echo -e "${BLUE}📦 Instalando dependências PHP...${NC}"
cd src
if [ ! -d "vendor" ]; then
    composer install
    echo -e "${GREEN}✅ Dependências PHP instaladas${NC}"
else
    echo -e "${YELLOW}⚠️  Dependências PHP já instaladas${NC}"
fi
cd ..

# Instalar dependências Node.js
echo -e "${BLUE}📦 Instalando dependências Node.js...${NC}"
if [ ! -d "node_modules" ]; then
    npm install
    echo -e "${GREEN}✅ Dependências Node.js instaladas${NC}"
else
    echo -e "${YELLOW}⚠️  Dependências Node.js já instaladas${NC}"
fi

# Compilar assets
echo -e "${BLUE}🔨 Compilando assets...${NC}"
npm run build:frontend

# Configurar banco de dados
echo -e "${BLUE}🗄️  Configuração do banco de dados...${NC}"
echo -e "${YELLOW}Por favor, configure o banco de dados manualmente:${NC}"
echo -e "1. Crie um banco de dados chamado 'churchcrm'"
echo -e "2. Crie um usuário 'churchcrm' com senha 'churchcrm123'"
echo -e "3. Conceda todos os privilégios ao usuário no banco de dados"
echo -e ""
echo -e "${BLUE}Comandos SQL para criar o banco de dados:${NC}"
echo -e "CREATE DATABASE churchcrm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
echo -e "CREATE USER 'churchcrm'@'localhost' IDENTIFIED BY 'churchcrm123';"
echo -e "GRANT ALL PRIVILEGES ON churchcrm.* TO 'churchcrm'@'localhost';"
echo -e "FLUSH PRIVILEGES;"

# Criar arquivo de configuração local
echo -e "${BLUE}⚙️  Criando arquivo de configuração local...${NC}"
if [ ! -f "src/Include/Config.php" ]; then
    if [ -f "docker/Config.php" ]; then
        cp docker/Config.php src/Include/Config.php
        echo -e "${GREEN}✅ Config.php copiado para src/Include/Config.php${NC}"
    else
        echo -e "${YELLOW}⚠️  Arquivo docker/Config.php não encontrado. Configure manualmente${NC}"
    fi
else
    echo -e "${YELLOW}⚠️  src/Include/Config.php já existe${NC}"
fi

# Resumo da configuração
echo -e "${GREEN}🎉 Configuração concluída!${NC}"
echo -e ""
echo -e "${BLUE}📋 Resumo:${NC}"
echo -e "- Ambiente local configurado"
echo -e "- Dependências instaladas"
echo -e "- Assets compilados"
echo -e "- Diretórios criados com permissões adequadas"
echo -e ""
echo -e "${BLUE}🚀 Próximos passos:${NC}"
echo -e "1. Configure o banco de dados MySQL/MariaDB"
echo -e "2. Inicie o servidor web PHP:"
echo -e "   cd src && php -S localhost:8080"
echo -e "3. Acesse a aplicação em: http://localhost:8080"
echo -e "4. Execute o instalador web para finalizar a configuração"
echo -e ""
echo -e "${YELLOW}⚠️  Importante:${NC}"
echo -e "- Mantenha o arquivo .env.local seguro"
echo -e "- Altere as senhas padrão em produção"
echo -e "- Configure o servidor web (Apache/Nginx) para produção"
