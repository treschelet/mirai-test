FROM nginx:1.19.2-alpine

ENV APP_ROOT /var/www/html
ENV APP_INDEX index.php
ENV FASTCGI_HOST 127.0.0.1
ENV FASTCGI_PORT 9000

ADD nginx.conf /etc/nginx/nginx.template

CMD envsubst '$$APP_ROOT $$APP_INDEX $$FASTCGI_HOST $$FASTCGI_PORT' < /etc/nginx/nginx.template > /etc/nginx/nginx.conf && exec nginx -g 'daemon off;'