# Dockerfile

FROM php:8.2-fpm

# Instala extensões do PHP
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl

# Instala o Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Cria diretório de trabalho
WORKDIR /var/www

# Copia arquivos do projeto
COPY . .

# Instala dependências do Laravel
RUN composer install

# Permissões (opcional)
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www/storage

EXPOSE 9000
CMD ["php-fpm"]
