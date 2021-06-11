# HetznerDynDNS
Create a DynDNS-Service with Hetzner DNS service


Supported DDNS protocols:
----------------------
 * cron/wget
 * dyndns2

Installation:
-------------
```
apt install php-cli php-curl php-mysql mariadb-server apache2 libapache2-mod-php git certbot python3-certbot-apache

mkdir /var/ddns
cd /var/ddns
git clone https://github.com/nemiah/HetznerDynDNS.git

cd HetznerDynDNS

nano update.php
#insert Hetzner API token

ln -s /var/ddns/HetznerDynDNS/update.php /var/www/html/update.php
ln -s /var/ddns/HetznerDynDNS/checkip.php /var/www/html/checkip.php

mysql -uroot -p < setup.sql
```

IP update:
----------
Via cron/wget:
```
wget -qO- https://dns.nemiah.de/update.php?domain=test.nemiah.de&username=nena&password=Hallo123&ip=123.123.123.123 &> /dev/null
dig test.nemiah.de
```

Via ddclient:
```
# /etc/ddclient.conf

protocol=dyndns2
use=web, web=dns.nemiah.de/checkip.php
server=dns.nemiah.de/update.php
login=nena
password='Hallo123'
test.nemiah.de
```
