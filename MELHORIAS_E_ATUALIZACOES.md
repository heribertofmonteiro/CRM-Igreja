# 🚀 Melhorias e Atualizações Sugeridas para o Projeto ChurchCRM

Este documento contém sugestões de melhorias e atualizações que podem ser aplicadas ao projeto **sem remover funcionalidades existentes**.

---

## 📦 1. Atualizações de Dependências

### 1.1 Dependências PHP (Composer)

**Pacotes que podem ser atualizados para versões mais recentes compatíveis:**

```json
{
  "require": {
    "php": ">=8.2",  // ✅ Mantém compatibilidade PHP 8.2+
    
    // Atualizações sugeridas:
    "monolog/monolog": "^3.0.0",        // ↑ v2.10.0 → v3.x (PHP 8.1+)
    "phpmailer/phpmailer": "^7.0.0",    // ↑ v6.9.1 → v7.x (mais seguro)
    "twig/twig": "^4.0.0",             // ↑ v3.20.0 → v4.x (melhor performance)
    "symfony/translation": "^6.4.0",    // ↑ v5.4.35 → v6.4.x (mais recente)
    
    // Manter versões atuais (estáveis):
    "slim/slim": "^4.15.0",            // ✅ Versão atual estável
    "defuse/php-encryption": "^2.4.0", // ✅ Versão atual adequada
    "pragmarx/google2fa": "^8.0.1"     // ✅ Versão atual adequada
  }
}
```

**⚠️ Atenção:** Testar cada atualização isoladamente antes de aplicar em produção.

### 1.2 Dependências JavaScript (npm)

**Pacotes que podem ser atualizados:**

```json
{
  "dependencies": {
    // Atualizações sugeridas:
    "bootstrap": "^5.3.3",              // ↑ v4.6.2 → v5.x (considerar migração gradual)
    "jquery": "^3.7.1",                // ✅ Versão atual já é recente
    "chart.js": "^4.5.0",              // ✅ Versão atual adequada
    "react": "^19.2.0",                // ✅ Versão atual já é muito recente
    
    // Considerar atualização futura:
    "@types/react": "^19.x",           // ↑ v18.3.18 → v19.x (alinhar com React)
    "@types/react-dom": "^19.x"        // ↑ v18.3.5 → v19.x
  },
  "devDependencies": {
    "webpack": "^5.97.1",              // ✅ Versão atual adequada
    "typescript": "^5.7.2",            // ✅ Versão atual adequada
    "prettier": "^3.6.2"               // ✅ Versão atual adequada
  }
}
```

**⚠️ Atenção:** Bootstrap 5 tem breaking changes. Considerar migração gradual ou manter Bootstrap 4 por enquanto.

---

## 🔒 2. Melhorias de Segurança

### 2.1 Headers de Segurança Adicionais

**Arquivo:** `src/Include/Header-Security.php`

Adicionar headers adicionais:

```php
// Headers adicionais sugeridos:
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN'); // ou DENY para máximo bloqueio
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' \'unsafe-eval\'; style-src \'self\' \'unsafe-inline\';');
```

### 2.2 Validação de Entrada Aprimorada

Considerar adicionar sanitização adicional em pontos críticos:
- Validação de uploads de arquivo (verificar MIME types reais, não apenas extensão)
- Sanitização de dados antes de inserção no banco (Propel já ajuda, mas validação extra é sempre bom)
- Validação de CSRF tokens em todas as operações de escrita

### 2.3 Configuração PHP de Segurança

**Arquivo:** `docker/Dockerfile.churchcrm-apache-php8`

Adicionar configurações de segurança no PHP:

```dockerfile
# Adicionar no Dockerfile após linha 38:
RUN sed -i 's/^expose_php.*$/expose_php = Off/g' $PHP_INI_DIR/php.ini && \
    sed -i 's/^session.cookie_httponly.*$/session.cookie_httponly = 1/g' $PHP_INI_DIR/php.ini && \
    sed -i 's/^session.cookie_secure.*$/session.cookie_secure = 1/g' $PHP_INI_DIR/php.ini && \
    sed -i 's/^session.use_strict_mode.*$/session.use_strict_mode = 1/g' $PHP_INI_DIR/php.ini
```

**⚠️ Atenção:** `session.cookie_secure = 1` requer HTTPS. Ajustar conforme ambiente.

---

## ⚡ 3. Melhorias de Performance

### 3.1 Cache de OpCode PHP

**Arquivo:** `docker/Dockerfile.churchcrm-apache-php8`

Adicionar OPcache para melhor performance:

```dockerfile
# Adicionar após linha 27:
RUN docker-php-ext-install -j$(nproc) opcache

# Adicionar configuração do OPcache (após linha 38):
RUN echo '[opcache]' >> $PHP_INI_DIR/php.ini && \
    echo 'opcache.enable=1' >> $PHP_INI_DIR/php.ini && \
    echo 'opcache.memory_consumption=256' >> $PHP_INI_DIR/php.ini && \
    echo 'opcache.max_accelerated_files=20000' >> $PHP_INI_DIR/php.ini && \
    echo 'opcache.validate_timestamps=0' >> $PHP_INI_DIR/php.ini
```

### 3.2 Compressão Gzip/Brotli

**Arquivo:** `docker/apache/default.conf`

Adicionar compressão para reduzir tamanho de resposta:

```apache
# Adicionar módulos de compressão:
LoadModule deflate_module modules/mod_deflate.so
LoadModule brotli_module modules/mod_brotli.so

# Adicionar compressão:
<Location />
    SetOutputFilter DEFLATE
    SetEnvIfNoCase Request_URI \
        \.(?:gif|jpe?g|png|ico|zip|gz|bz2|pdf)$ no-gzip dont-vary
</Location>
```

### 3.3 Cache de Navegador

Adicionar cache estático mais agressivo:

**Arquivo:** `docker/apache/default.conf`

```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

### 3.4 Webpack - Modo de Produção

**Arquivo:** `webpack.config.js`

Adicionar modo de produção otimizado:

```javascript
module.exports = (env, argv) => {
  const isProduction = argv.mode === 'production';
  
  return {
    mode: isProduction ? 'production' : 'development',
    // ... configurações existentes ...
    
    optimization: {
      minimize: isProduction,
      // Adicionar tree shaking e outras otimizações
    },
    
    plugins: [
      // ... plugins existentes ...
      ...(isProduction ? [
        new webpack.optimize.ModuleConcatenationPlugin()
      ] : [])
    ]
  };
};
```

---

## 🐳 4. Melhorias no Docker

### 4.1 Multi-stage Build Otimizado

**Arquivo:** `docker/Dockerfile.churchcrm-apache-php8`

Otimizar camadas do Docker:

```dockerfile
# Combinar RUN commands para reduzir camadas:
RUN apt-get update && \
    apt-get install -y \
        libxml2-dev \
        gettext \
        locales \
        locales-all \
        libpng-dev \
        libzip-dev \
        libfreetype6-dev \
        libjpeg-dev \
        git \
    && docker-php-ext-install -j$(nproc) xml exif pdo_mysql gettext iconv mysqli zip opcache \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean
```

### 4.2 Healthcheck para Web Server

**Arquivo:** `docker/docker-compose.yaml`

Adicionar healthcheck para webserver:

```yaml
webserver-dev:
  # ... configurações existentes ...
  healthcheck:
    test: ["CMD", "curl", "-f", "http://localhost/"]
    interval: 30s
    timeout: 10s
    retries: 3
    start_period: 40s
```

### 4.3 Variáveis de Ambiente para Configuração

**Arquivo:** `docker/docker-compose.yaml`

Tornar mais configurável via .env:

```yaml
services:
  webserver-dev:
    environment:
      - PHP_MEMORY_LIMIT=${PHP_MEMORY_LIMIT:-512M}
      - PHP_MAX_EXECUTION_TIME=${PHP_MAX_EXECUTION_TIME:-120}
      - APACHE_SERVER_NAME=${APACHE_SERVER_NAME:-localhost}
```

---

## 🧹 5. Melhorias de Qualidade de Código

### 5.1 Arquivo `.editorconfig` - Adicionar TypeScript

**Arquivo:** `.editorconfig`

```ini
[*.{ts,tsx}]
indent_size = 2  # TypeScript geralmente usa 2 espaços
```

### 5.2 PHPStan - Análise Estática

**Arquivo:** `phpstan.neon` (criar se não existir)

```yaml
parameters:
    level: 5  # Começar com nível 5, aumentar gradualmente
    paths:
        - src
    excludePaths:
        - src/vendor
        - src/ChurchCRM/model
```

Adicionar script no `package.json`:

```json
{
  "scripts": {
    "phpstan": "cd src && vendor/bin/phpstan analyse"
  }
}
```

### 5.3 Prettier - Configuração TypeScript

**Arquivo:** `.prettierrc` (criar se não existir)

```json
{
  "semi": true,
  "singleQuote": true,
  "tabWidth": 2,
  "trailingComma": "es5",
  "printWidth": 100
}
```

---

## 📊 6. Monitoramento e Logging

### 6.1 Estruturado Logging

Já usa Monolog, mas considerar adicionar contexto estruturado:

```php
// Exemplo de melhoria:
$logger->info('User login', [
    'user_id' => $user->getId(),
    'ip_address' => $_SERVER['REMOTE_ADDR'],
    'user_agent' => $_SERVER['HTTP_USER_AGENT']
]);
```

### 6.2 Métricas de Performance

Considerar adicionar middleware para medir tempos de resposta:

```php
// Middleware para medir tempo de resposta
$app->add(function ($request, $handler) {
    $start = microtime(true);
    $response = $handler->handle($request);
    $duration = microtime(true) - $start;
    
    return $response->withHeader('X-Response-Time', sprintf('%.3f', $duration));
});
```

---

## 🔧 7. Configurações Adicionais

### 7.1 Arquivo `.dockerignore` - Melhorias

Já está bem configurado, mas considerar adicionar:

```dockerignore
# Adicionar:
../src/logs/**/*
../src/tmp_attach/**
../.env*
../*.md
../demo/Images/**
```

### 7.2 Scripts NPM Adicionais

**Arquivo:** `package.json`

```json
{
  "scripts": {
    // Scripts adicionais sugeridos:
    "security:check": "npm audit && cd src && composer audit",
    "qa": "npm run phpstan && npm run lint",
    "build:prod": "NODE_ENV=production npm run build",
    "docker:dev:restart": "npm run docker:dev:stop && npm run docker:dev:start"
  }
}
```

### 7.3 Variáveis de Ambiente para Desenvolvimento

**Arquivo:** `.env.example` (criar como exemplo)

```env
# Docker Environment Variables
DATABASE_PORT=3306
WEBSERVER_PORT=80
ADMINER_PORT=8088
MAILSERVER_PORT=1025
MAILSERVER_GUI_PORT=8025

# PHP Configuration
PHP_MEMORY_LIMIT=512M
PHP_MAX_EXECUTION_TIME=120

# Application Configuration
APP_ENV=development
APP_DEBUG=true
```

---

## 📝 8. Documentação

### 8.1 README de Desenvolvimento

Criar `DEVELOPMENT.md` com:
- Guia de setup local
- Comandos úteis
- Convenções de código
- Processo de contribuição

### 8.2 Comentários de Código

Adicionar PHPDoc mais completo em classes públicas:
- Exemplos de uso
- Parâmetros e retornos detalhados
- Exceções possíveis

---

## ✅ Checklist de Implementação

### Prioridade Alta (Segurança e Performance)
- [ ] Atualizar dependências com vulnerabilidades conhecidas
- [ ] Adicionar headers de segurança adicionais
- [ ] Habilitar OPcache no Docker
- [ ] Configurar compressão Gzip/Brotli

### Prioridade Média (Melhorias Gerais)
- [ ] Atualizar Monolog para v3
- [ ] Adicionar PHPStan com nível gradual
- [ ] Melhorar logging estruturado
- [ ] Adicionar healthcheck no docker-compose

### Prioridade Baixa (Refinamentos)
- [ ] Atualizar documentação
- [ ] Adicionar scripts NPM úteis
- [ ] Melhorar comentários PHPDoc
- [ ] Considerar migração gradual para Bootstrap 5

---

## ⚠️ Avisos Importantes

1. **Testar Cada Mudança:** Sempre testar em ambiente de desenvolvimento antes de produção
2. **Backup:** Fazer backup antes de atualizações significativas
3. **Versionamento:** Usar controle de versão adequado (Git)
4. **Monitoramento:** Após implementações, monitorar logs e performance
5. **Breaking Changes:** Algumas atualizações podem ter breaking changes - revisar changelogs

---

## 📚 Recursos Úteis

- [Composer Update Guide](https://getcomposer.org/doc/01-basic-usage.md#updating-dependencies-to-their-latest-compatible-versions)
- [PHP 8.3 Migration Guide](https://www.php.net/manual/en/migration83.php)
- [Docker Best Practices](https://docs.docker.com/develop/dev-best-practices/)
- [OWASP Security Headers](https://owasp.org/www-project-secure-headers/)

---

**Última atualização:** 2025-01-XX
**Versão do Projeto:** 6.0.0

