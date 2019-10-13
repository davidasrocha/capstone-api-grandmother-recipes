pipeline {
    agent any

    stages {
        stage('Docker Images') {
            parallel {
                stage('NGINX Builds + Deploys') {
                    stages {
                        stage('Build NGINX') {
                            steps {
                                sh "docker build --force-rm --rm --no-cache -t davidasrocha/api-grandmother-recipes-nginx -f $WORKSPACE/docker/nginx/Dockerfile ."
                            }
                        }
                        stage('Deploy NGINX') {
                            steps {
                                script {
                                    docker.withRegistry('', 'DOCKER_HUB_DEVOPS') {
                                        sh "docker tag davidasrocha/api-grandmother-recipes-nginx davidasrocha/api-grandmother-recipes-nginx:latest"
                                        sh "docker push davidasrocha/api-grandmother-recipes-nginx:latest"
                                        sh "docker tag davidasrocha/api-grandmother-recipes-nginx davidasrocha/api-grandmother-recipes-nginx:$GIT_BRANCH-$GIT_COMMIT"
                                        sh "docker push davidasrocha/api-grandmother-recipes-nginx:$GIT_BRANCH-$GIT_COMMIT"
                                    }
                                }
                            }
                        }
                    }
                }
                stage('PHP Builds + Deploys') {
                    stages {
                        stage('Build PHP') {
                            steps {
                                sh "docker build --force-rm --rm --no-cache -t davidasrocha/api-grandmother-recipes-php -f $WORKSPACE/docker/php/Dockerfile ."
                            }
                        }
                        stage('Deploy PHP') {
                            steps {
                                script {
                                    docker.withRegistry('', 'DOCKER_HUB_DEVOPS') {
                                        sh "docker tag davidasrocha/api-grandmother-recipes-php davidasrocha/api-grandmother-recipes-php:latest"
                                        sh "docker push davidasrocha/api-grandmother-recipes-php:latest"
                                        sh "docker tag davidasrocha/api-grandmother-recipes-php davidasrocha/api-grandmother-recipes-php:$GIT_BRANCH-$GIT_COMMIT"
                                        sh "docker push davidasrocha/api-grandmother-recipes-php:$GIT_BRANCH-$GIT_COMMIT"
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        stage('Remove Images') {
            steps {
                sh "docker rmi -f davidasrocha/api-grandmother-recipes-nginx:$GIT_BRANCH-$GIT_COMMIT"
                sh "docker rmi -f davidasrocha/api-grandmother-recipes-nginx:latest"
                sh "docker rmi -f davidasrocha/api-grandmother-recipes-nginx"
                sh "docker rmi -f davidasrocha/api-grandmother-recipes-php:$GIT_BRANCH-$GIT_COMMIT"
                sh "docker rmi -f davidasrocha/api-grandmother-recipes-php:latest"
                sh "docker rmi -f davidasrocha/api-grandmother-recipes-php"
            }
        }
    }
}