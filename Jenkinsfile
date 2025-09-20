pipeline {
    agent any

    environment {
        VAULT_ADDR = 'http://127.0.0.1:8200'
        SONARQUBE_SERVER = 'SonarQubeServer'
        SONARQUBE_SCANNER = 'sonar-scanner'
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Install dependencies') {
            steps {
                sh '''
                    # Install Composer if not installed
                    which composer || (
                        php -r "copy('https://getcomposer.org/installer','composer-setup.php');" &&
                        php composer-setup.php --install-dir=/usr/local/bin --filename=composer
                    ) || true
                    
                    # Install project dependencies
                    composer install --no-interaction --prefer-dist || true
                    
                    # Install phpdotenv for environment variable handling
                    composer require vlucas/phpdotenv --no-interaction || true
                '''
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

        stage('Quality Gate') {
            steps {
                timeout(time: 30, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: false
                }
            }
        }

        stage('Fetch secrets from Vault') {
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

        stage('Deploy to App Server') {
            steps {
                sshagent(['jenkins-deploy-ssh']) {
                    sh "rsync -avz --delete --exclude '.git' ./ deploy@192.168.5.245:/var/www/html/payment_automation_test"
                }
            }
        }
    }

    stage('Security Scan (OWASP ZAP)') {
    steps {
        sh '''
            # Run ZAP in daemon mode scanning your deployed app
            docker run -t owasp/zap2docker-stable zap-baseline.py \
                -t http://192.168.5.245/payment_automation_test \
                -r zap_report.html
        '''
        archiveArtifacts artifacts: 'zap_report.html', fingerprint: true
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
