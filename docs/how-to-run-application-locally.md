# How to run this application locally

First, you must to copy the `.env` file to `.env.local` to development environment, and `.env.prod`  to production environment.

Inside this new file, you need to change 2 environment variables values: 
   
   * `APP_ENV`: for development environment you can use `dev`, and for production environment `prod`
   * `APP_DEBUG`, for development environment you can use `1`, and for production environment `0` 

Second, you must to run composer to install the application dependencies packages using this command to development environment:

```
composer install -o
```

Or, this command to production environment:

```
composer install --no-dev -o
``` 

Third, you must to `clear` and `warmup` the application cache using this command:

```
bin/console cache:clear

bin/console cache:warmup
```

Finally, you must to setup the database. This step is splitted between `database create` and `schema create`.

You can run this command to create database:

```
bin/console doctrine:database:create
```

And, you can this command to create schema:

```
bin/console doctrine:schema:create
```


**Optional PHP Built-In Server:** 

You may run this application using PHP built-in to development environment using this command:

```
php -S 127.0.0.1:8000 -t public/
```

Now you can access the application in your web browser using this URL `http://127.0.0.1:8000`, and you will be see the `Symfony Welcome` page.

![alt text][api-grandmother-recipe-symfony-welcome-page]

[api-grandmother-recipe-symfony-welcome-page]: ./img/api-grandmother-recipe-symfony-welcome-page.png "Symfony Welcome Page"

### How to access the API Documentation

The application API provide an address to consult and test the resources, you can access the URL `http://127.0.0.1:8000/api/v1/doc` and will see the page:

![alt text][api-v1-doc]

[api-v1-doc]: ./img/api-v1-doc.png "API Doc"