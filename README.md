# WEBSTER 

Cette application est une sorte de **librairie de tutoriels** en lien avec la programmation informatique. 

## The team 

- **Maxime RENAUD**  [Github](https://github.com/max1300)
- **Damien TERRO** [Github](https://github.com/Damdam9388) - [LinkedIn](https://www.linkedin.com/in/damien-terro-945a08bb/)
- **Safia MSELLEK ISMAILI** [Github](https://github.com/Safiamoon) - [LinkedIn](https://www.linkedin.com/in/safia-msellek-ismaili-21a743176/)

## Technologies

- **Symfony** as a backend Framework
- **React.js** as a Frontend Framework
- **HTML 5**
- **CSS 3**
- **Bootstrap** for styling frontend
- **Chakra** UI design system
- **phpMyAdmin** for the database

## The project

The projet has been developed as part of the web developement training program at [Le Cnam - House Of Code] remotely, starting from 27th April 2020 to 23rd September 2020. 

This application is intended primarily for students of the House Of Code program, but its usefulness can be extended to the majority of learners in the field of computer programming.

## Features

* Choice between Frameworks
* Choice between programming Languages
* The possiblity to add a resource for a framework or a programming language

## Hosting

*  VPS OVH

## API used

- API Plateform

## Setup instructions

Before continuing, make sure to:

* install PHP
* install composer and symfony
* launch WAMP / MAMP / LAMP

#### Clone this repo 
``` git clone https://github.com/max1300/web_library.git```

#### Run Composer install
```compose install```

#### Create a .env.local file with the followings :

**DATABASE_URL**=mysql://user-name:password@127.0.0.1:3306/DBName?serverVersion=5.7
**JWT_PASSPHRASE**=*****
**MAILER_DSN**=gmail://emailAdress:password@default
**ADMIN_EMAIL**=emailAdress

#### Generate the JWT passphrase:

- ```mkdir -p config / jwt```

- Private key: 

```openssl genpkey -out config / jwt / private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits: 4096```

- Public key:

```openssl pkey -in config / jwt / private.pem -out config / jwt / public.pem -pubout```


#### Database

```php bin/console doctrine: migrations: migrate```

```php bin/console doctrine: fixtures: load```

#### Start the server with symfony

```symfony server: start```

**PS : To use the application fully, don't forget the front-end part  [Front part of the application](https://github.com/Damdam9388/web_library_front)**
