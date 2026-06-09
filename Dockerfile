# === ÉTAPE 1 : Compilation des assets avec Node ===
FROM node:20-alpine AS asset-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# === ÉTAPE 2 : Image finale PHP-Apache ===
FROM php:8.4-apache

# 1. Installation des dépendances système nécessaires
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Installation des extensions PHP requises par Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 3. Activation du module de réécriture d'Apache
RUN a2enmod rewrite

# 4. Modification de la racine d'Apache vers /public et activation d'AllowOverride
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf
RUN sed -ri -e 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# 5. Récupération de Composer officiel
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Définition du dossier de travail
WORKDIR /var/www/html

# 7. OPTIMISATION CACHE COMPOSER : Copie uniquement les fichiers de dépendances en premier
COPY composer.json composer.lock ./

# Installation sans exécuter les scripts (évite d'appeler la DB manquante au build)
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts --ignore-platform-reqs

# 8. COPIE DU CODE SOURCE APPLICATIF
COPY . .

# 9. COMPILATION VITE : Rapatriement des assets compilés à l'étape 1
COPY --from=asset-builder /app/public/build ./public/build

# 10. GESTION DES PERMISSIONS
# On s'assure qu'Apache possède les droits sur l'ensemble, surtout storage et bootstrap/cache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache