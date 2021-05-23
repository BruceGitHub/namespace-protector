
install_phpunit:
	wget -O phpunit https://phar.phpunit.de/phpunit-9.phar
	mv phpunit ./vendor/bin/phpunit
	chmod +x ./vendor/bin/phpunit
	./vendor/bin/phpunit --check-version

install_csfixer:
	wget -O ./bin/php-cs-fixer https://cs.symfony.com/download/php-cs-fixer-v2.phar 
	chmod a+x ./bin/php-cs-fixer

install_composer:
	docker-compose -f .container/docker-compose.yml run composer install

install: 
	install_composer && 
	install_phpunit &&
	install_csfixer

phar_build:
	box build -v
	mv ./output/namespace-protector.phar .phar/namespace-protector

docker_command_build:
	docker build . -t brucedockerhub/namespace-protector:0.1.0  -f .container/DockerCommand/Dockerfile

docker_command_push:
	docker push brucedockerhub/namespace-protector:0.1.0

docker_command_shell:
	docker run --entrypoint /bin/sh --rm -it -v $(PWD):/namespace-protector brucedockerhub/namespace-protector:0.1.0

docker_test_build:
	docker build . -t brucegithub/namespace-protector:0.1.0 -f .container/Dockerfile

docker_run:
	docker run --rm brucegithub/namespace-protector:0.1.0 list

docker_shell:
	docker run --rm -ti brucegithub/namespace-protector:0.1.0 sh

psalm:
	docker-compose -f .container/docker-compose.yml exec php php ./vendor/bin/psalm

psalm-clearcache:
	docker-compose -f .container/docker-compose.yml exec php php ./vendor/bin/psalm --clear-cache

psalm-with-issue:
	docker-compose -f .container/docker-compose.yml exec php php ./vendor/bin/psalm --show-info=true


csf:
	docker-compose -f ./.container/docker-compose.yml exec php7 php ./bin/php-cs-fixer fix --verbose

run:
	./bin/namespace-protector v

start:
	docker-compose -f ./.container/docker-compose.yml up php php7

shell: start
	docker-compose -f ./.container/docker-compose.yml run php sh

stop:
	docker-compose -f ./.container/docker-compose.yml down

restart: stop start

composer_shell:
	docker-compose -f ./.container/docker-compose.yml run --rm composer sh

test:
	docker-compose -f ./.container/docker-compose.yml exec php php ./vendor/bin/phpunit --bootstrap ./vendor/autoload.php tests --testdox --colors=always --order-by=defects

test-filter:
	./vendor/bin/phpunit --bootstrap ./vendor/autoload.php tests --testdox --colors=always --order-by=defects --filter=$(filter)

test-coverage:
	./vendor/bin/phpunit --bootstrap ./vendor/autoload.php tests --coverage-html=./.coverage/ --whitelist=./src

.SILENT:
