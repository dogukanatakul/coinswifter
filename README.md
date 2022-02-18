# Vue3 Devtools Eklentisi

https://github.com/vuejs/devtools/releases/tag/v6.0.0-beta.2
Devtools eklentisini kurduktan sonra firefox için bu eklenti de kurulmalı.

## Gereksinimler:

nodejs - v16.13.0

npm - 8.1.0

yarn - 1.22.17

# Çalıştırma

npm run watch (vue projesini derler ve kaynağı canlı tutar)
npm run prod

php artisan serve (route çıkışını sağlar)

# Kurulum

### Cloudflare Ip Adres algılaması için

php artisan cloudflare:reload

### İşlerin atanması için

php artisan upgrade:project

### Config Files

sudo nano /etc/nginx/nginx.conf\
sudo nano /etc/nginx/sites-enabled/default

sudo service nginx reload       
sudo /etc/init.d/nginx restart

sudo systemctl start mariadb.service

php -i | grep "Loaded Configuration File"


####Ubuntu:
sudo /etc/init.d/php7.4-fpm restart

sudo /etc/init.d/nginx restart

### Sunucu Yetki Hatası

- sudo find . -type f -exec chmod 664 {} \\;
- sudo find . -type d -exec chmod 775 {} \\;
- sudo setenforce 0
- sudo chown -R nginx:root ./
- chmod -R a+x node_modules
- sudo chmod -R 777 /var/www/laravel-vue-borsa/storage
- git config core.fileMode false
- git config --global core.filemode false

## Kullanıcı Durum Yönetimi

- 0: Yeni Kayıt
- 1: Telefon Doğrulamış
- 2: Mail Doğrulamış
- 11: Yeni Oturum Telefon Doğrulayacak
- 12: Yeni Oturum Mail Doğrulayacak
- 31: İletişim Adresi Değişikliği Eski Onaylayacak
- 32: İletişim Adresi Değişikliği Yeni Onaylayacak

## İletişim Durum Yönetimi

- 0: Yeni Eklenmiş
- 1: Doğrulanmış
- 3: Değişim Eski
- 4: Değişim Yeni

### Plugins


`sudo apt-get install php-pgsql`
