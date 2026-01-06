# Análise do Projeto ChurchCRM

## Visão Geral
O **ChurchCRM** é um sistema de CRM voltado para igrejas, projetado para gerenciar membros, eventos, finanças e atividades administrativas.  
O projeto combina um **backend PHP monolítico** com um **frontend híbrido**, mesclando tecnologias legadas e modernas.

---

## Tecnologias e Frameworks Identificados

### Backend (PHP)
- **PHP 8.2+**  
  Linguagem principal do backend.
- **Slim Framework 4.15.0**  
  Microframework PHP para APIs e aplicações web.
- **Propel ORM 2.0.0-alpha12**  
  ORM para mapeamento objeto-relacional.
- **Twig 3.20.0**  
  Template engine para renderização de views.
- **Symfony Components**  
  - Dependency Injection  
  - Translation
- **Monolog 2.10.0**  
  Sistema de logging.

---

### Frontend (JavaScript / TypeScript)
- **React 19.2.0**  
  Biblioteca principal para componentes modernos de UI.
- **TypeScript 5.7.2**  
  Superset do JavaScript com tipagem estática.
- **Bootstrap 4.6.2**  
  Framework CSS para layout responsivo.
- **jQuery 3.7.1**  
  Biblioteca JavaScript utilizada em partes legadas.
- **AdminLTE 3.2.0**  
  Template administrativo baseado em Bootstrap.

---

### Ferramentas de Build
- **Webpack 5.97.1**  
  Bundler para assets JavaScript e CSS.
- **Grunt**  
  Task runner para automação de tarefas.
- **Sass / SCSS**  
  Pré-processador CSS.
- **npm**  
  Gerenciador de pacotes JavaScript.

---

### Banco de Dados
- **MySQL / MariaDB**  
  Banco de dados relacional principal.
- **Propel ORM**  
  Camada de abstração para acesso ao banco (via `mysqli`).

---

### Testes e Qualidade de Código (QA)
- **PHPUnit 11.5**  
  Testes unitários em PHP.
- **Cypress 15.4.0**  
  Testes end-to-end (E2E).
- **PHPStan 2.1.6**  
  Análise estática de código PHP.
- **PHP_CodeSniffer 3.11.3**  
  Verificação de padrões e estilo de código.

---

### Infraestrutura e DevOps
- **Docker & Docker Compose**  
  Containerização do ambiente.
- **Apache**  
  Servidor web (executado via Docker).
- **GitHub Actions**  
  Pipeline de CI/CD.

---

### Outras Tecnologias
- **i18next**  
  Internacionalização (i18n).
- **Chart.js 4.5.0**  
  Gráficos e visualizações de dados.
- **FullCalendar 6.1.19**  
  Componente de calendário.
- **Font Awesome 6.7.2**  
  Biblioteca de ícones.
- **Uppy**  
  Upload e gerenciamento de arquivos.

---

## Arquitetura do Projeto

### Características Principais
- Backend PHP **monolítico** utilizando Slim Framework.
- Frontend **híbrido**:
  - Páginas tradicionais com **Twig + jQuery**
  - Componentes modernos em **React + TypeScript**
- Sistema completo de **internacionalização**, com suporte a múltiplos idiomas.
- Processo de build moderno usando **Webpack**.
- Ambiente totalmente **containerizado com Docker**.
- **Suíte completa de testes automatizados**, cobrindo backend e frontend.

---

## Conclusão
O ChurchCRM é um projeto maduro, robusto e funcional, que combina tecnologias legadas e modernas.  
Apesar de sua arquitetura monolítica, o uso de React, TypeScript e ferramentas modernas de QA e CI/CD mostra uma evolução gradual rumo a boas práticas contemporâneas de desenvolvimento.

# Análise de Atualizações Possíveis

## Atualizações Críticas Recomendadas

---

## Frontend – JavaScript / TypeScript

### Bootstrap 4 → 5 (**Alto Impacto**)
- **Atual:** Bootstrap 4.6.2  
- **Latest:** 5.3.8  
- **Impacto:** 🔴 **MUITO ALTO** — *breaking changes significativos*

**Principais mudanças:**
- Remoção do **jQuery** como dependência
- Mudanças em classes CSS  
  - Ex: `ml-*` → `ms-*`
- Novo sistema de cores e grid
- Atualização completa dos componentes JavaScript

---

### AdminLTE 3 → 4 (**Médio Impacto**)
- **Atual:** AdminLTE 3.2.0  
- **Latest:** 4.0.0-rc6  
- **Impacto:** 🟠 **MÉDIO** — baseado em Bootstrap 5

**Benefícios:**
- Modernização da interface
- Melhor acessibilidade (**WCAG 2.1 AA**)

---

### React Types (**Baixo Impacto**)
- **@types/react:** 18.3.18 → 19.2.7  
- **@types/react-dom:** 18.3.5 → 19.2.3  
- **Impacto:** 🟢 **BAIXO** — apenas atualização de tipos

---

### Uppy Components (**Baixo Impacto**)
- **Atual:** `@uppy/*` 4.x  
- **Latest:** 5.x  
- **Impacto:** 🟢 **BAIXO** — componentes de upload

---

## Backend – PHP

### Symfony Components (**Médio Impacto**)
- `symfony/dependency-injection:` 6.0.20 → 7.4.3  
- `symfony/translation:` 5.4.35 → 7.4.3  
- **Impacto:** 🟠 **MÉDIO** — possíveis *breaking changes* entre versões major

---

### Slim Framework (**Mínimo Impacto**)
- `slim/slim:` 4.15.0 → 4.15.1  
- `slim/psr7:` 1.7 → 1.8.0  
- **Impacto:** 🟢 **MÍNIMO** — apenas *patch versions*

---

### Monolog (**Médio Impacto**)
- **Atual:** 2.10.0  
- **Latest:** 3.10.0  
- **Impacto:** 🟠 **MÉDIO** — major version com *breaking changes*

---

### PHPUnit (**Mínimo Impacto**)
- **Atual:** 11.5  
- **Latest:** 11.5.46  
- **Impacto:** 🟢 **MÍNIMO** — apenas *patch version*

---

## Plano de Atualização Recomendado

### 🟢 Fase 1: Atualizações Seguras (Baixo Risco)

**NPM (patch versions):**
- `@types/react`
- `@types/react-dom`
- Cypress
- Prettier
- Sass
- Webpack CLI

**Composer (patch versions):**
- PHPUnit 11.5.46
- PHPStan 2.1.33
- Slim Framework (patches)

---

### 🟠 Fase 2: Atualizações Médias (Risco Controlado)

**Symfony Components:**
- Testar compatibilidade com Symfony 7.x
- Atualizar de forma gradual

**Monolog 3.x:**
- Verificar compatibilidade com código existente
- Testar configurações de logging

---

### 🔴 Fase 3: Grandes Atualizações (Alto Risco)

**Bootstrap 5:**
- Requer migração planejada
- Substituição de dependências jQuery
- Atualização de classes CSS em todo o projeto
- Testes completos de UI

**AdminLTE 4:**
- Depende diretamente da migração para Bootstrap 5

**Benefícios:**
- Melhor acessibilidade
- Modernização geral
- Melhor performance

---

## Riscos e Considerações

### Bootstrap 5 — Desafios
- ❌ Remoção do jQuery (uso extensivo no projeto)
- ❌ Mudanças em CSS (múltiplos arquivos afetados)
- ❌ Necessidade de testes extensivos

---

### Symfony 7 — Desafios
- ❌ Possíveis *breaking changes*
- ✅ PHP 8.2+ já atendido pelo projeto

---

## Recomendação Final

Comece pelas atualizações de **baixo risco (Fase 1)** para manter o projeto seguro e estável.

A migração para **Bootstrap 5** deve ser tratada como um **projeto separado**, devido ao alto impacto, mas trará benefícios significativos a longo prazo:

- Remoção do jQuery
- Melhor performance
- Acessibilidade aprimorada

**🎯 Prioridade imediata:**  
Atualizações de segurança e *patch versions* para manter o sistema estável e seguro.
