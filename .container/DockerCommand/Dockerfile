FROM php:8.0.3-cli-alpine
LABEL maintainer="roberto.diana@gmail.com"
WORKDIR /namespace-protector-dir

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY .container/DockerCommand/custom.ini /usr/local/etc/php/conf.d/custom.ini

RUN apk add --no-cache \
	graphviz

COPY .phar/namespace-protector /namespace-protector-dir/namespace-protector
RUN chmod +x /namespace-protector-dir/namespace-protector

WORKDIR /namespace-protector
ENTRYPOINT [ "/namespace-protector-dir/namespace-protector"]