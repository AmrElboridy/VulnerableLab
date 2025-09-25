FROM php:7.4-apache

RUN apt-get update && apt-get install -y sqlite3 libsqlite3-dev

COPY . /var/www/html/

RUN a2enmod rewrite headers

# Add CSP headers
RUN echo '<IfModule mod_headers.c>\n\
    Header set Content-Security-Policy "default-src '\''self'\''; script-src '\''self'\''; object-src '\''none'\''; base-uri '\''self'\'';"\n\
</IfModule>' > /etc/apache2/conf-enabled/csp.conf
