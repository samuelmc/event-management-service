# **Installation**

## **Server**
Setup a basic LAMP server on a VPS, VM or any locally installed Linux Distribution or other UNIX-like system

**Requirements**
- Apache >= 2.4
    - required mods:
        - mod_php7.4
        - mod_rewrite

- PHP >= 7.4
    - required extensions
        - Ctype
        - Iconv
        - JSON
        - PCRE
        - Session
        - SimpleXML
        - Tokenizer
        - PDO

- MySQL or MariaDB
    - user with create database permission
    
- Composer

To check your installations you can use the [symfony CLI](https://symfony.com/download):

`symfony check:requirements`

## **Application**

Clone the repository onto the server (eg. /var/www/event-management-service )

`cd` into the project 

run `composer install`

setup the database connection in .env or .env.test:

`DATABASE_URL=mysql://<user>:<password>@127.0.0.1:3306/event-management-service?serverVersion=5.7`

create the database, run migrations & load fixtures

`php bin/conosle doctrine:database:create`

`php bin/console doctrine:migrations:migrate`

`php bin/console doctrine:fixtures:load`

Setup an Apache VirtualHost with <project location>/public as DocumentRoot and AllowOverride All