# HetznerDynDNS
Create a DynDNS-Service with Hetzner DNS service


Supported DDNS protocols:
----------------------
 * cron/wget
 * dyndns2

Installation:
-------------
```
apt install php-cli php-curl php-mysql mariadb-server apache2 libapache2-mod-php

mkdir /var/ddns
cd /var/ddns
git clone https://github.com/nemiah/HetznerDynDNS.git

cd HetznerDynDNS

ln -s /var/ddns/HetznerDynDNS/update.php /var/www/html/update.php
ln -s /var/ddns/HetznerDynDNS/checkip.php /var/www/html/checkip.php

mysql -uroot -p < setup.sql

dig @localhost nemiah.de
```

IP update:
----------
Via cron/wget:
```
wget -qO- https://nemiah.de/update.php?domain=home.nemiah.de&username=nena&password=Hallo123&ip=123.123.123.123 &> /dev/null
dig @localhost home.nemiah.de
```

Via ddclient:
```
# /etc/ddclient.conf

protocol=dyndns2
use=web, web=nemiah.de/checkip.php
server=nemiah.de/update.php
login=nena
password='Hallo123'
home.nemiah.de
```
