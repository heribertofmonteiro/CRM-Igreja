#!/bin/bash

# Script para aplicar o menu melhorado e agrupado
# Substitui o menu padrão pelo menu organizado por afinidade

echo "🎨 Aplicando Menu Melhorado e Agrupado..."

# Backup dos arquivos originais
echo "📦 Fazendo backup dos arquivos originais..."
cp src/ChurchCRM/Config/Menu/Menu.php src/ChurchCRM/Config/Menu/Menu.php.backup
cp src/ChurchCRM/view/MenuRenderer.php src/ChurchCRM/view/MenuRenderer.php.backup

# Aplicar menu melhorado
echo "🔄 Aplicando menu melhorado..."
cp src/ChurchCRM/Config/Menu/MenuImproved.php src/ChurchCRM/Config/Menu/MenuImproved.php.temp
cp src/ChurchCRM/view/MenuRendererImproved.php src/ChurchCRM/view/MenuRendererImproved.php.temp

# Substituir imports nos arquivos
echo "🔧 Substituindo imports dos arquivos..."

# Substituir Menu.php para usar MenuImproved
sed -i 's/class Menu/class MenuImproved/g' src/ChurchCRM/Config/Menu/MenuImproved.php.temp
sed -i 's/Menu::init()/MenuImproved::init()/g' src/ChurchCRM/Config/Menu/MenuImproved.php.temp
sed -i 's/Menu::getMenu()/MenuImproved::getMenu()/g' src/ChurchCRM/Config/Menu/MenuImproved.php.temp

# Substituir MenuRenderer.php para usar MenuRendererImproved
sed -i 's/class MenuRenderer/class MenuRendererImproved/g' src/ChurchCRM/view/MenuRendererImproved.php.temp
sed -i 's/MenuRenderer::renderMenu()/MenuRendererImproved::renderMenu()/g' src/ChurchCRM/view/MenuRendererImproved.php.temp

# Mover arquivos temporários para os originais
mv src/ChurchCRM/Config/Menu/MenuImproved.php.temp src/ChurchCRM/Config/Menu/Menu.php
mv src/ChurchCRM/view/MenuRendererImproved.php.temp src/ChurchCRM/view/MenuRenderer.php

# Compilar CSS com o novo sidebar
echo "🎨 Compilando CSS com sidebar melhorado..."
npm run build:frontend

if [ $? -eq 0 ]; then
    echo "✅ Menu melhorado aplicado com sucesso!"
    echo ""
    echo "🎯 Melhorias Aplicadas:"
    echo "  📋 Agrupamento por afinidade:"
    echo "    - 👥 People (Cadastro + Visualização)"
    echo "    - 📅 Calendar & Events (Calendário + Eventos)"
    echo "    - 🎯 Ministry & Groups (Ministérios + Grupos)"
    echo "    - 🏫 Education (Escola Dominical)"
    echo "    - 💰 Finance (Depósitos + Relatórios)"
    echo "    - 📧 Communication (Email + Notificações)"
    echo "    - 📊 Reports & Analytics (Relatórios + Análises)"
    echo "    - ⚙️ Administration (Configurações + Ferramentas)"
    echo ""
    echo "  🎨 Design Melhorado:"
    echo "    - Cores vivas por categoria"
    echo "    - Gradientes modernos"
    echo "    - Animações suaves"
    echo "    - Hover effects"
    echo "    - Badges informativos"
    echo ""
    echo "  📱 Responsividade:"
    echo "    - Sidebar adaptável"
    echo "    - Menu mobile-friendly"
    echo "    - Transições suaves"
    echo ""
    echo "🌐 Reinicie o servidor para ver as mudanças:"
    echo "   ./start-server.sh"
    echo ""
    echo "🔗 Acesse:"
    echo "   http://localhost:8080"
    echo "   Login: admin/0631"
else
    echo "❌ Erro na compilação do CSS"
    echo "🔄 Restaurando arquivos originais..."
    mv src/ChurchCRM/Config/Menu/Menu.php.backup src/ChurchCRM/Config/Menu/Menu.php
    mv src/ChurchCRM/view/MenuRenderer.php.backup src/ChurchCRM/view/MenuRenderer.php
    exit 1
fi
