FROM php:7.4-apache

# Install SQLite for demo DB
RUN apt-get update && apt-get install -y sqlite3 libsqlite3-dev

# Copy code
COPY . /var/www/html/

RUN a2enmod rewrite
