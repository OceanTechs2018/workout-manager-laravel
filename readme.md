## Perform following steps after clone

- ## install **Composer** with following command
    - to add required autoload file
    - run command: [**composer install**]()

- ## copy **.env** file from **.env.example**
    - 1.Copy .env.example to .env:
    - run command: [**cp -a .env.example .env**]()
    - 2.Generate a key:
    - run command: [**php artisan key:generate**]()
    - then run command: [**php artisan config:cache**]()
    - 3.Only then run:
    - run command: [**php artisan serve**]()

- ## add dataBase in **phpMyAdmin**
    - [Open PhpMyAdmin in Browser](http://localhost/phpmyadmin/).
    - add dataBase with same **name** added in **.env** file

- ## perform **Migration** to create DataBase Tables
    - run migration command: [**php artisan migrate**]()

- ## create **Passport Key** by run following command
    - run Command: [**php artisan passport:key**]()
    - this command will create Key for passport
    - this command added after notice error while deploy on live server

- ## initialize **Passport Client** by run following command
    - run Command: [**php artisan passport:client --personal**]()
    - and create passport client

- ## [Setup multiple **PHP Versions** on **macOS**](https://medium.com/macoclock/how-to-install-multiple-php-versions-on-macos-1f290c32cd63) - Medium Link
    - [**StackOverFlow** Answer Link](https://stackoverflow.com/a/70066558)
  

    - After Install PHP Version
  1. First Unlink Current PHP Version with
  - run Command: [**brew unlink php@8.2**]()
  2. Second Link New PHP Version with
  - run Command: [**brew link --overwrite --force php@7.2**]()
