
# Desafio Técnico para Magalu Fulfillment

[![LinkedIn](https://img.shields.io/badge/LinkedIn-Melfre%20Diego-blue?logo=linkedin)](https://www.linkedin.com/in/melfre-diego/)

Este projeto refere-se a um **Desafio Técnico para Magalu Fulfillment**, desenvolvido por **Melfre Diego** (Desenvolvedor FullStack Sênior).

---

## **Como Rodar o Projeto**

### **1. Clonar o Repositório**
Clone o repositório do projeto em sua máquina local:

```bash
git clone <URL_DO_REPOSITORIO>
cd nome-do-projeto
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
#### Rodar Seeder Específico
Para rodar a seeder de `Bank`, execute:

```bash
php artisan db:seed --class=BankSeeder
```

#### Rodar Todos os Seeders
Caso deseje executar todas as seeders de uma vez:

```bash
php artisan db:seed
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

Acesse o projeto em seu navegador através de:  
[http://localhost:8000](http://localhost:8000)

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

Com este guia, você pode clonar e rodar o projeto Laravel 10, configurar o banco de dados, rodar seeders, e executar testes de forma eficiente.
