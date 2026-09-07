<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Jeton de déploiement
    |--------------------------------------------------------------------------
    |
    | Jeton attendu dans l'en-tête « X-Deploy-Token » pour déclencher une
    | livraison. Tant qu'il est vide, la route répond 404 : l'environnement
    | local n'expose donc rien.
    |
    */

    'token' => env('DEPLOY_TOKEN'),

];
