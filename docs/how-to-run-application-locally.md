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