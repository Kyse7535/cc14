Commande symfony
================
# Question 1
1. `symfony new --full nomDuProjet`
2. `composer req symfony/webpack-encore-bundle`

# Question 2
1. `symfony console d:d:c`
2. `symfony console make:entity activite`
3. `symfony console make:migration`
4. `symfony doctrine:migrations:migrate`

# Question 3
1. `composer require --dev orm-fixtures`
2. `composer require fakerphp/faker`
3. `symfony console doctrine:fixtures:load`

# Question 4

`symfony console make:crud Activite`

# Question 6

`composer require michelf/php-markdown`

# Question 7

1. `composer require symfony/security-bundle`
2. `symfony console make:user` pour créer un user
3. `symfony console maker:entity user` pour rajouter les attributs nom et prenom

# Question 8
1. `symfony console make:entity activite` pour relier les entités user et activite
2. `symfony console doctrine:schema:drop --force`
3. `symmfony console make:migration`
4. `symfony console doctrine:migrations:migrate `
5. `symfony console doctrine:fixtures:load`

# Question 9
`symfony console make:auth`

# Question 12
`symfony console make:entity Activite` pour rajouter le champ enfants
`symfony console make:migration`
`symfony console doctrine:migrations:migrate`
`symfony console doctrine:fixtures:load`

# Question 15
`symfony console make:crud User`





