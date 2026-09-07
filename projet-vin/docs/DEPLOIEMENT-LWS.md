# Deploiement automatique sur LWS / cPanel via FTPS

Le SSH externe est bloque sur l'hebergement, donc le deploiement passe par FTPS.
GitHub Actions construit l'application, envoie les fichiers sur cPanel, puis
appelle une route protegee pour lancer les commandes Laravel.

```text
push sur main
   -> tests
      -> deploy
         -> composer install --no-dev
         -> npm ci && npm run build
         -> upload FTPS vers cPanel
         -> POST /deploy/run
            -> optimize:clear
            -> migrate --force
            -> storage:link --force
            -> optimize
```

Node et Composer ne sont pas necessaires sur l'hebergement : `vendor/` et
`public/build/` sont construits par GitHub Actions puis envoyes par FTPS.

## 1. Chemin serveur

Le dossier Laravel actuel est :

```text
/home/c2860566c/public_html/winestock.studiobeyam.net/c2860566c/winestock
```

Son dossier public est :

```text
/home/c2860566c/public_html/winestock.studiobeyam.net/c2860566c/winestock/public
```

Le domaine doit pointer vers le dossier `public`, pas vers la racine Laravel.
Sinon `.env`, `vendor/` ou `storage/` peuvent etre exposes.

## 2. Fichier `.env` serveur

Le fichier existe deja ici :

```text
/home/c2860566c/public_html/winestock.studiobeyam.net/c2860566c/winestock/.env
```

Il doit contenir au minimum :

```dotenv
APP_NAME=WineStock
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://winestock.studiobeyam.net

APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr

LOG_CHANNEL=stack
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=c2860566c_...
DB_USERNAME=c2860566c_...
DB_PASSWORD=...

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
CACHE_STORE=database
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
MAIL_MAILER=log

DEPLOY_TOKEN=...
```

Genere `DEPLOY_TOKEN` en local :

```powershell
php -r "echo bin2hex(random_bytes(32));"
```

La meme valeur doit etre mise dans :

```text
LWS_DEPLOY_TOKEN
```

sur GitHub.

## 3. Secrets GitHub

Dans GitHub : **Settings -> Secrets and variables -> Actions -> New repository
secret**.

| Secret | Valeur |
| --- | --- |
| `LWS_FTP_SERVER` | Hote FTP/FTPS fourni par LWS |
| `LWS_FTP_USERNAME` | Utilisateur FTP |
| `LWS_FTP_PASSWORD` | Mot de passe FTP |
| `LWS_FTP_DIR` | Dossier FTP distant, avec slash final |
| `LWS_DEPLOY_URL` | `https://winestock.studiobeyam.net/deploy/run` |
| `LWS_DEPLOY_TOKEN` | Meme valeur que `DEPLOY_TOKEN` dans le `.env` serveur |

Pour `LWS_FTP_DIR`, utilise le chemin FTP qui correspond au dossier Laravel :

```text
/public_html/winestock.studiobeyam.net/c2860566c/winestock/
```

Si ton compte FTP est deja limite au dossier `winestock`, mets simplement :

```text
/
```

## 4. Premiere livraison

1. Verifie que `.env` existe sur le serveur.
2. Verifie que `DEPLOY_TOKEN` est dans `.env`.
3. Verifie que `LWS_DEPLOY_TOKEN` a exactement la meme valeur.
4. Lance **Actions -> deploy -> Run workflow** dans GitHub.

Le premier envoi peut etre long, car `vendor/` est transfere en entier.

## 5. Seed initial

Si tu dois creer les donnees de depart une seule fois, lance dans le terminal
cPanel :

```bash
cd /home/c2860566c/public_html/winestock.studiobeyam.net/c2860566c/winestock
/usr/local/bin/php artisan db:seed --force
```

Ensuite connecte-toi avec le compte initial, puis change immediatement le PIN ou
le mot de passe par defaut.

## 6. Depannage

| Symptome | Cause probable |
| --- | --- |
| `530 Login authentication failed` | Mauvais utilisateur ou mot de passe FTP |
| `FTPS connection failed` | Essaie `protocol: ftp` dans le workflow, ou verifie le mode FTPS chez LWS |
| `404` sur `/deploy/run` | `DEPLOY_TOKEN` absent du `.env`, ou different du secret GitHub |
| `500` sur `/deploy/run` | Regarde `storage/logs/laravel.log` sur le serveur |
| Styles absents | `public/build/` non envoye ou domaine mal pointe |
| Photos en 404 | `storage:link --force` a echoue ou les droits bloquent le lien |
| Fichiers sensibles visibles | Le domaine ne pointe pas vers `public/` |
