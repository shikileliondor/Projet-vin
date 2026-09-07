# WineStock

Application simple de gestion de stock pour vins, champagnes et spiritueux.

## Installation locale

Prérequis : PHP 8.3+, Composer, Node.js, npm et MySQL.

1. Copier `.env.example` vers `.env` et renseigner les accès MySQL.
2. Installer les dépendances avec `composer install` puis `npm install`.
3. Générer la clé avec `php artisan key:generate`.
4. Créer les tables et le compte initial avec `php artisan migrate --seed`.
5. Créer le lien des photos avec `php artisan storage:link`.
6. Compiler l'interface avec `npm run build`.

Les seeders ajoutent un compte `Administrateur` avec le PIN `1234`, cinq catégories, cinq produits de démonstration, leurs stocks initiaux et les paramètres de base. Modifiez ce PIN dès la première connexion depuis la page Utilisateurs.

## Développement

Lancer Laravel avec `php artisan serve` et Vite avec `npm run dev`.

Vérifications principales :

```bash
composer test
npm run types:check
npm run check -- resources/js
npm run build
```

## Déploiement LWS

Le déploiement est automatique : un push sur `main` lance les vérifications puis,
si elles passent, compile l'application, l'envoie en FTPS et joue les migrations.
La procédure d'installation complète est décrite dans
[docs/DEPLOIEMENT-LWS.md](docs/DEPLOIEMENT-LWS.md).

- Pointer le domaine vers le dossier `public` de Laravel.
- Utiliser MySQL et PHP 8.3 ou supérieur.
- `vendor` et `public/build` sont construits par GitHub Actions : ni Composer ni Node.js ne sont requis sur l'hébergement.
- Conserver `QUEUE_CONNECTION=sync` : aucun worker permanent n'est requis.
- Donner les droits d'écriture nécessaires à `storage` et `bootstrap/cache`.

La quantité de référence en base est toujours la bouteille. Les cartons servent uniquement d'unité de saisie.
