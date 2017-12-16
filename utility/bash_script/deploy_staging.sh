cd /var/www/html/stockadjustmentproj
chown -R www-data /var/www/html/stockadjustmentproj/
git stash
git pull
wget -O composer https://getcomposer.org/composer.phar
chmod +x composer
./composer install
chown -R www-data /var/www/html/stockadjustmentproj/
