# Contribuer à ToDo & Co

1. Fork du projet 
Commencez par forker le projet en utilisant le bouton "Fork" en haut à droite de la page du dépôt sur GitHub. Cela créera une copie du projet dans votre compte GitHub.

2. Intallation du projet
Voir le fichier ReadMe.md

3. Tester le projets
Afin de vous assurer que le fichier est bien configurer et que vous êtes dans les conditions idéales pour modifier le codes, lancer une analyse des tests:
```bash
php bin/phpunit
``` 

4. Création d'une branche de fonctionnonalité
```bash
git checkout -b nom-de-la-fonctionnalité 
``` 

5. Effectuer des modifications
Apportez vos modifications au code. N'oubliez pas de suivre les bonnes pratiques de codage et les règles PSR12.

6. Tests
Si vous ajoutez une nouvelle fonctionnalité, assurez-vous d'ajouter des tests correspondants, le code coverage doit être au minimum à 70%. Exécutez les tests existants pour vous assurer que tout fonctionne correctement:
```bash
php bin/phpunit
``` 

7. Soumettre une Pull Request
Une fois que vos modifications sont prêtes, poussez votre branche sur votre fork sur GitHub:
```bash
git push origin nom-de-la-fonctionnalite
``` 
Ensuite, rendez-vous sur la page de votre fork sur GitHub et créez une Pull Request en cliquant sur le bouton "New Pull Request". Décrivez clairement vos changements dans la PR.

8. Revue de code
Les contributeurs existants examineront votre Pull Request. Soyez prêt à apporter des modifications en fonction des commentaires reçus.

9. Fusion de la Pull Request
Une fois que votre Pull Request a été approuvée et testée, elle sera fusionnée dans la branche principale du projet.