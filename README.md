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

dubliquer le .env et en faire un .env.local



`php bin/console doctrine:database:create`  (ou symfony console doctrine:database:create) 

puis

`php bin/console doctrine:migrations:migrate` (ou symfony console doctrine:migrations:migrate) -> vous permet de charger les migrations que nous avons créées

`php bin/console make:migration` -> (a priori vous n'en n'avez pas besoin : permet de générer de nouvelles migration)

## Installation de bundle de fixtures

`composer require --dev doctrine/doctrine-fixtures-bundle`

## Chargement des fixtures en BDD

`php bin/console doctrine:fixtures:load`-> répondre « yes » pour effacer les données
