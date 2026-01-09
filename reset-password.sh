#!/bin/bash

# Script para resetar senha do admin do ChurchCRM
# Uso: ./reset-password.sh [nova_senha]

PASSWORD=${1:-changeme}

echo "🔐 Resetando senha do admin do ChurchCRM..."
echo "Nova senha: $PASSWORD"

# Obter o PersonID do admin
PERSON_ID=$(mysql -u churchcrm -pchurchcrm123 churchcrm -sN -e "SELECT usr_per_ID FROM user_usr WHERE usr_UserName = 'admin';")

if [ -z "$PERSON_ID" ]; then
    echo "❌ Usuário admin não encontrado"
    exit 1
fi

echo "PersonID: $PERSON_ID"

# Gerar hash da senha usando o método do ChurchCRM (SHA256 + PersonID)
HASH=$(php -r "echo hash('sha256', '$PASSWORD' . '$PERSON_ID');")

echo "Hash gerado: $HASH"

# Atualizar senha no banco de dados
mysql -u churchcrm -pchurchcrm123 churchcrm -e "UPDATE user_usr SET usr_Password = '$HASH' WHERE usr_UserName = 'admin';"

if [ $? -eq 0 ]; then
    echo "✅ Senha atualizada com sucesso!"
    echo "📱 Login: admin/$PASSWORD"
    echo "🌐 Acesse: http://localhost:8080"
else
    echo "❌ Erro ao atualizar senha"
    exit 1
fi
