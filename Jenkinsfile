pipeline {
    agent none
    environment {
        CI = 'true'
    }
    stages {
        stage('Build') {
            agent {
                dockerfile {
                    args '-v /srv/docker/jenkins-data/.ssh:/root/.ssh -p 3000:3000'
                }
            }

            steps {
                sh 'composer install --dev'
            }
        }
        stage('Test') {
            agent {
                dockerfile {
                    args '-v /srv/docker/jenkins-data/.ssh:/root/.ssh -p 3000:3000'
                }
            }
            steps {
                sh 'composer run test'
            }
        }
    }
}
