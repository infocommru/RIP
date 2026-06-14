# syntax=docker/dockerfile:1
FROM ubuntu:24.04

RUN apt update && apt upgrade -y
RUN apt -y install software-properties-common curl gnupg --no-install-recommends
RUN add-apt-repository -y ppa:ondrej/php

RUN apt-get install -y locales apache2 libapache2-mod-php7.4 php7.4 php7.4-mbstring \
	php7.4-xml php7.4-gd php7.4-mysqli php7.4-curl php7.4-zip \
	python3 python3-pip python3-venv poppler-utils unzip --no-install-recommends \
	&& apt-get clean
RUN locale-gen ru_RU.UTF-8 && update-locale LANG=ru_RU.UTF-8
ENV LANG=ru_RU.UTF-8
ENV LC_ALL=ru_RU.UTF-8

RUN rm /var/www/html/* && rm /etc/apache2/sites-enabled/*
COPY rip.conf /etc/apache2/sites-available/rip.conf
RUN ln -s /etc/apache2/sites-available/rip.conf /etc/apache2/sites-enabled/rip.conf && a2enmod rewrite \
	&& chown www-data:www-data /var/www/html

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
php -r "if (hash_file('sha384', 'composer-setup.php') === 'c8b085408188070d5f52bcfe4ecfbee5f727afa458b2573b8eaaf77b3419b0bf2768dc67c86944da1544f06fa544fd47') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }" && \
php composer-setup.php --install-dir=/usr/local/bin && \
php -r "unlink('composer-setup.php');"

WORKDIR /var/www/html

USER www-data
RUN python3 -m venv .venv
ENV PATH="/var/www/html/.venv/bin:$PATH"
RUN --mount=type=cache,target=/tmp/.cache/pip .venv/bin/pip3 install --no-cache-dir --upgrade pip \
	&& .venv/bin/pip3 install --no-cache-dir pandas numpy openpyxl XlsxWriter reportlab pdf2image pyaspeller pyppeteer tqdm

COPY --chown=www-data:www-data composer.json composer.lock ./
ENV COMPOSER_CACHE_DIR=/tmp/.cache/composer
RUN --mount=type=cache,target=/tmp/.cache/composer php /usr/local/bin/composer.phar install --no-interaction --prefer-dist --optimize-autoloader
COPY --chown=www-data:www-data . .
RUN rm rip.conf && mkdir web/assets && chown www-data:www-data web/assets
USER root

EXPOSE 80
CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
