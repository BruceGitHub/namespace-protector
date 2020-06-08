
install_phpunit:
	wget -O phpunit https://phar.phpunit.de/phpunit-9.phar
	mv phpunit ./vendor/bin/phpunit
	chmod +x ./vendor/bin/phpunit
	./vendor/bin/phpunit --check-version

install_csfixer:
	wget https://cs.symfony.com/download/php-cs-fixer-v2.phar -O ./bin/php-cs-fixer
	sudo chmod a+x ./bin/php-cs-fixer

phpstan:
	docker run --rm -v $(CURDIR):/app phpstan/phpstan analyse /app/src

csf:
	./bin/php-cs-fixer fix .

run:
	./bin/namespace-protector v

start:
	docker-compose -f ./.container/docker-compose.yml up -d php

shell: start
	docker-compose -f ./.container/docker-compose.yml exec php sh

stop:
	docker-compose -f ./.container/docker-compose.yml down

restart: stop start

install_composer:
	docker-compose run composer install

composer_shell:
	docker-compose run composer sh


.SILENT:
