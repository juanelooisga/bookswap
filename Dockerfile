FROM php:8.3-apache
RUN docker-php-ext-install pdo_mysql && a2enmod rewrite headers
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts --no-plugins --optimize-autoloader
COPY . .
RUN composer dump-autoload --no-dev --optimize --no-scripts \
    && sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && printf '<Directory /var/www/html/public>\nAllowOverride All\nRequire all granted\nDirectoryIndex frontend.html index.php\n</Directory>\nServerTokens Prod\nServerSignature Off\n' > /etc/apache2/conf-available/bookswap.conf \
    && a2enconf bookswap \
    && printf 'display_errors=Off\nlog_errors=On\nexpose_php=Off\n' > /usr/local/etc/php/conf.d/bookswap.ini
ENV APP_ENV=production APP_DEBUG=false DEMO_MODE=true
EXPOSE 10000
CMD ["sh", "docker/start.sh"]
