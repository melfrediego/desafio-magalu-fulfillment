
# Configurando e Criando Testes Unitários e de Integração no Laravel 10

[![LinkedIn](https://img.shields.io/badge/LinkedIn-Melfre%20Diego-blue?logo=linkedin)](https://www.linkedin.com/in/melfre-diego/)

## **Sobre o Projeto**

Este projeto refere-se a um **Desafio Técnico para Magalu Fulfillment**, desenvolvido por **Melfre Diego** (Desenvolvedor FullStack Sênior).

---

## **Configuração do Projeto**

### **1. Instale o Laravel 10**
Certifique-se de que o Composer está instalado e crie um novo projeto Laravel:

```bash
composer create-project laravel/laravel nome-do-projeto
```

### **2. Configure o Banco de Dados**
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

### **3. Execute as Migrations**
Crie as tabelas no banco de dados configurado:

```bash
php artisan migrate
```

---

## **Configuração do Ambiente de Testes**

### **1. Crie o Arquivo `.env.testing`**
Na raiz do projeto, crie um arquivo chamado `.env.testing` com o seguinte conteúdo:

```dotenv
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

### **2. Execute as Migrations no Ambiente de Testes**
Certifique-se de que as tabelas estão configuradas para o ambiente de testes:

```bash
php artisan migrate --env=testing
```

---

## **Gerando Testes**

### **1. Criar Testes Unitários**
Crie os arquivos para os testes unitários usando os comandos abaixo:

```bash
php artisan make:test BankTest --unit
php artisan make:test AccountTest --unit
php artisan make:test UserTest --unit
```

### **2. Criar Testes de Integração**
Crie os arquivos para os testes de integração com os comandos abaixo:

```bash
php artisan make:test BankIntegrationTest
php artisan make:test AccountIntegrationTest
php artisan make:test UserIntegrationTest
```

---

## **Rodando Seeders**

### **1. Executar Seeders Específicos**
Para rodar a seeder de `Bank`, use o comando:

```bash
php artisan db:seed --class=BankSeeder
```

### **2. Executar Todos os Seeders**
Caso deseje executar todas as seeders de uma vez, utilize o comando:

```bash
php artisan db:seed
```

### **Nota:**
Certifique-se de que o banco de dados está configurado corretamente no arquivo `.env` antes de rodar as seeders.

---

## **Executando os Testes**

Para rodar todos os testes criados no projeto, use o comando:

```bash
php artisan test
```

---

## **Observações**
- O ambiente de testes utiliza o banco de dados SQLite em memória para otimizar a execução.
- Sempre garanta que as migrations estão atualizadas tanto para o ambiente de desenvolvimento quanto para o ambiente de testes antes de executar os testes.

Com este guia, você pode configurar e rodar sua aplicação Laravel 10, criar testes unitários e de integração, e rodar seeders de forma eficiente.
