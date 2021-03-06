![Logo Segresta](https://github.com/cent89/segresta/raw/master/public/logo_segresta.png)
# SEGRESTA 2.0
Gestionale per il tuo oratorio! Con Segresta puoi gestire in maniera rapida ed efficace tutti gli eventi che organizzi nel tuo oratorio: CRE, Campi estivi, ...
Puoi installare Segresta su qualsiasi server seguendo le istruzioni qui sotto.

## Installazione
Le istruzioni fanno riferimento ad un server web Debian. Puoi trovare ulteriori informazioni sulla pagina di installazione di Laravel (https://laravel.com/docs/5.7).

### Installazione di PHP 7.2 e NGINX
Aggiungi il repository Sury per PHP 7.2:

```
sudo apt install apt-transport-https lsb-release ca-certificates
wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
```

Aggiungi al file ``` /etc/apt/sources.list ``` il repository ``` deb https://packages.sury.org/php/ stretch main ```

Installa tutti i pacchetti per un buon funzionamento di Segresta:

```
sudo apt update
sudo apt install nginx php7.2 composer mariadb-server mariadb-client php7.2-fpm php7.2-cli php7.2-common php7.2-json php7.2-opcache php7.2-mysql php7.2-zip php7.2-mbstring php7.2-gd php7.2-dom php7.2-curl unoconv texlive-extra-utils python-pip
```

### Clone del repo e configurazione

```
sudo mkdir /var/www/segresta
sudo chmod 777 -R /var/www/segresta
cd /var/www/segresta
git init
git remote add origin https://github.com/cent89/segresta.git
git pull origin master
composer update
```

### File .env
Rinomina il file .env.example in .env e modifica i parametri necessari, tra cui la connessione al database.
Ricorda di mettere OWNER_MAIL=admin@email.it
Crea la struttura del database:

```
php artisan module:migrate Oratorio
php artisan module:migrate User
php artisan module:migrate Attributo
php artisan module:migrate Event
php artisan module:migrate Elenco
php artisan module:migrate Group
php artisan module:migrate Subscription
php artisan migrate
```

Crea l'utente amministratore, l'oratorio di prova, crea i ruoli e i permessi:

```
php artisan db:seed
```

Pulisci la cache e crea il link allo storage:

```
php artisan config:cache
php artisan storage:link
```

Genera una chiave per l'applicazione:

```
php artisan key:generate
```

Assegna i giusti permessi alle cartelle

```
sudo chown -R www-data:www-data /var/www/segresta
sudo find /var/www/segresta -type f -exec chmod 644 {} \;
sudo find /var/www/segresta -type d -exec chmod 755 {} \;
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache
```

### Configurazione di Apache
Apri il file /etc/apache2/sites-available/000-default.conf (o crea un vhost apposito) e aggiungi queste righe:

```
<Directory "/var/www/segresta/public">
        AllowOverride All
        Order allow,deny
        Allow from all
        Options +FollowSymLinks
        RewriteBase /segresta
        DirectoryIndex index.php
        AcceptPathInfo on
</Directory>
Alias /segresta "/var/www/segresta/public"
```

Riavvia Apache:

```
sudo service apache2 restart
sudo apt install unoconv texlive-extra-utils
```

Ora puoi utilizzare Segresta, collegandoti a http://localhost/segresta.
