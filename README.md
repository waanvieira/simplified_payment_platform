# Sistema simplificado de transferências 
<p>
<a href="https://github.com/waanvieira/simplified_payment_platform/blob/main/LICENSE"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Sobre o projeto
Projeto simplificado de transferências entre contas que utiliza o padrão Saga, sistema distribuido com comunicação assíncrona entre os sistemas fila (RabbitMQ).

# Tecnologias utilizadas
- PHP 8.2
- Laravel 11.7.0
- MYSQL 8
- RabbitMQ

## Implantação em produção
- Back end: Heroku
- Front end web: Netlify
- Banco de dados: Postgresql

# Como executar o projeto

## Pré-requisitos
Docker
https://www.docker.com/get-started/

```bash
# clonar repositório
git clone https://github.com/waanvieira/simplified_payment_platform.git

# entrar na pasta do projeto back end
cd simplified_payment_platform

# executar o projeto
docker-compose up -d

# Executar o consumer do ms_account
docker-compose exec app_account php artisan rabbitmq:consumer

# Executar o consumer do ms_transaction
docker-compose exec app_transaction php artisan rabbitmq:consumer

# Executar o consumer do ms_notification
docker-compose exec app_notification php artisan rabbitmq:consumer

```

# Uso do sistema

* Checar se os endpoins estão no ar

* 
# Autor

Wanderson Alves Vieira

https://www.linkedin.com/in/wanderson-alves-vieira-59b832148
