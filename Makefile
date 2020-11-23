
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

psalm:
	docker-compose -f .container/docker-compose.yml run --rm php ./vendor/bin/psalm

csf:
	./bin/php-cs-fixer fix --verbose

run:
	./bin/namespace-protector v

start:
	docker-compose -f ./.container/docker-compose.yml up -d php

shell: start
	docker-compose -f ./.container/docker-compose.yml exec php sh

stop:
	docker-compose -f ./.container/docker-compose.yml down

restart: stop start

composer_shell:
	docker-compose -f ./.container/docker-compose.yml run --rm composer sh

test:
	./vendor/bin/phpunit --bootstrap ./vendor/autoload.php tests --testdox --colors=always --order-by=defects

test-filter:
	./vendor/bin/phpunit --bootstrap ./vendor/autoload.php tests --testdox --colors=always --order-by=defects --filter=$(filter)

test-coverage:
	./vendor/bin/phpunit --bootstrap ./vendor/autoload.php tests --coverage-html=./.coverage/ --whitelist=./src

.SILENT:
