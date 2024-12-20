
# Desafio Técnico para Magalu Fulfillment

[![LinkedIn](https://img.shields.io/badge/LinkedIn-Melfre%20Diego-blue?logo=linkedin)](https://www.linkedin.com/in/melfre-diego/)

Este projeto refere-se a um **Desafio Técnico para Magalu Fulfillment**, desenvolvido por **Melfre Diego** (Desenvolvedor FullStack Sênior).

---

## **Como Rodar o Projeto**

### **1. Clonar o Repositório**
Clone o repositório do projeto em sua máquina local:

```bash
git clone https://github.com/melfrediego/desafio-magalu-fulfillment
cd desafio-magalu-fulfillment
```

### **2. Instalar Dependências**
Certifique-se de que o Composer está instalado. Instale as dependências do projeto:

```bash
composer install
```

### **3. Configurar o Banco de Dados**
#### MySQL
No arquivo `.env`, configure as credenciais do banco de dados MySQL:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario
DB_PASSWORD=senha
```

#### PostgreSQL
Para PostgreSQL, configure o `.env` da seguinte maneira:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario
DB_PASSWORD=senha
```

### **4. Executar Migrations**
Crie as tabelas no banco de dados configurado:

```bash
php artisan migrate
```

### **5. Executar Seeders**

#### Rodar Todos os Seeders
Caso deseje executar todas as seeders de uma vez:

```bash
php artisan db:seed
```

---

#### Configurar filas processamento
Modifique ou adicione no arquivo `.env`:

```dotenv
QUEUE_CONNECTION=database
```

Em seguida execute os comando:

```bash
php artisan queue:table
php artisan migrate
```

Para processar os jobs na fila, inicie o worker de filas:

```bash
php artisan queue:work
```


---

## **Configuração do Ambiente de Testes**

### **1. Criar o Arquivo `.env.testing`**
Na raiz do projeto, crie um arquivo chamado `.env.testing` com o seguinte conteúdo:

```dotenv
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

```bash
cp .env .env.testing
php artisan key:generate --env=testing
```

### **2. Executar Migrations no Ambiente de Testes**
Certifique-se de que as tabelas estão configuradas para o ambiente de testes:

```bash
php artisan migrate --env=testing
```



---

## **Executando o Projeto**

Inicie o servidor do Laravel na porta padrão (8000):

```bash
php artisan serve
```
ou

```bash
php artisan serve --port xxxx
```

Acesse o projeto em seu navegador através de ou informe a porta correta ex: :8002, :8003.  
[http://localhost:8000](http://localhost:8000)


Acesse a documentação em seu navegador através de:  
[http://localhost:8000/api/documentation/](http://localhost:8000/api/documentation/)

---

## **Executando os Testes**

Para rodar todos os testes criados no projeto, use o comando:

```bash
php artisan test
```

```bash
php artisan test --testsuite=Unit
```

```bash
php artisan test --testsuite=Feature
```

---

#### **Executando os Testes Especificos**
Testes Unitários de Account:

```bash
php artisan test --filter AccountUnitTest
```

Testes de Integração de Account:

```bash
php artisan test --filter AccountIntegrationTest
```

Testes Unitários de Client:

```bash
php artisan test --filter ClientUnitTest
```

Testes de Integração de Client:

```bash
php artisan test --filter ClientIntegrationTest
```

Testes Unitários de User
```bash
php artisan test --filter UserUnitTest
```

Testes de Integração de User
```bash
php artisan test --filter UserIntegrationTest
```

Testes de Transações
```bash
php artisan test --filter TransactionTest
```

Testes de Transações (TransactionServiceTest)
```bash
php artisan test --filter test_deposit_updates_balance_correctly
```
---

## **Tecnologias Utilizadas**
 - Laravel 10: Framework PHP.
 - MySQL 8: Banco de dados relacional.
 - PostgreSQL 15: Banco de dados relacional.
 - Postman: Testes de API.

---

## **Melhorias Propostas**
 - Docker(Container).
 - Ajustes Test Unitarios
---

## **Observações**
- Sobre o ultimo item, existe um link para reprocesssar transações, no projeto consta o arquivo dos endpoints para POSTMAN.
- O ambiente de testes utiliza o banco de dados SQLite em memória para otimizar a execução.
- Sempre garanta que as migrations estão atualizadas tanto para o ambiente de desenvolvimento quanto para o ambiente de testes antes de executar os testes.

Com este guia, você pode clonar e rodar o projeto Laravel 10, configurar o banco de dados, rodar seeders, e executar testes de forma eficiente.
