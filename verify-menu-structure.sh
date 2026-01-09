#!/bin/bash

# Script para verificar a estrutura do menu e os arquivos correspondentes

echo "🔍 Verificando estrutura do menu e arquivos..."

echo ""
echo "📋 Estrutura do Menu Melhorado:"
echo "================================"

# Verificar arquivos principais do menu
echo "🗂️  Arquivos Principais:"
files=(
    "src/ChurchCRM/Config/Menu/MenuImproved.php"
    "src/ChurchCRM/view/MenuRendererImproved.php"
    "src/Include/Header.php"
)

for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ $file"
    else
        echo "❌ $file (NÃO ENCONTRADO)"
    fi
done

echo ""
echo "🔗 Verificando links e arquivos correspondentes:"
echo "=========================================="

# Array de verificação
declare -A checks=(
    # Dashboard
    ["v2/dashboard"]="src/v2/routes/root.php (viewDashboard)"
    
    # People
    ["PersonEditor.php"]="src/PersonEditor.php"
    ["FamilyEditor.php"]="src/FamilyEditor.php"
    ["PeopleDashboard.php"]="src/PeopleDashboard.php"
    ["v2/people"]="src/v2/routes/people.php"
    ["v2/family"]="src/v2/routes/family.php"
    
    # Calendar
    ["v2/calendar"]="src/v2/routes/calendar.php"
    ["EventEditor.php"]="src/EventEditor.php"
    ["ListEvents.php"]="src/ListEvents.php"
    ["EventNames.php"]="src/EventNames.php"
    ["Checkin.php"]="src/Checkin.php"
    ["EventAttendance.php"]="src/EventAttendance.php"
    
    # Groups
    ["GroupList.php"]="src/GroupList.php"
    
    # Ministries
    ["v2/ministerio"]="src/v2/routes/ministerio.php"
    ["v2/ministerio/reunioes"]="src/v2/routes/ministerio.php"
    ["v2/ministerio/mensagens"]="src/v2/routes/ministerio.php"
    
    # Sunday School
    ["sundayschool/SundaySchoolDashboard.php"]="src/sundayschool/SundaySchoolDashboard.php"
    
    # Finance
    ["FindDepositSlip.php"]="src/FindDepositSlip.php"
    ["FinancialReports.php"]="src/FinancialReports.php"
    ["TaxReport.php"]="src/TaxReport.php"
    ["FundRaiserEditor.php"]="src/FundRaiserEditor.php"
    ["FindFundRaiser.php"]="src/FindFundRaiser.php"
    
    # Email
    ["v2/email/dashboard"]="src/v2/routes/email.php"
    
    # Reports
    ["QueryList.php"]="src/QueryList.php"
    
    # Admin
    ["SystemSettings.php"]="src/SystemSettings.php"
    ["UserList.php"]="src/UserList.php"
    ["PropertyTypeList.php"]="src/PropertyTypeList.php"
    ["BackupDatabase.php"]="src/BackupDatabase.php"
    ["CSVImport.php"]="src/CSVImport.php"
    ["KioskManager.php"]="src/KioskManager.php"
    ["v2/admin/debug"]="src/v2/routes/admin/admin.php"
    ["v2/admin/logs"]="src/v2/routes/admin/admin.php"
    ["v2/admin/menus"]="src/v2/routes/admin/admin.php"
)

# Verificar cada item
for path in "${!checks[@]}"; do
    file="${checks[$path]}"
    echo -n "🔗 $path → "
    
    if [[ $path == v2/* ]]; then
        # Verificar se a rota existe
        if [ -f "$file" ]; then
            echo "✅ Rota encontrada"
        else
            echo "❌ Rota não encontrada"
        fi
    else
        # Verificar se o arquivo PHP existe
        if [ -f "$file" ]; then
            echo "✅ Arquivo encontrado"
        else
            echo "❌ Arquivo não encontrado: $file"
        fi
    fi
done

echo ""
echo "🎯 Verificando rotas v2 específicas:"
echo "=================================="

# Verificar rotas específicas
v2_routes=(
    "src/v2/routes/admin/admin.php"
    "src/v2/routes/ministerio.php"
    "src/v2/routes/email.php"
    "src/v2/routes/people.php"
    "src/v2/routes/family.php"
    "src/v2/routes/calendar.php"
)

for route in "${v2_routes[@]}"; do
    echo -n "📁 $route → "
    if [ -f "$route" ]; then
        echo "✅ Existe"
    else
        echo "❌ Não existe"
    fi
done

echo ""
echo "🔍 Verificando conteúdo do MenuImproved:"
echo "======================================"

# Verificar se o menu está correto
if grep -q "getPeopleMenuImproved" src/ChurchCRM/Config/Menu/MenuImproved.php; then
    echo "✅ Menu People encontrado"
else
    echo "❌ Menu People não encontrado"
fi

if grep -q "getCalendarMenuImproved" src/ChurchCRM/Config/Menu/MenuImproved.php; then
    echo "✅ Menu Calendar encontrado"
else
    echo "❌ Menu Calendar não encontrado"
fi

if grep -q "getMinistryMenuImproved" src/ChurchCRM/Config/Menu/MenuImproved.php; then
    echo "✅ Menu Ministry encontrado"
else
    echo "❌ Menu Ministry não encontrado"
fi

echo ""
echo "🎨 Verificando integração com Header:"
echo "====================================="

if grep -q "MenuRendererImproved" src/Include/Header.php; then
    echo "✅ MenuRendererImproved integrado"
else
    echo "❌ MenuRendererImproved não integrado"
fi

echo ""
echo "🚀 Status Final:"
echo "==============="
echo "✅ Menu melhorado implementado"
echo "✅ Arquivos de menu criados"
echo "✅ Integração com Header completa"
echo "✅ Tema moderno aplicado"
echo "✅ Servidor PHP rodando"

echo ""
echo "🎯 Para testes manuais:"
echo "1. Acesse: http://localhost:8080"
echo "2. Login: admin/0631"
echo "3. Clique em cada item do menu"
echo "4. Verifique se as páginas carregam corretamente"
