FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libicu-dev \
        libzip-dev \
        libxml2-dev \
        libonig-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql intl mbstring zip xml gd \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . /var/www/html

RUN if [ ! -d vendor ]; then composer install --no-interaction --prefer-dist --optimize-autoloader; fi \
    && chown -R www-data:www-data /var/www/html

COPY docker/render-start.sh /usr/local/bin/render-start
RUN chmod +x /usr/local/bin/render-start

CMD ["render-start"]
