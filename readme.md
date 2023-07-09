## Perform following steps after clone

- ## install **Composer** with following command
    - to add required autoload file
    - run command: **composer install**

- ## copy **.env** file from **.env.example**
    - 1.Copy .env.example to .env:
    - run command: **cp -a .env.example .env**
    - 2.Generate a key:
    - run command: **php artisan key:generate**
    - then run command: **php artisan config:cache**
    - 3.Only then run:
    - run command: **php artisan serve**

- ## add dataBase in **phpMyAdmin**
    - [Open PhpMyAdmin in Browser](http://localhost/phpmyadmin/).
    - add dataBase with same **name** added in **.env** file

- ## perform **Migration** to create DataBase Tables
    - run migration command - **php artisan migrate**

- ## create **Passport Key** by run following command
    - run: **php artisan passport:key**
    - this command will create Key for passport
    - this command added after notice error while deploy on live server

- ## initialize **Passport Client** by run following command
    - run: **php artisan passport:client --personal**
    - and create passport client
