FROM php:8.0.3-cli-alpine

# Install system dependencies
RUN apk update && apk add git

LABEL maintainer="roberto.diana@gmail.com"

RUN mkdir -p /etc/php.d
RUN touch /etc/php.d/my-php.ini
RUN echo "zend_extension=xdebug.so" >> /etc/php.d/my-php.ini
RUN php -v
WORKDIR /var/www/html
