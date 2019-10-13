pipeline {
    agent any

    stages {
        stage('Build Images') {
            steps {
                sh "./build_docker.sh"
            }
        }
        stage('Deploy Images') {
            steps {
                script {
                    docker.withRegistry('', 'DOCKER_HUB_DEVOPS') {
                        sh "./upload_docker.sh"
                    }
                }
            }
        }
    }
}