# projet-12-helpers-elders

Le projet est déployé sur main : les features sont sur la branche develop

# Installation de Symfony

`symfony new projet-12-helpers-elders-back --version=5.4 --webapp`

## installation des dépendances

`composer install`

## installation de Symfony client sous Ubuntu Debian (optionnel : permet d’utiliser les commandes Symfony)

`curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | sudo -E bash`
puis
`sudo apt install symfony-cli`

## Création de la base de données

dubliquer le .env et en faire un .env.local.
Commenter le postresql.
Décommenter le mysql et saisir les identifiants / mdp, nom de la base de données et les nom et version du serveur de BDD

`# DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
DATABASE_URL="mysql://identifiant_database:password_database@127.0.0.1:3306/database_name?serverVersion=MariaDB-version_MariaDB&charset=utf8mb4"
# DATABASE_URL="postgresql://app:nom_de_la_bdd@127.0.0.1:5432/app?serverVersion=15&charset=utf8"
###< doctrine/doctrine-bundle ###`


`php bin/console doctrine:database:create`  (ou symfony console doctrine:database:create) 

puis

`php bin/console doctrine:migrations:migrate` (ou symfony console doctrine:migrations:migrate) -> vous permet de charger les migrations que nous avons créées

`php bin/console make:migration` -> (a priori vous n'en n'avez pas besoin : permet de générer de nouvelles migration)

## Installation de bundle de fixtures

`composer require --dev doctrine/doctrine-fixtures-bundle`

## Chargement des fixtures en BDD

`php bin/console doctrine:fixtures:load`-> répondre « yes » pour effacer les données

## Token
`composer require lexik/jwt-authentication-bundle`

`php bin/console lexik:jwt:generate-keypair`

copier les 3 lignes jwt du .env et les mettre dans le env.local en les décommentants

`###> lexik/jwt-authentication-bundle ###
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=2e2e77ccffda8495fa95d0817d834544
###< lexik/jwt-authentication-bundle ###`


## Démarrer le serveur

`symfony server start`
ou 
`php bin/console 0.0.0.0:8000`


