# Application banque 2026

## Lancement

- Clonez le dépôt localement
```bash
git clone https://gitlab.univ-lorraine.fr/e14663u/app-banque.git
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
### [Gitlab](https://gitlab.univ-lorraine.fr/e14663u/app-banque)
Toujours réaliser un pull avant de push afin de merge en amont