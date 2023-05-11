# Nginx-unpriv
FROM nginxinc/nginx-unprivileged:1.23-alpine
USER root

ARG PHP_PACKAGES="\
	php81-cli \
	php81-common \
	php81-curl \
	php81-dom \
	php81-exif \
	php81-fileinfo \
	php81-fpm \
	php81-gd \
	php81-iconv \
	php81-intl \
	php81-json \
	php81-mbstring \
	php81-opcache \
	php81-openssl \
	php81-pcntl \
	php81-pecl-redis \
	php81-pecl-xdebug \
	php81-phar \
	php81-simplexml \
	php81-pdo_mysql \
	php81-session \
	php81-soap \
	php81-tokenizer \
	php81-xml \
	php81-xmlwriter \
	php81-bcmath \
	php81-ctype"

RUN \
	# Add basic packages to Alpine
	apk add --update --no-cache \
	    $PHP_PACKAGES \
	    php81-pear \
	    php81-dev \
	    gcc \
	    g++ \
	    make \
		su-exec \
		git \
		openssh openssh-client curl \
		php81-openssl && \
		pecl81 install -f xdebug

RUN chmod u+s /sbin/su-exec

#ENV COMPOSER_AUTH='{"http-basic":{"repo.packagist.com":{"username":"token","password":"fe38b7412867788b7e2e36d305a85d5be60f5f18d2c4cb0651aa44d2b81b"}}}'
RUN cp /usr/bin/php81 /usr/bin/php
COPY --chown=101:0 dev/chown-project-dir.sh /docker-entrypoint.d/50-chown-project-dir.sh
COPY --from=composer /usr/bin/composer /usr/bin/
COPY --chown=101:0 dev/50_xdebug.ini /etc/php81/conf.d/

WORKDIR /var/www/
