FROM php:8.0.3-cli-alpine

LABEL maintainer="roberto.diana@gmail.com"
COPY src /var/www/html/src
COPY vendor /var/www/html/vendor
COPY bin /var/www/html/bin
WORKDIR /var/www/html/

# COPY .container/run.sh /var/www/html/bin/run.sh
# RUN chmod +x /var/www/html/bin/run.sh
# ENTRYPOINT ["/var/www/html/bin/run.sh"]
