

# Exercice PHP/JS

## Introduction
Projet sous Symfony 4.

## Commandes
### Pré-requis

 - Avoir **Composer** (<https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx>)
 - Avoir **NodeJS** (<https://nodejs.org/en/download/package-manager/>)

### Initialisation du projet
Commencez par créer une base de données et modifier le fichier .env.
```
DATABASE_URL=mysql://<user>:<password>@127.0.0.1:3306/<db-name>
```

Puis, il faut exécuter les commandes suivantes.
```bash
composer install
php bin/console doctrine:migrations:migrate
npm install
```

### Compiler
```bash
npm run dev
```

## L'exercice
Créer, en Symfony, une API Rest qui permet de lister, consulter, ajouter, modifier et de supprimer un auteur, un livre ou un lecteur. Avec HTML & JavaScript, créer les requêtes qui utiliseront l'API et afficher les différentes informations récupérées.
L'entité "Livre" n'est pas créée. Concernant ses relations avec les autres entités :
 - 1 livre a 1 auteur
 - 1 auteur a 0, 1, ou plusieurs livres
 - 1 livre a 0, 1, ou plusieurs lecteurs
 - 1 lecteur a 0, 1, ou plusieurs livre

> **NOTE :** Si vous le souhaitez, vous pouvez ajouter du style.

Utilisez **git** pour versionnez votre exercice.
Envoyer un email à l'adresse suivante : <contact@ze-company.com>. L'email doit contenir le lien vers votre projet sur **GitLab**.

## Liens utiles

 - **Webpack Encore (Symfony)** : <https://symfony.com/doc/current/frontend.html> (pour les fichiers .js et .scss)
 - **MakerBundle (Symfony)** : <https://symfony.com/doc/current/bundles/SymfonyMakerBundle/index.html> (pour générer les entités et les controlleurs)
 - **API Fetch (JavaScript)** : <https://developer.mozilla.org/fr/docs/Web/API/Fetch_API/Using_Fetch> (pour les requêtes à l'API Rest)
