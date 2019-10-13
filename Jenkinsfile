pipeline {
    agent any

    stages {
        stage('Docker Images') {
            parallel {
                stage('Builds + Deploys') {
                    stages {
                        stage('Build NGINX') {
                            steps {
                                sh "docker build --force-rm --rm --no-cache -t davidasrocha/api-grandmother-recipes-nginx"
                            }
                        }
                        stage('Deploy NGINX') {
                            steps {
                                script {
                                    docker.withRegistry('', 'DOCKER_HUB_DEVOPS') {
                                        sh "docker tag davidasrocha/api-grandmother-recipes-nginx davidasrocha/api-grandmother-recipes-nginx:latest"
                                        sh "docker tag davidasrocha/api-grandmother-recipes-nginx davidasrocha/api-grandmother-recipes-nginx:$GIT_COMMIT"
                                        sh "docker push davidasrocha/api-grandmother-recipes-nginx:latest"
                                        sh "docker push davidasrocha/api-grandmother-recipes-nginx:$GIT_COMMIT"
                                    }
                                }
                            }
                        }
                    }
                }
                stage('Builds + Deploys') {
                    stages {
                        stage('Build PHP') {
                            steps {
                                sh "docker build --force-rm --rm --no-cache -t davidasrocha/api-grandmother-recipes-php"
                            }
                        }
                        stage('Deploy PHP') {
                            steps {
                                script {
                                    docker.withRegistry('', 'DOCKER_HUB_DEVOPS') {
                                        sh "docker tag davidasrocha/api-grandmother-recipes-php davidasrocha/api-grandmother-recipes-php:latest"
                                        sh "docker tag davidasrocha/api-grandmother-recipes-php davidasrocha/api-grandmother-recipes-php:$GIT_COMMIT"
                                        sh "docker push davidasrocha/api-grandmother-recipes-php:latest"
                                        sh "docker push davidasrocha/api-grandmother-recipes-php:$GIT_COMMIT"
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}