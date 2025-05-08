## Installation

1. Télécharger le projet
2. Modifier le fichier _.env_ et renseigner vos informations de connexion à la base de données
3. Créer la base de données avec `php bin/console doctrine:database:create`
4. Appliquer les migrations avec `php bin/console doctirne:migrations:migrate`
5. Insérer les fixtures avec `php bin/console doctrine:fixtures:load`
6. Lancer le serveur