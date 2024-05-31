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

# Executar testes

docker-compose exec app_account vendor/bin/phpunit

# Executar o consumer do ms_account
docker-compose exec app_account php artisan rabbitmq:consumer

# Executar o consumer do ms_transaction
docker-compose exec app_transaction php artisan rabbitmq:consumer

# Executar o consumer do ms_notification
docker-compose exec app_notification php artisan rabbitmq:consumer

```

# Uso do sistema

* Checar se os endpoins estão no ar <br>
  http://localhost:8081/health <br>
  http://localhost:8082/health <br>
  http://localhost:8083/health <br>

* Criar conta
curl  -X POST 'http://localhost:8001/api/accounts' \
  --header 'Accept: application/json' \
  {
    "name": "user test",
    "cpf_cnpj": "153.267.740-54",
    "email": "email@dev.teste",
    "password": "123456"
  }

* Realizar transferência

Para reallizar transferências é obrigatório que tenha criado contas válidas e com saldo positivo para realizar a transferência

curl  -X POST 'http://localhost:8001/api/transfer' \
  --header 'Accept: application/json' \
  {
    "payer_id": "7e0b17a3-2f57-4273-a48a-9f81ed2958eb",
    "payee_id": "8afd4fc5-5989-4b69-a799-1a0e2866235d",
    "value": 10
  }

# Autor

Wanderson Alves Vieira

https://www.linkedin.com/in/wanderson-alves-vieira-59b832148
