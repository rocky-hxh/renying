# initialization
```
pacman -S composer
https_proxy='socks5://127.0.0.1:1080' composer create-project symfony/skeleton:"6.1.*" project
```
# homestead
```
cd project/
composer require laravel/homestead --dev
php vendor/bin/homestead make

pacman -S libvirt qemu-headless dnsmasq dmidecode
systemctl enable --now libvirtd
usermod -a -G libvirt rocky
pacman -S vagrant
vagrant plugin install vagrant-libvirt
vagrant up

vagrant ssh
export https_proxy='socks5://192.168.56.1:1080'
php81
cd ~/code/
php bin/console about

composer require symfony/orm-pack
composer require --dev symfony/maker-bundle
vim .env
php bin/console doctrine:query:sql "select version();"

mysql homestead < docs/interview_data.sql

php bin/console make:controller
```