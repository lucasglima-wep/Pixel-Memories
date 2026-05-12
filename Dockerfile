FROM php:8.2-apache

# Instala as dependências do sistema para a biblioteca GD
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli \
    && docker-php-ext-enable mysqli gd

# Ativa o mod_rewrite do Apache
RUN a2enmod rewrite

# Garante permissões para upload de arquivos
RUN chown -R www-data:www-data /var/www/html

# No seu Dockerfile, adicione estas linhas antes do final:
RUN echo "upload_max_filesize = 20M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 21M" >> /usr/local/etc/php/conf.d/uploads.ini