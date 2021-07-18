INTRODUCTION
This api calculates github score terms. It is compliant with the json api standard.
The api is documented as specified by openapi 3. 
It is possible to use not only github services by changing the symfony configuration.

USAGE
1. Make git clone and setup configure files.
2. php bin/console doctrine:database:create
3. php bin/console make:migration
4. php bin/console doctrine:migrations:migrate
5. php bin/console --env=test doctrine:database:create
6. php bin/console --env=test doctrine:schema:create
7. composer install