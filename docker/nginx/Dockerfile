FROM nginx:1.11

WORKDIR /var/www/html

COPY ./docker/nginx/conf.d/php_upstream.conf /etc/nginx/conf.d/php_upstream.conf
COPY ./docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

RUN mkdir -p /var/www/html/public

EXPOSE 80