# Сборка статики Astro и раздача через nginx (совместимо с Traefik на 80 внутри контейнера)
# syntax=docker/dockerfile:1

FROM node:22-alpine AS builder
WORKDIR /app
ENV npm_config_update_notifier=false

COPY package.json package-lock.json* ./
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi

COPY . .
RUN npm run build

FROM nginx:1.27-alpine
COPY deploy/nginx-docker.conf /etc/nginx/conf.d/default.conf
COPY --from=builder /app/dist /usr/share/nginx/html
EXPOSE 80
