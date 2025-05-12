FROM php:8.3.21-cli-alpine3.20

RUN apk update && apk add openssh
RUN apk add sshpass

RUN curl -s http://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/ \
    && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

ADD . /app
WORKDIR /app
RUN composer install --no-dev
#RUN chmod +x /app/entrypoint.sh
#ENTRYPOINT ["/app/entrypoint.sh"]
## CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
CMD ["top", "-b"]