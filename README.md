## Installation

1. Télécharger le projet
3. Modifier le fichier _.env_ et renseigner vos informations de connexion à la base de données
4. Lancer la commande : composer install
5. Créer la base de données avec `php bin/console doctrine:database:create`
6. Appliquer les migrations avec `php bin/console doctrine:migrations:migrate`
7. Insérer les fixtures avec `php bin/console doctrine:fixtures:load`
8. Lancer le serveur `symfony server:start`
