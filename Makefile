.ONESHELL:

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
	docker-compose -f ./.container/docker-compose.yml exec php php ./tools/php-cs-fixer/vendor/bin/php-cs-fixer fix src

start:
	docker-compose -f ./.container/docker-compose.yml up php

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
	docker-compose -f ./.container/docker-compose.yml exec php php ./vendor/bin/phpunit --bootstrap ./vendor/autoload.php tests --testdox --colors=always --order-by=defects --filter=$(filter)


#Local file sysyem 

local_run:
	./bin/namespace-protector v

local_test_coverage:
	./vendor/bin/phpunit --bootstrap ./vendor/autoload.php tests --coverage-html=./.coverage/ --whitelist=./src

local_install_phpunit:
	wget -O phpunit https://phar.phpunit.de/phpunit-9.phar
	mv phpunit ./vendor/bin/phpunit
	chmod +x ./vendor/bin/phpunit
	./vendor/bin/phpunit --check-version

local_install_csfixer:
	composer require --working-dir=tools/php-cs-fixer friendsofphp/php-cs-fixer

local_install_composer:
	docker-compose -f .container/docker-compose.yml run composer install

local_install: 
	install_composer && 
	install_phpunit &&
	install_csfixer

local_phar_build:
	box build -v
	mv ./output/namespace-protector.phar .phar/namespace-protector
	cp .phar/namespace-protector ./../namespace-protector-phar/namespace-protector

local_prepare_phar_release:
	$(MAKE) -C ./ -C ./../namespace-protector-phar/

local_prepare_release: local_phar_build docker_command_build local_prepare_phar_release
	echo "Release ready"

.SILENT:
