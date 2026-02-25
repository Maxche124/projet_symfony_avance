# Application banque 2026

## Lancement

- Clonez le dépôt localement
```bash
git clone https://github.com/Maxche124/projet_symfony_avance
```
- Composez le projet sur Docker via le fichier *compose.yaml* présent à la racine
```bash
# ou à la racine du projet 
docker compose up
```
- Attendez que les conteneurs soient opérationnels (au premier lancement, cela peut prendre un peu de temps sous
  windows le temps de récupérer toutes les dépendances)
- Allez sur http://localhost:8085 pour voir l'application
- Les identifiants de connexions test sont généré dans les fixtures `/src/DataFixtures/AppFixtures.php` au cas où les suivants ne sont plus d'actualité
  - Gestionnaire (ROLE_MANAGER)
    - Email `manager@example.com`
    - Mot de passe `password123`
  - Administrateur (ROLE_ADMIN)
    - Email `admin@example.com`
    - Mot de passe `password123`
  - Client/Utilisateur simple (ROLE_USER)
    - Email `user@example.com`
    - Mot de passe `password123`

## Développement

- Vous pouvez monter rapidement un environnement de développement à la racine du projet via un terminal dédié
```bash
docker run -it --rm `
    -v "${PWD}:/workspace" `
    -w /workspace `
    -u root `
    --name symfony-dev `
    --entrypoint /bin/bash `
    fbraz3/php-composer:8.4 `
    -c "git config --global --add safe.directory /workspace && exec /bin/bash"
```
Vous aurez alors un conteneur avec PHP 8.4 et composer. Quand vous avez terminé de développé vous pouvez simplement quitter le terminal.

- Si vous n'avez pas déjà importé les dépendances
```bash
composer update
# ou
composer install
```

## Fonctionnalités

L'application possède un panneau de navigation à gauche permettant de changer la langue (boutons drapeaux), de se connecter et d'accéder aux différentes pages du site.

Une fois connecté avec l'un des compte fournis ci-dessus, différentes pages seront accessibles : 
- En tant qu'utilisateur simple (ROLE_USER), seule la liste des produits sera accessible, sans possibilité d'accéder au CRUD (sauf les détails de chaque produit)
- En tant que manager (ROLE_MANAGER), tout sera accessible, à savoir la liste des produits, celle des utilisateurs, la possibilité de les gérer (boutons détails, suppression, création et modification disponibles) et un bouton en bas de la liste des produits permettant de l'exporter en CSV.

Une commande pour importer un fichier CSV dans la base de données est également disponible. Pour l'utiliser, ouvrir le projet dans un terminal ou dans un IDE puis entrer dans ledit Terminal la commande : 

```bash
php bin/console app:produit:import produits.csv
```

### Rappel

- lister les container
```bash
docker ps -a
```
- ouvrir un terminal d'un container ouvert _(notamment utile pour accéder au container du compose)_
```bash
# remplacez bien {container id}
docker exec -it {container id} bash
```
Vous pouvez aussi utiliser `sh` au lieu de `bash`

## Organisation
### [Github]([https://github.com/Maxche124/projet_symfony_avance/])

Toujours réaliser un pull avant de push afin de merge en amont

