#!/bin/bash

# Script para alternar entre temas do ChurchCRM
# Uso: ./switch-theme.sh [classic|modern]

THEME=${1:-classic}

echo "🎨 Alternando tema do ChurchCRM para: $THEME"

case $THEME in
    "classic")
        echo "📜 Aplicando tema CLÁSSICO (design original)..."
        
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
        echo "🚀 Aplicando tema MODERNO (com atualizações)..."
        
        # Backup do arquivo atual
        cp src/skin/churchcrm.scss src/skin/churchcrm.scss.backup
        
        # Restaurar tema moderno (original)
        git checkout HEAD -- src/skin/churchcrm.scss 2>/dev/null || {
            echo "⚠️  Não foi possível restaurar do git, criando arquivo moderno padrão..."
            
            cat > src/skin/churchcrm.scss << 'EOF'
@use "sass:meta";
/*! DO NOT MANUALLY EDIT skin/churchcrm.min.css

    Please make changes to skin/scss/*.scss and recompile with SASS. http://sass-lang.com/

    sass --watch src/skin/churchcrm.scss:src/skin/churchcrm.min.css --style compressed

*/

// Import Libraries

// Import Bootstrap 5 from node_modules via webpack
@import "~bootstrap/scss/bootstrap";

// Import other external libraries
@import "external/bootstrap-datepicker/bootstrap-datepicker.standalone.min.css";
@import "external/bootstrap-daterangepicker/daterangepicker.css";
@import "external/datatables/datatables.min.css";
@import "external/select2/select2.min.css";
@import "external/adminlte/adminlte.min.css";

// jQuery UI is loaded from CDN in base template, not from local files (missing image assets)
@import "external/bootstrap-toggle/bootstrap-toggle.css";
@import "external/jquery.steps/jquery.steps.css";

// Import ChurchCRM custom styles

// Utility Classes - Reusable CSS classes for common styling patterns
@include meta.load-css("scss/utility-classes");

// UI Components - Custom ChurchCRM UI elements
@include meta.load-css("scss/ui-components");

@include meta.load-css("scss/dropdowns");
@include meta.load-css("scss/forms");
@include meta.load-css("scss/tables");
@include meta.load-css("scss/profileImage");
@include meta.load-css("scss/shortConfig");
@include meta.load-css("scss/leftNav");
@include meta.load-css("scss/imageOverlay");
@include meta.load-css("scss/maps");
@include meta.load-css("scss/spacing");
@include meta.load-css("scss/SystemNotifications");
@include meta.load-css("scss/cart");
@include meta.load-css("scss/calendars");
@include meta.load-css("scss/react-datepicker");
@include meta.load-css("scss/_two-factor-enrollment");
@include meta.load-css("scss/systemSettings");
@include meta.load-css("scss/kiosk");
@include meta.load-css("scss/groups");

html,
body {
    font-size: 14px;
}
EOF
        }
        
        echo "✅ Tema moderno aplicado"
        ;;
        
    *)
        echo "❌ Tema inválido! Use: classic ou modern"
        echo "Exemplo: ./switch-theme.sh classic"
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
else
    echo "❌ Erro na compilação do CSS"
    exit 1
fi
