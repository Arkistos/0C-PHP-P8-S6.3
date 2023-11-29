# ToDo & Co \n

Bienvenue dans ToDo & Co, un projet Symfony de gestion de tâches et de listes de choses à faire. Ce document vous guidera à travers le processus d'installation du projet sur votre machine locale.

### Prérequis
Assurez-vous d'avoir les éléments suivants installés sur votre machine:

-   PHP 7.4 ou une version ultérieure
-   Composer (gestionnaire de dépendances pour PHP)
-   Symfony CLI (facultatif, mais recommandé)

### Installation 
1.  Clonnez le repository
```bash
git clone https://github.com/Arkistos/0C-PHP-P8-S6.3.git
```

2.  Déplacez-vous dans le répertoire du projet
```bash
cd 0C-PHP-P8-S6.3
```

3.  Installez les dépendances
```bash
composer install
```

4.  Configurez les variables d'environnements
Dupliquez le fichier .env en .env.local et configurez les paramètres liés à votre environnement (base de données, etc.).

5.  Créez la base de donnée
```bash
php bin/console doctrine:database:create
```

6.  Migrez le schéma de la base de donnée 
```bash
php bin/console doctrine:migration:migrate
```

7.  Chargez les données de démonstrations
```bash
php bin/console doctrine:fixtures:load
```

8.  Lancer le serveur Symfony
```bash
symfony server:start
```

Le serveur de développement sera accessible à l'adresse http://localhost:8000.
