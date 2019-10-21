pipeline {
    agent any

    environment {
        CLUSTER_NAME = "capstone"
        BUCKET_NAME = "capstone-cicd-storage-eks-configs"
        REGION = "us-west-2"
    }

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
                sh "docker rmi -f davidasrocha/api-grandmother-recipes-php:$GIT_BRANCH-$GIT_COMMIT"
                sh "docker rmi -f davidasrocha/api-grandmother-recipes-php:latest"
            }
        }
        stage('Deploy') {
            environment {
                KUBECONFIG = "$WORKSPACE/.kube/config"
            }
            steps {
                withAWS(region: "$REGION", credentials: 'AWS_DEVOPS') {
                    sh "rm -rf $WORKSPACE/kubernetes/config-maps/*"
                    s3Download(file: "$WORKSPACE/kubernetes/config-maps/", bucket: "$BUCKET_NAME", path: "prod/", force: true)
                    s3Download(file: "$KUBECONFIG", bucket: "$BUCKET_NAME", path: "$CLUSTER_NAME", force: true)
                    sh "./devops_deploy_app.sh $CLUSTER_NAME $GIT_BRANCH-$GIT_COMMIT LoadBalancer"
                }
            }
        }
        stage('Validate') {
            environment {
                KUBECONFIG = "$WORKSPACE/.kube/config"
            }
            steps {
                withAWS(region: "$REGION", credentials: 'AWS_DEVOPS') {
                    s3Download(file: "$KUBECONFIG", bucket: "$BUCKET_NAME", path: "$CLUSTER_NAME", force: true)
                    sh "./devops_deploy_validate.sh $CLUSTER_NAME"
                }
            }
        }
        stage('Swap') {
            environment {
                KUBECONFIG = "$WORKSPACE/.kube/config"
            }
            steps {
                withAWS(region: "$REGION", credentials: 'AWS_DEVOPS') {
                    s3Download(file: "$KUBECONFIG", bucket: "$BUCKET_NAME", path: "$CLUSTER_NAME", force: true)
                    sh "./devops_deploy_swap.sh $CLUSTER_NAME"
                }
            }
        }
    }
}