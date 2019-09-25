# How to use Docker to build and run the application

For you build or run this application in Docker, you need to have `Docker` and `Docker Compose` installed in your environment.

* [How to build application in Docker]()
* [How to run application in Docker]()



# How to run this application in Docker

For you run this application in Docker, you need to have `Docker` and `Docker Compose` installed in your environment.

You can execute this application in Docker to development and production environment.

### How can run this application in development environment

To execute in development environment, there is a script in project to help you.

The script `run_docker.sh`, will help to do setup and run the application, without change any configuration.

For start, you need to run this command:

```
./run_docker.sh
```

This command is going to present for you the bash terminal of the PHP container, where you can run commands.

![alt text][php-docker-bash-terminal]

[php-docker-bash-terminal]: ./img/php-docker-bash-terminal.png "PHP Docker Bash Terminal"

Now you can access the application in your web browser using this URL `http://127.0.0.1`, and you will be see the `Symfony Welcome` page.

![alt text][api-grandmother-recipe-symfony-welcome-page]

[api-grandmother-recipe-symfony-welcome-page]: ./img/api-grandmother-recipe-symfony-welcome-page.png "Symfony Welcome Page"