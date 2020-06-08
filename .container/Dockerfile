FROM clearlinux/php-fpm:7.3.12

LABEL maintainer="roberto.diana@gmail.com"
RUN swupd bundle-add php-extras make wget

RUN mkdir -p /etc/php.d
RUN touch /etc/php.d/my-php.ini
RUN echo "zend_extension=xdebug.so" >> /etc/php.d/my-php.ini
RUN php -v
