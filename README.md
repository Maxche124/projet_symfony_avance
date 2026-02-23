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
    - Email `jean.dupont@example.com`
    - Mot de passe `password123`
  - Client/Utilisateur simple (ROLE_USER)
    - Email `marie.martin@example.com`
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
# remplaçais bien {container id}
docker exec -it {container id} bash
```
Vous pouvez aussi utiliser `sh` au lieu de `bash`

## Organisation
### [Gitlab](https://gitlab.univ-lorraine.fr/e14663u/app-banque)
Toujours réaliser un pull avant de push afin de merge en amont
Les commit sont normalement publié instantanément sur le discord du projet mais cela n'exclus pas une seconde vérification par un pull/fetch
### [Trello](https://trello.com/b/O1INTRkS/but3qualdevsuivi7)
Pour éviter de travailler a deux sur une même feature ou amélioration veillez à vous saisir d'une carte kamban sur Trello
Vous pouvez également déclaré votre prise d'activité sur discord
### Projet legacy
Le projet legacy est une bonne base de travail, nous voulons améliorer l'existant mais retrouver ce qui fonctionnait  
#### [Github](https://github.com/Kyusaor/BUT3_QUALDEV_7)
#### [Documentation](https://documentation-gp7-qualite-dev.netlify.app/installation%20et%20configuration/)
#### [SonarCloud](https://sonarcloud.io/project/overview?id=Kyusaor_BUT3_QUALDEV_7)

## Limitations
### Docker
Pour le moment le docker compose ne permet pas de monter un environnement de développement, nous souhaitons palier à ça à l'avenir pour streamliner le développement.

### Style
À ce jour le style de l'application est très rudimentaire et n'est pas responsive, ce à quoi nous souhaitons remédier dans le futur.