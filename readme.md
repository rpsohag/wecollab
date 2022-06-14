Wecollab
========

<p align="center">
	<a href="https://git.we-com.info/wecollab/wecollab/-/commits/master"><img alt="Pipeline Status" src="https://git.we-com.info/wecollab/wecollab/badges/master/pipeline.svg" /></a>
	<a href="https://git.we-com.info/wecollab/wecollab/-/commits/master"><img alt="Coverage Report" src="https://git.we-com.info/wecollab/wecollab/badges/master/coverage.svg" /></a>
</p>

# Prima installazione
```
git clone git@git.we-com.info:wecollab/wecollab.git 

rm -rf .htaccess
rm -rf public/.htaccess
cp ../../../pcappaspina/progetti/wecollab/.htaccess .
cp ../../../pcappaspina/progetti/wecollab/public/.htaccess public/
cp ../../../pcappaspina/progetti/wecollab/.env .

chmod -R 777 storage/
chmod -R 777 bootstrap/cache/
mkdir public/uploads
mkdir public/thumbs
chmod 775 public/uploads
chmod 775 public/thumbs

rm -rf vendor
rm -rf composer.lock
php7.1 composer.phar install # se versioni vecchie
composer install # se ultima versione
# editare il file .env cambiando il nome utente sull' APP_URL (ad. es ~pcappaspina con ~atimperi)
```
 
 

# se si vuole forzare un pacchetto che richiede php 7.2  
```
php7.2 composer.phar require php-imap/php-imap "^4.1"
```
```
php7.1 composer.phar require php-imap/php-imap "^3.1.0"

```


# messa in produzione delle modifiche
Dal branch develop
```
git pull
git flow release start v4.0.x
```

Modifica del file "conig/app.php" aggiornando la nuova versione

```
git add . && git commit -m "Bump to version v4.0.x"
git flow release finish v4.0.x
```


# creazione hotfix
```
git checkout develop
git pull
git checkout master
git pull
git flow hotfix start [nome]
effettuare modifiche
git add && git commit
git flow hotfix finish [nome]
```
