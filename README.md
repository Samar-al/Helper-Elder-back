# projet-12-helpers-elders-back 

# Installation Back : commandes

## installation des dépendances
`composer install`

## installation de Symfony client sous Ubuntu Debian (optionnel : permet d’utiliser les commandes Symfony)
`curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | sudo -E bash`
puis
`sudo apt install symfony-cli`

## Création de la base de données
`php bin/console doctrine:database:create`  (ou symfony console doctrine:database:create) 

puis

`php bin/console doctrine:migrations:migrate` (ou symfony console doctrine:migrations:migrate)

## Installation de bundle de fixtures
`composer require --dev doctrine/doctrine-fixtures-bundle`

## Chargement des fixtures en BDD
`php bin/console doctrine:fixtures:load`-> répondre « yes » pour effacer les données
