# syntax=docker/dockerfile:1
# This app is plain PHP: it has no Node build, Composer install, or database.
FROM php:8.4-apache-bookworm

ENV PORT=10000 \
    PORTFOLIO_CONTACT_ENABLED=true

WORKDIR /var/www/html

RUN set -eux; \
    cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"; \
    if ! php -r 'exit(extension_loaded("Zend OPcache") ? 0 : 1);'; then \
        docker-php-ext-install opcache; \
    fi; \
    a2enmod headers deflate expires; \
    php -r 'foreach (["curl", "openssl", "session", "json", "Zend OPcache"] as $extension) { if (!extension_loaded($extension)) { fwrite(STDERR, "Missing extension: " . $extension . PHP_EOL); exit(1); } }'

COPY docker/php-production.ini /usr/local/etc/php/conf.d/portfolio.ini
COPY docker/apache-portfolio.conf /etc/apache2/sites-available/000-default.conf
COPY docker/start-portfolio.sh /usr/local/bin/start-portfolio
COPY docker/healthcheck.php /usr/local/bin/portfolio-healthcheck.php

# Copy only runtime files. Documentation, Git metadata, backups, and the unused
# PHPMailer vendor directory never become public or enter the production image.
COPY index.php project.php ./
COPY assets/ ./assets/
COPY config/ ./config/
COPY data/ ./data/
COPY handlers/ ./handlers/
COPY includes/ ./includes/
COPY pages/ ./pages/

RUN set -eux; \
    sed -i 's/\r$//' /usr/local/bin/start-portfolio; \
    chmod 755 /usr/local/bin/start-portfolio; \
    find /var/www/html -type d -exec chmod 755 {} +; \
    find /var/www/html -type f -exec chmod 644 {} +; \
    find /var/www/html -type f -name '*.php' -print0 | xargs -0 -n 1 php -l; \
    php -l /usr/local/bin/portfolio-healthcheck.php; \
    mkdir -p /var/lib/php/sessions; \
    chown www-data:www-data /var/lib/php/sessions; \
    chmod 700 /var/lib/php/sessions

EXPOSE 10000
HEALTHCHECK --interval=30s --timeout=5s --start-period=15s --retries=3 CMD ["php", "/usr/local/bin/portfolio-healthcheck.php"]
CMD ["start-portfolio"]
