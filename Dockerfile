# Use Ubuntu 22.04 as the base image
FROM ubuntu:22.04

# Update the package repository
RUN apt-get update -y

# Install PHP 8.1 and its dependencies
RUN apt-get install -y software-properties-common && \
    add-apt-repository ppa:ondrej/php && \
    apt-get update -y && \
    DEBIAN_FRONTEND="noninteractive" apt-get install -y php8.1 \
    php8.1-cli \
    php8.1-common \
    php8.1-mysql \
    php8.1-mysqli \
    php8.1-zip \
    php8.1-gd \
    php8.1-mbstring \
    php8.1-curl \
    php8.1-xml \
    php8.1-bcmath \
    curl \
    wget \
    ;

# Install MariaDB 10.6 and set up a MySQL user
RUN DEBIAN_FRONTEND="noninteractive" apt-get install -y mariadb-server-10.6 && \
    service mariadb start && \
    mariadb -e "CREATE DATABASE app;" && \
    mariadb -e "CREATE USER 'app'@'localhost' IDENTIFIED BY 'password';" && \
    mariadb -e "GRANT ALL PRIVILEGES ON *.* TO 'app'@'localhost';" && \
    mariadb -e "FLUSH PRIVILEGES;"

# Set the working directory
WORKDIR /app
