FROM php:8.0.3-cli-alpine
LABEL maintainer="roberto.diana@gmail.com"
WORKDIR /namespace-protector-dir

RUN wget -O namespace-protector https://raw.githubusercontent.com/BruceGitHub/namespace-protector-phar/main/namespace-protector \
    && chmod +x /namespace-protector-dir/namespace-protector

WORKDIR /namespace-protector
ENTRYPOINT [ "/namespace-protector-dir/namespace-protector"]