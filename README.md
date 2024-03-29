# Tom McFarlane's web API
To run this project, you will need [docker](https://docs.docker.com/get-docker/) to be installed.

This project uses:
- Docker
- PHP 7.4
- Composer

Once Docker is installed, run:
- `docker-compose up --build`
- `./docker/tools/composer install` - run composer in the container and pass it arguments
- You will also need to copy the `.env` and `phinx.yml`

Once the above has been done, go to [http://localhost:8080/](http://localhost:8080/) - to access the site.

## Database commands
Migrations are done through the [Phinx](https://book.cakephp.org/phinx/0/en/intro.html) library.
I have used this as a learning exercise as I wanted to create my own 'ORM' as such. 
To run migrations, exec into the container `./docker/tools/php`: 
- run `./docker/tools/composer migration-migrate` - migrate migration
- run `./docker/tools/composer migration-create MyNewMigration` - create a new migration
- run `./docker/tools/composer migration-rollback` - rollback migrations
- for more migration documentation, go to [Phinx](https://book.cakephp.org/phinx/0/en/intro.html) for more information

## PHPStan commands
- [PHPStan](https://phpstan.org/user-guide/getting-started) is a static analyser which helps to detect type safety and 
ensure your code is valid before it even runs. This reduces chances of bugs and helps improve coding standard overall.
- run `./docker/tools/composer phpstan src` - validate `src` directory
- run `./docker/tools/composer phpstan tests` - validate `tests` directory 
- for more migration documentation, go to [PHPStan](https://phpstan.org/user-guide/getting-started) for more information

## PHP_CodeSniffer
- [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) is a PSR compliant standard library. 
This reduces chances of bugs and helps improve coding standard overall.
- run `./docker/tools/composer phpcs` - detect PHP coding errors
- run `./docker/tools/composer phpcbf` - automatically fix coding standards
- for more migration documentation, go to [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) for more information

## Contributing to the project
- Must be PSR compliant
- Must use OOP PHP 7.4 to code
- Before pushing up, ensure code is compliant with PHPStan and meets the standard

## Configuring xdebug
- Configuring xdebug has been made simple and it can be done through docker. To configure it, please follow the commands
- `Preferences > Languages & Frameworks > PHP > Debug` - configure `Xdebug Port` to be 9000
- `Preferences > Languages & Frameworks > PHP > Server` - create a new server and configure to the below
- `host` - localhost
- `port` - 8080
- `Debugger` - xdebug
- `Path mappings` - convert the root of the project to `/var/www/`
- `Run > Edit Configurations` - create a `PHP Remote Debug` - select your server name and add in the key `PHPSTORM`
- Set `Break at First line` and it will start listening if the chrome extension is set up.
