# This Dockerfile is intended for the production build of the Allisa Platform when using docker-compose-example-production.yml (docker compose up -f docker-compose-example-production.yml).
# For development, see docker/php-fpm/Dockerfile for development usage with docker-compose-developmnet.yml (docker compose up -f docker-compose-example-production.yml)

# Added: Weasyprint Installation

# Copied from cyberduck/php-fpm-laravel
#https://github.com/Cyber-Duck/php-fpm-laravel/blob/8.1/Dockerfile
FROM allisa/php8.1-fpm:1.2.3

ENV COMPOSER_MEMORY_LIMIT='-1'

#####################################
# App specific PHP config:
#####################################

ADD docker/app/laravel.ini /usr/local/etc/php/conf.d

#####################################
# Laravel Schedule Cron Job:
#####################################

RUN echo "* * * * * www-data /usr/local/bin/php /var/www/artisan schedule:run >> /dev/null 2>&1"  >> /etc/cron.d/laravel-scheduler
RUN chmod 0644 /etc/cron.d/laravel-scheduler

#####################################
# Application Code:
#####################################

COPY --chown=www-data:www-data --chmod=664 . /var/www/html

WORKDIR /var/www/html

RUN find /var/www/html/. -type d -exec chmod 775 {} \;
RUN chgrp -R www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R ug+rwx /var/www/html/storage /var/www/html/bootstrap/cache

RUN composer install --prefer-dist --no-scripts -q -o --optimize-autoloader
RUN composer dump-autoload

RUN php artisan larecipe:install
RUN php artisan telescope:publish
RUN php artisan route:cache

COPY docker/app/docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
RUN ln -s /usr/local/bin/docker-entrypoint.sh /
ENTRYPOINT ["docker-entrypoint.sh"]

# Required to start sshd, otherwise the container will error out on startup with the message
# "sshd: no hostkeys available -- exiting."
# @see https://stackoverflow.com/a/65348102/413531
RUN ssh-keygen -A

# For NGINX
EXPOSE 9000

CMD ["php-fpm"]
