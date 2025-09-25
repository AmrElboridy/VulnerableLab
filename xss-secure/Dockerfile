FROM php:7.4-apache

# Copy app code
COPY . /var/www/html/

# Enable Apache modules
RUN a2enmod rewrite headers

# Add CSP config
RUN echo '<IfModule mod_headers.c>\n\
    Header set Content-Security-Policy "default-src '\''self'\''; script-src '\''self'\''; object-src '\''none'\''; base-uri '\''self'\'';"\n\
</IfModule>' > /etc/apache2/conf-enabled/csp.conf
