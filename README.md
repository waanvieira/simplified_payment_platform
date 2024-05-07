<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>


## Como rodar o projeto

Copiar o arquivo .env-example a pasta raiz e renomea-lo para .env

Criar uma rede docker 

docker network create simplified-payment-network

Depois da rede criada rodar o comando

docker-compose up -d

Acessar

http://localhost:9003/

Usuário admin padrão

email: useradmin@dev.com

Rodar teste do PHPUNIT

docker-compose exec app php -d xdebug.mode=coverage vendor/bin/phpunit --coverage-clover='reports/coverage/coverage.xml' --coverage-html='reports/coverage'

Rodar teste do Laravel

php artisan test

## Libs usadas

Lib RabbitMQ

https://github.com/php-amqplib/php-amqplib

