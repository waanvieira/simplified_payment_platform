FROM php:8.1-fpm-alpine3.19
RUN apk add --no-cache openssl bash mysql-client nodejs npm alpine-sdk autoconf librdkafka-dev vim nginx openrc
RUN mkdir -p /run/nginx && \
    echo "pid /run/nginx.pid;" >> /etc/nginx/nginx.conf

RUN docker-php-ext-install pdo pdo_mysql bcmath
RUN pecl install rdkafka

RUN ln -s /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini && \
    echo "extension=rdkafka.so" >> /usr/local/etc/php/php.ini

#  Add following lines to php docker file
# RUN pecl install xdebug
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS
RUN apk add --update linux-headers
RUN pecl install xdebug-3.1.5
RUN docker-php-ext-enable xdebug
RUN apk del -f .build-deps

# Configure Xdebug
RUN echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.log=/var/www/html/xdebug/xdebug.log" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/xdebug.ini
    #\ && echo "xdebug.client_port=9008" >> /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /var/www

#Deletando a pasta HTML que é criada por padrão
RUN rm -rf /var/www/html
#Criando link simbolico
RUN ln -s public html
#Instalando COMPOSER
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#Copiando o entrypoint para a pasta /
COPY .docker/entrypoint.sh /entrypoint.sh

# RUN chmod -R 777 /entrypoint.sh

EXPOSE 9000
# CMD ["sh" ".entrypoint.sh"]
