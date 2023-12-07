FROM ubuntu:22.04
ARG DEBIAN_FRONTEND=noninteractive

WORKDIR /app

# Install php 8.1 and common extensions
RUN apt update; \
    \
    apt install --no-install-recommends php8.1; \
    \
    apt-get install -y \
    php8.1-cli \
    php8.1-common \
    php8.1-mysql \
    php8.1-zip \
    php8.1-gd \
    php8.1-mbstring \
    php8.1-curl \
    php8.1-xml \
    php8.1-bcmath \
    \
    ;

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Copy sources
COPY --link . ./
