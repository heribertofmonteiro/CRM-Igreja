#!/bin/bash

# Script para alternar entre temas do ChurchCRM
# Uso: ./switch-theme-modern.sh [classic|modern|futuristic]

THEME=${1:-modern}

echo "🎨 Alternando tema do ChurchCRM para: $THEME"

case $THEME in
    "classic")
        echo "📜 Aplicando tema CLÁSSICO..."
        
        # Backup do arquivo atual
        cp src/skin/churchcrm.scss src/skin/churchcrm.scss.backup
        
        # Aplicar tema clássico
        cat > src/skin/churchcrm.scss << 'EOF'
@use "sass:meta";
/*! DO NOT MANUALLY EDIT skin/churchcrm.min.css

    Please make changes to skin/scss/*.scss and recompile with SASS. http://sass-lang.com/

    sass --watch src/skin/churchcrm.scss:src/skin/churchcrm.min.css --style compressed

*/

// Import Classic Theme (Design Original)
@import "churchcrm-classic.scss";
EOF
        
        echo "✅ Tema clássico aplicado"
        ;;
        
    "modern")
        echo "🚀 Aplicando tema MODERNO..."
        
        # Backup do arquivo atual
        cp src/skin/churchcrm.scss src/skin/churchcrm.scss.backup
        
        # Aplicar tema moderno
        cat > src/skin/churchcrm.scss << 'EOF'
@use "sass:meta";
/*! DO NOT MANUALLY EDIT skin/churchcrm.min.css

    Please make changes to skin/scss/*.scss and recompile with SASS. http://sass-lang.com/

    sass --watch src/skin/churchcrm.scss:src/skin/churchcrm.min.css --style compressed

*/

// Import Modern Theme (Bootstrap 5 + Tailwind CSS)
@import "churchcrm-modern.scss";
EOF
        
        echo "✅ Tema moderno aplicado"
        ;;
        
    "futuristic")
        echo "🔮 Aplicando tema FUTURISTA..."
        
        # Backup do arquivo atual
        cp src/skin/churchcrm.scss src/skin/churchcrm.scss.backup
        
        # Aplicar tema futurista
        cat > src/skin/churchcrm.scss << 'EOF'
@use "sass:meta";
/*! DO NOT MANUALLY EDIT skin/churchcrm.min.css

    Please make changes to skin/scss/*.scss and recompile with SASS. http://sass-lang.com/

    sass --watch src/skin/churchcrm.scss:src/skin/churchcrm.min.css --style compressed

*/

// Import Futuristic Theme (Bootstrap 5 + Tailwind CSS)
@import "churchcrm-futuristic.scss";
EOF
        
        echo "✅ Tema futurista aplicado"
        ;;
        
    *)
        echo "❌ Tema inválido! Use: classic, modern ou futuristic"
        echo "Exemplo: ./switch-theme-modern.sh modern"
        exit 1
        ;;
esac

# Compilar CSS
echo "🔨 Compilando CSS..."
npm run build:frontend

if [ $? -eq 0 ]; then
    echo "✅ CSS compilado com sucesso!"
    echo "🎯 Tema $THEME aplicado e pronto para uso"
    echo "🌐 Reinicie o servidor para ver as mudanças: ./start-server.sh"
    
    # Descrição do tema
    case $THEME in
        "classic")
            echo "📜 Tema Clássico: Design original do ChurchCRM com cores tradicionais"
            ;;
        "modern")
            echo "🚀 Tema Moderno: Bootstrap 5 + Tailwind CSS com cores vivas e design limpo"
            ;;
        "futuristic")
            echo "🔮 Tema Futurista: Design arrojado com gradientes, glassmorphism e efeitos neon"
            ;;
    esac
else
    echo "❌ Erro na compilação do CSS"
    exit 1
fi
