pipeline {
    agent any

    environment {
        VAULT_ADDR = 'http://127.0.0.1:8200'
        SONARQUBE_SERVER = 'SonarQubeServer'
        SONARQUBE_SCANNER = 'sonar-scanner'
        BACKEND_DIR = "./payment_automation_api_test"
    }

    options {
        skipDefaultCheckout() // We'll do explicit checkout
        timestamps()
    }

    stages {

        stage('Clean Workspace before build') {
            steps {
                cleanWs()
            }
        }

        stage('Checkout Code from SCM') {
            steps {
                checkout scm
            }
        }

        stage('Fetch Secrets from Vault and Prepare Environment') {
            steps {
                withVault([configuration: [vaultUrl: env.VAULT_ADDR, vaultCredentialId: 'vault-jenkins-approle'],
                           vaultSecrets: [[path: 'secret/app1/postgres', engineVersion: 2, secretValues: [
                               [envVar: 'DB_USER', vaultKey: 'username'],
                               [envVar: 'DB_PASS', vaultKey: 'password'],
                               [envVar: 'DB_NAME', vaultKey: 'dbname'],
                               [envVar: 'DB_PORT', vaultKey: 'port'],
                               [envVar: 'DB_HOST', vaultKey: 'host']
                           ]]]]) {
                    sh '''
                        cat > .env <<EOF
DB_HOST=${DB_HOST}
DB_DATABASE=${DB_NAME}
DB_USERNAME=${DB_USER}
DB_PASSWORD=${DB_PASS}
DB_PORT=${DB_PORT}
EOF
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
                    if (fileExists(env.BACKEND_DIR)) {
                        archiveArtifacts artifacts: "${env.BACKEND_DIR}/**/*", fingerprint: true
                    } else {
                        echo "Backend folder not found, skipping archive"
                    }
                }
            }
        }

        stage('Build Backend') {
            steps {
                script {
                    if (fileExists(env.BACKEND_DIR)) {
                        dir(env.BACKEND_DIR) {
                            sh '''
                                # Install dependencies
                                composer install --no-interaction --prefer-dist
                            '''
                        }
                    } else {
                        echo "Backend folder not found, skipping build"
                    }
                }
            }
        }

        stage('Archive Artifact') {
            steps {
                archiveArtifacts artifacts: "**/vendor/**/*", fingerprint: true
            }
        }

        stage('SonarQube Analysis') {
            steps {
                withSonarQubeEnv("${env.SONARQUBE_SERVER}") {
                    sh """
                        ${tool(env.SONARQUBE_SCANNER)}/bin/sonar-scanner \
                            -Dsonar.projectKey=app1 \
                            -Dsonar.sources=.
                    """
                }
            }
        }

        stage('Deploy to Environment') {
            steps {
                sshagent(['jenkins-deploy-ssh']) {
                    sh "rsync -avz --delete --exclude '.git' ./ deploy@192.168.5.245:/var/www/html/payment_automation_test"
                }
            }
        }

        stage('Security Scan (OWASP ZAP)') {
            steps {
                sh '''
                    docker run -t owasp/zap2docker-stable zap-baseline.py \
                        -t http://192.168.5.245/payment_automation_test \
                        -r zap_report.html
                '''
                archiveArtifacts artifacts: 'zap_report.html', fingerprint: true
            }
        }
    }

    post {
        success {
            echo '✅ Deployment succeeded'
        }
        failure {
            echo '❌ Build or deploy failed'
        }
    }
}
