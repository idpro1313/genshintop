# GenshinTop: nginx + PHP-FPM + supervisord (как dandangers). Без Node в runtime.
# Сборка из корня репозитория: docker build -f docker/Dockerfile .
# syntax=docker/dockerfile:1

FROM php:8.2-fpm-alpine

RUN apk add --no-cache nginx supervisor \
  && mkdir -p /var/log/supervisor /run/nginx /etc/nginx/snippets \
  && sed -i 's|listen = 9000|listen = 127.0.0.1:9000|' /usr/local/etc/php-fpm.d/www.conf

COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
COPY docker/nginx-default.conf /etc/nginx/http.d/default.conf

RUN chmod +x /usr/local/bin/docker-entrypoint.sh

WORKDIR /var/www

# Рантайм читает content/; legacy info/ не копируется в образ.
COPY VERSION ./VERSION
COPY bin ./bin/
COPY lib ./lib/
COPY content ./content/
COPY public ./public/

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
