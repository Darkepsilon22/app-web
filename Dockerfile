FROM php:8.2-fpm

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    curl \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier le script start.sh
COPY start.sh /usr/local/bin/start.sh

# Donner les permissions d'exécution au script
RUN chmod +x /usr/local/bin/start.sh

# Exposer le port 8000
EXPOSE 8000

# Définir le répertoire de travail
WORKDIR /app-crypto

# Exécuter le script start.sh
CMD ["/usr/local/bin/start.sh"]
