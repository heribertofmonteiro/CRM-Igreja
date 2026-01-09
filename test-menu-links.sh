#!/bin/bash

# Script para testar todos os links da barra lateral

echo "🔍 Testando todos os links da barra lateral..."

# Fazer login primeiro
echo "🔐 Fazendo login..."
curl -s -c /tmp/menu-test-cookies.txt -X POST \
    -d "User=admin&Password=0631" \
    http://localhost:8080/session/begin > /dev/null

# Array de links para testar
declare -A links=(
    # Dashboard
    ["Dashboard"]="v2/dashboard"
    
    # People - Registration
    ["Add New Person"]="PersonEditor.php"
    ["Add New Family"]="FamilyEditor.php"
    
    # People - View
    ["Active People"]="v2/people"
    ["Inactive People"]="v2/people?familyActiveStatus=inactive"
    ["All People"]="v2/people?familyActiveStatus=all"
    ["Active Families"]="v2/family"
    ["Inactive Families"]="v2/family?mode=inactive"
    
    # People Dashboard
    ["People Dashboard"]="PeopleDashboard.php"
    
    # Calendar
    ["Calendar"]="v2/calendar"
    
    # Events
    ["Add Event"]="EventEditor.php"
    ["List Events"]="ListEvents.php"
    ["Event Types"]="EventNames.php"
    ["Check-in/Check-out"]="Checkin.php"
    ["Event Attendance"]="EventAttendance.php"
    
    # Groups
    ["Groups"]="GroupList.php"
    
    # Ministries
    ["Ministries"]="v2/ministerio"
    ["Ministry Meetings"]="v2/ministerio/reunioes"
    ["Ministry Messages"]="v2/ministerio/mensagens"
    
    # Sunday School
    ["Sunday School Dashboard"]="sundayschool/SundaySchoolDashboard.php"
    
    # Finance
    ["View All Deposits"]="FindDepositSlip.php"
    ["Deposit Reports"]="FinancialReports.php"
    ["Financial Reports"]="FinancialReports.php"
    ["Tax Report"]="TaxReport.php"
    
    # Fundraisers
    ["Create New Fundraiser"]="FundRaiserEditor.php?FundRaiserID=-1"
    ["View All Fundraisers"]="FindFundRaiser.php"
    
    # Email
    ["Email Dashboard"]="v2/email/dashboard"
    
    # Reports
    ["Query Menu"]="QueryList.php"
    
    # Admin
    ["General Settings"]="SystemSettings.php"
    ["System Users"]="UserList.php"
    ["Property Types"]="PropertyTypeList.php"
    ["Backup Database"]="BackupDatabase.php"
    ["CSV Import"]="CSVImport.php"
    ["Kiosk Manager"]="KioskManager.php"
    ["Debug"]="v2/admin/debug"
    ["System Logs"]="v2/admin/logs"
    ["Custom Menus"]="v2/admin/menus"
)

echo "📋 Testando $((${#links[@]})) links..."

# Contadores
success=0
error=0
redirect=0

# Testar cada link
for name in "${!links[@]}"; do
    url="${links[$name]}"
    
    echo -n "🔗 Testing $name... "
    
    # Fazer requisição
    response=$(curl -s -w "%{http_code}" -b /tmp/menu-test-cookies.txt \
        -o /dev/null \
        "http://localhost:8080/$url")
    
    # Verificar status
    case $response in
        200)
            echo "✅ OK ($response)"
            ((success++))
            ;;
        302|301)
            echo "🔄 Redirect ($response)"
            ((redirect++))
            ;;
        403)
            echo "🚫 Forbidden ($response)"
            ((error++))
            ;;
        404)
            echo "❌ Not Found ($response)"
            ((error++))
            ;;
        500)
            echo "💥 Server Error ($response)"
            ((error++))
            ;;
        *)
            echo "⚠️  Unknown ($response)"
            ((error++))
            ;;
    esac
done

# Resumo
echo ""
echo "📊 Resumo dos Testes:"
echo "✅ Sucesso: $success"
echo "🔄 Redirects: $redirect"
echo "❌ Erros: $error"
echo "📋 Total: $((${#links[@]}))"

# Verificar se há problemas críticos
if [ $error -gt 0 ]; then
    echo ""
    echo "⚠️  Links com problemas:"
    echo "Verifique os arquivos e rotas correspondentes"
    
    # Sugerir correções
    echo ""
    echo "🔧 Sugestões de correção:"
    echo "1. Verifique se os arquivos PHP existem"
    echo "2. Verifique as rotas em src/v2/routes/"
    echo "3. Verifique permissões de acesso"
    echo "4. Verifique configurações de URL"
else
    echo ""
    echo "🎉 Todos os links estão funcionando!"
fi

# Limpar cookies
rm -f /tmp/menu-test-cookies.txt

echo ""
echo "🎯 Teste concluído"
