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

# Каталог content/ не копируется: в git не версионируется; рантайм читает только info/.
COPY lib ./lib/
COPY info ./info/
COPY public ./public/
# Не класть в conf.d: в Alpine nginx туда подключается http{}, а rewrite допустим только в server/location.
COPY docker/genshintop-redirects.conf /etc/nginx/snippets/genshintop-redirects.conf

RUN php lib/build-sitemap.php

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
