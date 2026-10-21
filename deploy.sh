#!/bin/sh
set -e

cd "$(dirname "$0")"

COMPOSE="docker compose -f docker-compose.prod.yml --env-file .env.production"

if [ ! -f .env.production ]; then
    cp .env.production.example .env.production

    sed -i "s|^APP_KEY=.*|APP_KEY=base64:$(openssl rand -base64 32)|" .env.production
    sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=$(openssl rand -hex 16)|" .env.production
    sed -i "s|^DB_ROOT_PASSWORD=.*|DB_ROOT_PASSWORD=$(openssl rand -hex 16)|" .env.production
    sed -i "s|^INVITATION_HOST_PASSWORD=.*|INVITATION_HOST_PASSWORD=$(openssl rand -hex 6)|" .env.production

    echo "Создан .env.production с сгенерированными секретами."
    echo "Откройте его (nano .env.production), укажите APP_URL (https://домен), DOMAIN (домен без https://) и при желании свой INVITATION_HOST_PASSWORD,"
    echo "затем запустите ./deploy.sh снова."
    exit 0
fi

if grep -q 'CHANGE-ME' .env.production; then
    echo "В .env.production не заполнены APP_URL и DOMAIN (осталось CHANGE-ME). Поправьте файл и запустите снова."
    exit 1
fi

if [ ! -f certs/fullchain.pem ] || [ ! -f certs/privkey.pem ]; then
    echo "Нет сертификата: положите certs/fullchain.pem и certs/privkey.pem и запустите снова."
    exit 1
fi

$COMPOSE up -d --build
$COMPOSE ps
