FROM composer:2.3.4
COPY backend /var/app/backend
WORKDIR /var/app/backend
RUN composer install --optimize-autoloader --no-dev

FROM node:14.18.3-alpine AS release
COPY --from=0 /var/app/backend /var/app/backend
COPY frontend /var/app/frontend
COPY var /var/app/var
COPY cli /var/app/cli
WORKDIR /var/app/frontend
ENV RELEASE_FILENAME=supla_icons_webapp.tar.gz
RUN npm install -g npm@7 && npm install && npm run release

FROM scratch
COPY --from=release /var/app/supla_icons_webapp.tar.gz .

