# Projeto Telegram Bot Laravel

Este projeto é um Bot do Telegram baseado em Laravel com gerenciamento de fluxo VIP, utilizando filas para processamento de mensagens e Telescope para debug.

---

## Requisitos

* Docker & Docker Compose
* Conta Telegram e Token do Bot

> Observação: Todo o ambiente Laravel é iniciado automaticamente via Docker.

---

## Instalação e Inicialização

1. Clonar o repositório:

```bash
git clone https://github.com/pedro-henrique-goncalves/telegram-bot.git
cd telegram-bot
```

2. Copiar `.env.example` para `.env`:

```bash
cp .env.example .env
```

3. Subir os containers Docker:

```bash
docker-compose up -d --build
```

4. Entrar no container PHP-FPM para rodar os comandos seguintes:

```bash
docker exec -it telegram-bot-php-fpm bash
```

5. Dentro do container, instalar dependências PHP via Composer:

```bash
composer install
```

6. Rodar migrations:

```bash
php artisan migrate
```

7. Gerar a chave da aplicação:

```bash
php artisan key:generate
```

---

## Configuração do Docker

O Docker já está configurado para iniciar o Laravel automaticamente, não sendo necessário rodar `artisan serve`.

* **Webserver:** Nginx expondo a porta 80.
* **PHP-FPM:** Container PHP com todas as dependências do Laravel.
* **MySQL:** Banco de dados persistente.
* **Redis:** Cache e estado temporário do fluxo do usuário.

---

## Configuração do Ambiente

No arquivo `.env`, configure:

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=telegram-bot
DB_USERNAME=telegram-bot
DB_PASSWORD=telegram-bot

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

TOKEN_TELEGRAM_BOT=SEU_TOKEN_DO_BOT
TELEGRAM_API_URL=https://api.telegram.org/bot
```

---

## Configuração do Bot do Telegram

1. Crie um bot via **BotFather**.
2. Copie o token e cole no `.env` em `TELEGRAM_BOT_TOKEN`.
3. Configure o webhook:

### Opções para configurar webhook

* **Via cURL:**

```bash
curl -F "url=<SEU_WEBHOOK_URL>/api/telegram/webhook" https://api.telegram.org/bot<SEU_TOKEN>/setWebhook
```

* **Via navegador:**
  Abra a URL:

```
https://api.telegram.org/bot<SEU_TOKEN>/setWebhook?url=<SEU_WEBHOOK_URL>/api/telegram/webhook
```

> Certifique-se que o webhook é público (ngrok ou domínio válido).

---

## Executando o Projeto

1. O Laravel já está rodando via Docker na porta 80.
2. Acesse via navegador ou teste o webhook diretamente com Telegram.

---

## Permissões

Se ocorrerem erros relacionados a permissão nas pastas `storage` ou `bootstrap/cache`, o Laravel pode não conseguir gravar arquivos necessários para logs, cache ou sessões.

1. Para resolver esses erros basta rodar o comando abaixo

```bash
chown -R www-data:www-data storage bootstrap/cache
```

## Filas e Jobs

1. Iniciar worker da fila (dentro do container PHP-FPM):

```bash
docker exec -it telegram-bot-php-fpm php artisan queue:work
```

2. Para teste rápido, use fila síncrona no `.env`:

```env
QUEUE_CONNECTION=sync
```

---

## Debug com Telescope

1. Acessar Telescope:

```
http://localhost/telescope
```

## Debug com Horizon 

1. Iniciar Horizon

```bash
docker exec -it telegram-bot-php-fpm php artisan horizon
```

2. Acessar Horizon:

```
http://localhost/horizon
```
---

## Comandos Artisan

* Limpar cache:

```bash
php artisan cache:clear
```

* Rodar migrations:

```bash
php artisan migrate
```

* Reiniciar workers da fila:

```bash
php artisan queue:restart
```

* Gerar chave da aplicação:

```bash
php artisan key:generate
```
---

## Observações

* Redis é usado para controlar o fluxo e estado temporário do usuário.
* Banco de dados armazena dados permanentes do usuário (VIP, email, histórico).
* Horizon permite monitorar o estado das filas e jobs em tempo real, facilitando debug e gestão do processamento assíncrono.
* Certifique-se que os workers da fila estão rodando para processar mensagens.
* O webhook deve ser acessível publicamente (ngrok ou domínio).

---
