# 🎨 Temas do ChurchCRM - Design Original Mantido

## ✅ Status: DESIGN ORIGINAL RESTAURADO

O ChurchCRM agora mantém o **design clássico original**, sem as atualizações visuais modernas que alteraram a experiência familiar.

## 🎯 Temas Disponíveis

### 📜 Tema Clássico (Default)
- **Design:** Original do ChurchCRM
- **Cores:** Azul clássico (#3c8dbc)
- **Fonte:** Helvetica Neue, Arial
- **Layout:** Limpo, funcional, familiar

### 🚀 Tema Moderno (Opcional)
- **Design:** Com atualizações visuais
- **Cores:** Padrão Bootstrap 5
- **Recursos:** Todos os componentes modernos

## 🛠️ Como Alternar Temas

### Via Script (Recomendado)
```bash
# Aplicar tema clássico (design original)
./switch-theme.sh classic

# Aplicar tema moderno (com atualizações)
./switch-theme.sh modern
```

### Via Manual
```bash
# Editar arquivo SCSS principal
vim src/skin/churchcrm.scss

# Recompilar CSS
npm run build:frontend

# Reiniciar servidor
./start-server.sh
```

## 🎨 Características do Tema Clássico

### 🎨 Cores
- **Header:** #3c8dbc (azul ChurchCRM)
- **Sidebar:** #222d32 (cinza escuro)
- **Background:** #ecf0f5 (cinza claro)
- **Cards:** Branco com sombras sutis
- **Botões:** Esquema de cores clássico

### 📐 Layout
- **Header:** Limpo, sem excessos
- **Sidebar:** Navegação clássica
- **Content:** Área de trabalho limpa
- **Cards:** Design funcional e objetivo

### 🎯 Componentes
- **Tabelas:** Estilo clássico com hover sutil
- **Formulários:** Inputs tradicionais
- **Botões:** Design clássico com hover
- **Badges:** Cores consistentes

## 📁 Arquivos de Tema

### Principal
- `src/skin/churchcrm.scss` - Arquivo principal de configuração
- `src/skin/churchcrm-classic.scss` - Estilos clássicos

### Compilado
- `src/skin/v2/churchcrm.min.css` - CSS final (1.5MB)

### Scripts
- `switch-theme.sh` - Script para alternar temas
- `start-server.sh` - Iniciar servidor
- `reset-password.sh` - Resetar senha admin

## 🔧 Manutenção

### Para Adicionar Novo Tema
1. Criar arquivo `nome-tema.scss`
2. Definir cores e estilos
3. Adicionar import em `churchcrm.scss`
4. Compilar com `npm run build:frontend`

### Para Personalizar Tema Clássico
1. Editar `src/skin/churchcrm-classic.scss`
2. Modificar cores desejadas
3. Recompilar CSS

## 🌐 Acesso

Após aplicar o tema:
```bash
# Iniciar servidor
./start-server.sh

# Acessar no navegador
http://localhost:8080

# Login
admin/sua_senha
```

## ✅ Benefícios

### Tema Clássico
- ✅ **Familiaridade** - Design que os usuários conhecem
- ✅ **Performance** - Menos CSS, mais rápido
- ✅ **Compatibilidade** - Funciona em todos os navegadores
- ✅ **Estabilidade** - Sem bugs visuais

### Sem Mudanças Tecnológicas
- ✅ **Bootstrap 5** mantido (funcionalidade)
- ✅ **AdminLTE** mantido (estrutura)
- ✅ **JavaScript** intacto (funcionalidades)
- ✅ **API** preservada (funcional)

## 🎯 Conclusão

O ChurchCRM agora **mantém o design original** enquanto preserva todas as funcionalidades modernas. O usuário tem a opção de usar o tema clássico familiar ou o moderno com atualizações.

**Design original restaurado com sucesso!** 🎉
