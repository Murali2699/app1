pipeline {
    agent any

    environment {
        VAULT_ADDR = 'http://127.0.0.1:8200'
    }

    stages {
        stage('Clean Workspace before build') {
            steps {
                cleanWs()
            }
        }

        stage('Checkout Code from SCM') {
            steps {
                checkout([$class: 'GitSCM',
                    branches: [[name: '*/main']],
                    doGenerateSubmoduleConfigurations: false,
                    extensions: [],
                    submoduleCfg: [],
                    userRemoteConfigs: [[
                        url: 'https://github.com/Murali2699/app1.git',
                        credentialsId: 'jenkins-deploy-ssh'
                    ]]
                ])
            }
        }

        stage('Fetch Secrets from Vault and Prepare Environment') {
            steps {
                withVault([vaultSecrets: [[
                    path: 'secret/app1/postgres',
                    secretValues: [
                        [envVar: 'DB_USER', vaultKey: 'username'],
                        [envVar: 'DB_PASS', vaultKey: 'password']
                    ]
                ]]]) {
                    sh '''
                        echo "DB_USER=$DB_USER" > .env
                        echo "DB_PASS=$DB_PASS" >> .env
                        chmod 640 .env
                    '''
                }
            }
        }

        stage('Verify Files') {
            steps {
                sh '''
                    echo "Listing all files in workspace..."
                    ls -lah
                '''
            }
        }

        stage('Archive Backend Code') {
            steps {
                script {
                    if (fileExists('payment_automation_test_api')) {
                        archiveArtifacts artifacts: 'payment_automation_test_api/**/*', fingerprint: true
                    } else {
                        error "❌ Backend folder 'payment_automation_test_api' not found"
                    }
                }
            }
        }

        stage('Build Backend') {
            steps {
                sh '''
                    # Copy composer.json into API folder
                    cp composer.json payment_automation_test_api/

                    cd payment_automation_test_api
                    composer install --no-interaction --prefer-dist
                '''
            }
        }

        stage('Archive Artifact') {
            steps {
                archiveArtifacts artifacts: 'payment_automation_test_api/vendor/**/*', fingerprint: true
            }
        }

        stage('SonarQube Analysis') {
            steps {
                withSonarQubeEnv('MySonarQube') {
                    sh '''
                        cd payment_automation_test_api
                        sonar-scanner \
                            -Dsonar.projectKey=app1 \
                            -Dsonar.sources=.
                    '''
                }
            }
        }

        stage('Deploy to Environment') {
            steps {
                sh '''
                    echo "Deploying API..."
                    # Example: rsync to server
                    # rsync -avz payment_automation_test_api/ user@server:/var/www/app1_api/
                '''
            }
        }

        stage('Security Scan (OWASP ZAP)') {
            steps {
                sh '''
                    echo "Running OWASP ZAP scan..."
                    # zap-cli quick-scan http://localhost:8080
                '''
            }
        }
    }

    post {
        success {
            echo "✅ Build and deploy successful"
        }
        failure {
            echo "❌ Build or deploy failed"
        }
    }
}
