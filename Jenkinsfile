// http://jenkins-php.org/configuration.html
// https://gist.github.com/lferro9000/471ae1a98267e20530d989f64f5290ee

pipeline{
    agent any
    stages{
         stage('checkout') {
         steps {
         sh '''
         env
         '''

         checkout([
          $class: 'GitSCM',
          branches: [[name: env.GIT_BUILD_REF]],
          userRemoteConfigs: [[
            url: env.GIT_REPO_URL,
            credentialsId: env.CREDENTIALS_ID
          ]]])
         }
        }
        stage('start redis/mysql'){
            steps {
                sh '''
                docker network create pcit
                '''

                sh '''
                docker run -d --network pcit --name=redis redis:alpine
                '''

                sh '''
                docker run -d --network pcit \
                -e MYSQL_DATABASE=test \
                -e MYSQL_ROOT_PASSWORD=test \
                --name mysql \
                mysql:8.0.21
                '''
            }
        }
        // stage("PHPLint") {
        //     steps{
        //         sh 'find app src -name "*.php" -print0 | xargs -0 -n1 php -l'
        //     }
        // }
        stage("script"){
            steps{
                sh '''
                docker run -i --rm \
                -v $PWD:/app \
                -v /tmp/.composer/cache:/tmp/composer/cache \
                khs1994/php:7.4.10-composer-alpine \
                sh -ecx ' \
                composer config -g repos.packagist composer https://packagist.mirrors.sjtug.sjtu.edu.cn \
                && composer install
                '
                '''

                sh '''
                docker run -i --rm \
                -v $PWD:/app \
                --network pcit \
                -e CI_REDIS_HOST=redis \
                -e CI_REDIS_PORT=6379 \
                -e CI_REDIS_DATABASE=15 \
                -e CI_MYSQL_HOST=mysql \
                -e CI_MYSQL_PORT=3306 \
                -e CI_MYSQL_USERNAME=root \
                -e CI_MYSQL_PASSWORD=test \
                -e CI_MYSQL_DATABASE=test \
                -e CI_WEBHOOKS_TOKEN=pcit \
                khs1994/php:8.0.0RC2-composer-alpine \
                sh -ecx ' \
                echo "zend_extension=xdebug" > ${PHP_INI_DIR}/conf.d/docker-php-ext-xdebug.ini \
                && echo "xdebug.mode=coverage" >> ${PHP_INI_DIR}/conf.d/docker-php-ext-xdebug.ini \
                && vendor/bin/phpunit --coverage-clover=build/logs/clover.xml \
                --coverage-html build/coverage \
                --coverage-xml build/coverage-xml \
                --coverage-cache cache/coverage \
                --log-junit build/logs/junit.xml \
                && composer run phploc
                '
                '''
            }
            post{
                always{
                    echo "========always========"
                }
                success{
                    // junit
                    sh 'ls -la build/logs/junit.xml'
                    sh 'touch build/logs/junit.xml'
                    junit 'build/logs/junit.xml'

                    // coverage-html
                    codingHtmlReport(
                        name: 'coverage-html',
                        tag: 'coverage-html',
                        path: 'build/coverage',
                        des: 'coverage-html',
                        entryFile: 'index.html'
                    )

                    // openapi
                    sh 'composer run openapi:ui'

                    codingHtmlReport(
                        name: 'openapi',
                        tag: 'openapi',
                        path: 'redoc-static.html',
                        des: 'openapi',
                        // entryFile: 'index.html'
                    )

                    // phpdox
                    sh '''
                    composer global require theseer/phpdox

                    # composer run phpdox
                    composer run phpdox:coding:ci
                    '''

                    codingHtmlReport(
                        name: 'phpdox',
                        tag: 'phpdox',
                        path: 'build/phpdox/html',
                        des: 'phpdox',
                        entryFile: 'index.html'
                    )

                    // sami
                    sh 'rm -rf build'

                    // sh '''
                    // docker run -i --rm \
                    // -v $PWD:/app \
                    // khs1994/php:sami \
                    // update .sami.php
                    // '''

                    // codingHtmlReport(
                    //     name: 'sami',
                    //     tag: 'sami',
                    //     path: 'build',
                    //     des: 'sami',
                    //     entryFile: 'index.html'
                    // )
                }
                failure{
                    echo "========A execution failed========"
                }
            }
        }
    }
    post{
        always{
            echo "========always========"
        }
        success{
            echo "========pipeline executed successfully ========"
        }
        failure{
            echo "========pipeline execution failed========"
        }
    }
}
