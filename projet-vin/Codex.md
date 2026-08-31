CODEX.md — WineStock
1. Présentation du projet

WineStock est une application web simple de gestion de stock pour vins, champagnes et spiritueux.

L'application doit être extrêmement simple à utiliser par du personnel non technique.

L'objectif principal est de permettre rapidement de :

consulter le stock ;
ajouter un produit ;
enregistrer une entrée ;
enregistrer une sortie ;
effectuer un inventaire ;
consulter l'historique des mouvements ;
identifier les stocks faibles et les ruptures.

Ne pas transformer le projet en ERP.

La simplicité métier et la facilité de prise en main sont prioritaires.

2. Stack technique

Utiliser uniquement :

Laravel
Inertia.js
React
Tailwind CSS
MySQL
Vite

Architecture :

Laravel monolithique avec React via Inertia.

Ne pas créer :

de frontend React séparé ;
d'API REST inutile ;
de microservices ;
de serveur Node.js en production ;
de système complexe de state management.

Laravel doit gérer :

routes ;
authentification ;
logique métier ;
validation ;
base de données ;
permissions.

React doit principalement gérer l'interface utilisateur.

3. Hébergement

L'application sera déployée sur un hébergement mutualisé LWS.

Le projet doit donc rester compatible avec un hébergement PHP classique.

Ne pas introduire de dépendance nécessitant :

Docker en production ;
Redis obligatoire ;
WebSockets ;
serveur Node permanent ;
queue worker permanent ;
infrastructure cloud complexe.

Les assets React seront compilés avec Vite avant le déploiement.

4. Philosophie du projet

Toujours privilégier :

simplicité ;
lisibilité ;
maintenabilité ;
rapidité d'utilisation ;
faible nombre de clics.

Une action fréquente doit idéalement nécessiter maximum 2 ou 3 clics.

Ne pas ajouter une fonctionnalité qui n'a pas été explicitement demandée.

Avant d'introduire une abstraction ou une dépendance, vérifier si Laravel natif suffit.

Éviter la surarchitecture.

5. Navigation principale

L'application doit principalement contenir :

Accueil
Produits
Stock
Inventaire

Menu secondaire :

Utilisateurs
Paramètres
Déconnexion

Sur mobile, privilégier une navigation simple adaptée aux écrans tactiles.

6. Authentification

L'utilisation quotidienne privilégie une connexion par code PIN à 4 chiffres.

Flux :

sélectionner l'utilisateur ;
saisir le PIN ;
accéder à l'application.

Le PIN ne doit jamais être stocké en clair.

Utiliser le système de hash Laravel.

Prévoir une limitation des tentatives de connexion.

Ne jamais afficher le PIN existant dans l'interface.

7. Rôles

Rester simple.

Rôles initiaux :

Admin

Accès complet.

Magasinier

Peut :

consulter les produits ;
ajouter/modifier les produits ;
effectuer des entrées ;
effectuer des sorties ;
réaliser les inventaires ;
consulter les mouvements.
Vendeur

Peut principalement :

consulter les produits ;
consulter le stock ;
enregistrer des sorties autorisées.

Ne pas créer un système complexe de dizaines de permissions sans besoin métier réel.

8. Produits

Un produit doit rester simple.

Champs principaux :

nom ;
catégorie ;
marque ;
millésime facultatif ;
contenance ;
prix d'achat ;
prix de vente ;
nombre de bouteilles par carton ;
stock actuel ;
stock minimum ;
photo facultative ;
code-barres facultatif.

Ne pas ajouter pour le MVP :

fournisseurs ;
commandes fournisseurs ;
CRM ;
clients ;
comptabilité ;
gestion complexe des lots.
9. Unité de stock

L'unité de référence interne est la bouteille.

Ne pas maintenir séparément :

stock de cartons ;
stock de bouteilles.

Un carton est uniquement une unité de saisie.

Exemple :

Si :

1 carton = 12 bouteilles

et l'utilisateur saisit :

3 cartons

alors :

3 × 12 = 36 bouteilles

Le stock doit augmenter de 36.

À l'affichage, il est possible de convertir :

43 bouteilles

en :

3 cartons + 7 bouteilles.

Mais la base doit conserver une seule quantité de stock de référence.

10. Mouvements de stock

Les entrées, sorties et ajustements doivent utiliser une logique commune de mouvement de stock.

Types :

IN
OUT
ADJUSTMENT

Chaque mouvement doit conserver :

produit ;
utilisateur ;
type ;
quantité ;
motif ;
stock avant ;
stock après ;
note facultative ;
date.

Motifs possibles pour une sortie :

Vente
Casse
Perte
Consommation interne
Cadeau
Autre
11. Modification du stock

Lorsqu'un mouvement est enregistré :

récupérer le stock actuel ;
calculer le nouveau stock ;
vérifier les règles métier ;
enregistrer le mouvement ;
mettre à jour le stock du produit.

Ces opérations doivent être exécutées dans une transaction de base de données.

Ne jamais permettre un stock négatif sauf décision métier explicite.

12. Entrée de stock

L'écran doit être très simple.

Champs :

produit ;
quantité ;
unité : bouteille ou carton ;
prix d'achat facultatif ;
commentaire facultatif.

Afficher avant validation :

stock actuel ;
quantité ajoutée ;
nouveau stock.

Exemple :

Stock actuel : 24

3 cartons × 12 bouteilles = 36

Nouveau stock : 60

13. Sortie de stock

Champs :

produit ;
quantité ;
unité ;
motif ;
commentaire facultatif.

Afficher :

stock actuel ;
quantité retirée ;
stock restant.

Empêcher une sortie supérieure au stock disponible.

14. Inventaire

L'inventaire doit comparer :

stock théorique ;
stock physique ;
écart.

Exemple :

Produit : Moët Brut

Stock théorique : 24

Stock physique : 22

Écart : -2

Lors de la validation de l'inventaire, créer automatiquement un mouvement de type ADJUSTMENT pour chaque différence.

Conserver l'historique de l'inventaire.

15. Dashboard

Le dashboard doit rester très simple.

Afficher principalement :

nombre total de bouteilles ;
nombre de produits ;
nombre de stocks faibles ;
nombre de ruptures ;
derniers mouvements.

Actions rapides visibles :



Entrée


Sortie

Éviter les graphiques inutiles.

Ne pas surcharger le dashboard.

16. Stock faible

Un produit est considéré comme ayant un stock faible lorsque :

stock_quantity <= minimum_stock

Un produit est en rupture lorsque :

stock_quantity = 0

Utiliser des badges simples :

vert : stock normal ;
orange : stock faible ;
rouge : rupture.
17. Interface utilisateur

L'interface doit être :

mobile-first ;
responsive ;
utilisable sur téléphone ;
utilisable sur tablette ;
utilisable sur ordinateur ;
adaptée aux écrans tactiles.

Favoriser :

gros boutons ;
formulaires courts ;
textes lisibles ;
tableaux simples ;
feedback visuel clair ;
confirmations uniquement pour les actions importantes.

Éviter :

les modales imbriquées ;
les menus complexes ;
les formulaires très longs ;
les animations inutiles.
18. React

Utiliser des composants React simples et réutilisables.

Exemples :

Button
Input
Select
Modal
Badge
ProductCard
StockStatus
PageHeader

Ne pas créer un système de composants excessivement abstrait.

Ne pas utiliser Redux ou une autre librairie de state management sans besoin réel.

Utiliser les données Inertia fournies par Laravel.

19. Laravel

Respecter les conventions Laravel.

Utiliser :

migrations ;
modèles Eloquent ;
Form Requests lorsque pertinent ;
middleware ;
policies ou logique d'autorisation simple ;
transactions DB pour les opérations sensibles.

Éviter les contrôleurs gigantesques.

Déplacer la logique métier importante dans des classes dédiées lorsque cela améliore réellement la lisibilité.

Exemple :

StockMovementService

Mais ne pas créer un service pour chaque petite opération.

20. Base de données initiale

Tables principales :

users
categories
products
stock_movements
inventories
inventory_items
settings

Éviter d'ajouter des tables sans nécessité.

21. Nommage

Code en anglais.

Interface utilisateur en français.

Exemple :

Code :

Product
StockMovement
Inventory

Interface :

Produit
Mouvement de stock
Inventaire

Utiliser des noms explicites.

Éviter les abréviations obscures.

22. Validation

Toute donnée utilisateur doit être validée côté Laravel.

React peut effectuer une validation d'interface supplémentaire, mais Laravel reste la source de vérité.

Toujours afficher les erreurs de validation clairement à proximité des champs concernés.

23. Sécurité

Toujours :

utiliser CSRF Laravel ;
valider les données ;
vérifier les autorisations ;
hasher les PIN ;
utiliser les transactions pour les modifications de stock ;
empêcher les modifications non autorisées ;
conserver la traçabilité des mouvements.

Ne jamais faire confiance aux valeurs envoyées par le frontend.

24. Suppression des données

Éviter de supprimer définitivement les mouvements de stock.

Un mouvement constitue un historique métier.

Si une erreur est faite, préférer un mouvement correctif plutôt qu'effacer silencieusement l'historique.

Pour les produits déjà utilisés dans des mouvements, éviter la suppression définitive.

Préférer un statut actif/inactif lorsque nécessaire.

25. Ce qu'il ne faut PAS développer actuellement

Ne pas développer sauf demande explicite :

gestion des fournisseurs ;
commandes fournisseurs ;
clients ;
CRM ;
facturation ;
caisse complète ;
comptabilité ;
paiement en ligne ;
marketplace ;
e-commerce ;
système de fidélité ;
microservices ;
API publique ;
application Android native ;
application iOS native ;
intelligence artificielle ;
notifications push ;
WebSockets ;
système complexe de permissions.
26. Méthode de travail avec Codex

Avant toute modification :

lire les fichiers concernés ;
comprendre l'architecture actuelle ;
identifier la modification minimale nécessaire ;
ne modifier que les fichiers réellement concernés.

Ne pas réécrire des fichiers entiers lorsque quelques lignes suffisent.

Ne pas modifier une fonctionnalité existante sans raison.

Ne pas introduire une nouvelle dépendance sans nécessité.

Lorsqu'une erreur existe :

identifier sa cause ;
expliquer brièvement la cause ;
corriger la cause réelle ;
ne pas masquer simplement l'erreur.
27. Lorsqu'une fonctionnalité est demandée

Procéder dans cet ordre :

analyser la demande ;
identifier les tables concernées ;
vérifier les routes existantes ;
vérifier les modèles ;
vérifier la logique métier ;
créer ou modifier le contrôleur ;
créer ou modifier l'interface React ;
ajouter la validation ;
vérifier les autorisations ;
tester le scénario principal ;
tester les cas d'erreur.
28. Principe final

WineStock doit rester un petit logiciel métier.

Un utilisateur doit pouvoir comprendre les principales fonctions en quelques minutes.

Le flux principal est :

Connexion PIN
→ Accueil
→ Produit
→ Entrée / Sortie
→ Stock mis à jour
→ Inventaire

Si une solution simple fonctionne correctement, ne pas la remplacer par une solution plus complexe uniquement pour rendre l'architecture plus sophistiquée.

La simplicité est une contrainte du projet, pas un manque de fonctionnalités.