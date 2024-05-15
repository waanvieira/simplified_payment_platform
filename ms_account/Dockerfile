FROM php:8.2-fpm-alpine3.19

RUN apk add --no-cache openssl bash mysql-client nodejs npm alpine-sdk autoconf librdkafka-dev vim nginx openrc
RUN mkdir -p /run/nginx && \
    echo "pid /run/nginx.pid;" >> /etc/nginx/nginx.conf

RUN apk add --no-cache -X http://dl-cdn.alpinelinux.org/alpine/edge/main mpdecimal-dev

RUN docker-php-ext-install pdo pdo_mysql bcmath
RUN pecl install rdkafka

RUN ln -s /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini && \
    echo "extension=rdkafka.so" >> /usr/local/etc/php/php.ini

#  Add following lines to php docker file
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS
RUN apk add --update linux-headers
RUN pecl install xdebug

ARG MPDECIMAL_VERSION=2.5.1

RUN set -eux; \
	cd /tmp/; \
		curl -sSL -O https://www.bytereef.org/software/mpdecimal/releases/mpdecimal-${MPDECIMAL_VERSION}.tar.gz; \
		tar -xzf mpdecimal-${MPDECIMAL_VERSION}.tar.gz; \
			cd mpdecimal-${MPDECIMAL_VERSION}; \
			./configure; \
			make; \
			make install

RUN pecl install decimal
RUN echo "extension=decimal" >> /usr/local/etc/php/php.ini

RUN docker-php-ext-enable xdebug
RUN apk del -f .build-deps
RUN docker-php-ext-install sockets

# Configure Xdebug
RUN echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.log=/var/www/html/xdebug/xdebug.log" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/xdebug.ini
    #\ && echo "xdebug.client_port=9008" >> /usr/local/etc/php/conf.d/xdebug.ini

ENV DOCKERIZE_VERSION v0.6.1
RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && rm dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz

WORKDIR /var/www

RUN rm -rf /var/www/html

RUN ln -s public html

COPY .docker/entrypoint.sh /var/www/entrypoint.sh

RUN chmod +x entrypoint.sh

EXPOSE 9000

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# CMD ["sh" ".entrypoint.sh"]
