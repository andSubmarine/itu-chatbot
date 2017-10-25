FROM niclashedam/ubuntu-laravel:latest

MAINTAINER Niclas Hedam <niclas@frax.dk>

ENV APP_DEBUG=false
ENV APP_ENV=production
ENV APP_LOG=daily

# Expose apache.
EXPOSE 80

RUN apt-get install -y locales && locale-gen da_DK.UTF-8

ADD . /var/www/
RUN cd /var/www && \
composer install --no-dev && \
rm -rf /var/www/.git && \
chown -R www-data:www-data /var/www && \
chmod -R 755 /var/www

# Add cronjob
RUN printf "* * * * * php /var/www/artisan schedule:run\n" > /var/crontab && chmod 755 /var/crontab

ADD storage/supervisord.conf /etc/supervisord.conf
RUN curl https://gist.githubusercontent.com/niclashedam/aa23002c978d4e7f9b2501223ef5a076/raw > /etc/apache2/sites-enabled/000-default.conf


CMD /usr/bin/supervisord -n -c /etc/supervisord.conf
