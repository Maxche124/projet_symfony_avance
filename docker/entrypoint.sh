#!/bin/sh
set -e

composer install --no-interaction --no-scripts
echo "Installation terminée"

echo "Attente de MySQL..."
until php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
    echo "MySQL pas encore opé..."
    sleep 3
done

echo "MySQL opérationnel - récupère les migrations"
if php bin/console doctrine:migrations:status --no-interaction | grep -q 'Already at latest version'; then
    echo "Pas de migrations en attente"
else
    echo "Des migrations sont disponibles, exécution..."
    php bin/console doctrine:migrations:migrate --no-interaction
fi

if [ "$(php bin/console doctrine:query:sql 'SELECT COUNT(*) FROM user' --no-interaction | grep -Eo '[0-9]+')" -eq 0 ]; then
    echo "Table user vide, chargement des fixtures..."
    php bin/console doctrine:fixtures:load --no-interaction
else
    echo "Données déjà présentes, skip des fixtures"
fi

exec "$@"
