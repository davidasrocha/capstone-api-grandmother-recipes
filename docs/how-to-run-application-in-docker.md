# How to run this application in Docker

You can execute this application in Docker to development and production environment.

### How can run this application in development environment

To execute in development environment, there is a script in project to help you.

The script `run_docker.sh`, will help to do setup and run the application, without change any configuration.

To start, you need to run this command:

```
./run_docker.sh
```

This command is going to present for you the bash terminal of the PHP container, where you can run commands.

![alt text][php-docker-bash-terminal]

[php-docker-bash-terminal]: ./img/php-docker-bash-terminal.png "PHP Docker Bash Terminal"

Now you can access the application in your web browser using this URL `http://127.0.0.1`, and you will be see the `Symfony Welcome` page.

![alt text][api-grandmother-recipe-symfony-welcome-page]

[api-grandmother-recipe-symfony-welcome-page]: ./img/api-grandmother-recipe-symfony-welcome-page.png "Symfony Welcome Page"

### How to access the API Documentation

The application API provide an address to consult and test the resources, you can access the URL `http://127.0.0.1:8000/api/v1/doc` and will see the page:

![alt text][api-v1-doc]

[api-v1-doc]: ./img/api-v1-doc.png "API Doc"