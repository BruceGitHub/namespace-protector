stan_check:
	docker run --rm -v $(CURDIR):/app phpstan/phpstan analyse /app/src

cs_check_install:
	wget https://cs.symfony.com/download/php-cs-fixer-v2.phar -O ./bin/php-cs-fixer
	sudo chmod a+x ./bin/php-cs-fixer

cs_run:
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

composer_install:
	docker-compose run composer install

composer_shell:
	docker-compose run composer sh


.SILENT:
