# boston-abundance-api
## Deployment
### 1. Install Apache Web Server
- verify that an Apache system service exists.

  `sudo systemctl status apache2`

- Install Apache.

  `sudo apt install apache2`

- if you are using a firewall, it is necessary to establish a rule.

    `sudo ufw allow “Apache Full”`

- After that, we can check the Apache service status again.

    `sudo systemctl status apache2`

### 2. Install PHP and extensions
`apt-get update`

`sudo apt install php libapache2-mod-php php-mbstring php-xmlrpc php-soap php-gd php-xml php-cli php-zip php-bcmath php-tokenizer php-json php-pear`

### 3. Install SqlSrv on PHP
`sudo apt update`

`sudo apt install php-pear php-dev`

`curl -s https://packages.microsoft.com/keys/microsoft.asc | sudo apt-key add -`

`sudo bash -c "curl -s https://packages.microsoft.com/config/ubuntu/20.04/prod.list > /etc/apt/sources.list.d/mssql-release.list"`

`sudo apt update`

`sudo ACCEPT_EULA=Y apt -y install msodbcsql17 mssql-tools`

`sudo apt -y install unixodbc-dev`

`sudo apt -y install gcc g++ make autoconf libc-dev pkg-config`

`sudo pecl install sqlsrv`

`sudo pecl install pdo_sqlsrv`

`printf "; priority=20\nextension=sqlsrv.so\n" > /etc/php/7.4/mods-available/sqlsrv.ini`

`printf "; priority=30\nextension=pdo_sqlsrv.so\n" > /etc/php/7.4/mods-available/pdo_sqlsrv.ini`

`sudo phpenmod -v 7.4 sqlsrv pdo_sqlsrv`

`sudo service php7.4-fpm restart`

### 4. Install Composer
- download Composer.

`curl -sS https://getcomposer.org/installer | php`

- make sure Composer can be used globally and make it executable.

`sudo mv composer.phar /usr/local/bin/composer`
`sudo chmod +x /usr/local/bin/composer`

### 5. Configure Project
- clone project

`cd /var/www && git clone https://github.com/pyphist/boston-abundance-api.git`

- install composer packages

`cd /var/www/boston-abundance-api  && sudo composer install`

`php artisan l5-swagger:generate`


- allow permissions

`sudo chgrp -R www-data /var/www/html/boston-abundance-api/`
`sudo chmod -R 777 /var/www/boston-abundance-api/storage`

### 6. Apache Configuration
- open apache conf file

`cd /etc/apache2/sites-available`
`sudo nano 000-default.conf`

- replace all with following code

```
<VirtualHost *:80>
   ServerName thedomain.com
   ServerAdmin webmaster@thedomain.com
   DocumentRoot /var/www/laravel-ksr-boston_abundance/public

   <Directory /var/www/laravel-ksr-boston_abundance>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
   </Directory>
   ErrorLog ${APACHE_LOG_DIR}/error.log
   CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```
- Enable the Apache rewrite module, and finally, restart the Apache service

`sudo a2enmod rewrite`
`sudo systemctl restart apache2`

### 7. Access Project on browser
Congratulations, the installation is complete. you can now enter the IP in the browser and view the page.
