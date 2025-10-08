# Gunakan image PHP dengan Apache yang sudah siap pakai
FROM php:8.2-apache 

# PERBAIKAN: Instal ekstensi database yang diperlukan
# Perintah ini menginstal mysqli, pdo, dan pdo_mysql.
RUN docker-php-ext-install mysqli pdo pdo_mysql 

# Tetapkan direktori kerja
WORKDIR /var/www/html

# Salin semua file source code Anda
COPY . /var/www/html

# Port 80 adalah port default Apache
EXPOSE 80
