# API boilerplate

This API is designed with hexagonal architecture, DDD and CQRS.
This boilerplate embeds an authentication and authorization by JWT for a user.

This project can run either with the standard web development sever or with the nginx sever configured in docker which I highly recommend.

> Note that **this API is not production ready** yet
 
## Documentation

The documentation of the project is available [here](doc/index.md)

## Roadmap
- [ ] Improvement of the CI 
  - [ ] Avoid to run twice a pipeline when a pr is merged.
  - [x] Generate code coverage.
  - [x] Increase the amount of tests
- [x] Implement the openApi specs and generate an openApi page with swagger ui
- [ ] Implement a CD
- [ ] Integration of blackfire for the monitoring of the app performance
- [ ] Integration of a tool like sentry to monitor errors
- [ ] Refactoring and improve code quality 

## Getting started

1. First clone the repo 
```bash
git clone git@github.com:niko-38500/boilerplate-symfony-hexagonal-architecture-CQRS-JWT.git your-project-name
```

2. Install the dependencies 
```bash
composer install && cd tools/php-cs-fixer && composer install && cd -
```
You have probably noticed that php cs fixer have its own composer.json file, this is normal, this is what is recommended in the [documentation](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer#installation)

3. Generate the RSA key pair for the JWT package (for dev and test env) there will be stored into ```config/jwt/```
```bash
php bin/console lexik:jwt:generate-keypair && php bin/console lexik:jwt:generate-keypair -e test
```

4. Lunch the server (run on ports 3300, can be changed in docker/docker-compose.yml)
```bash
composer up
```
helper composer script for the full liste refer to this [section](#composer-helper-scripts)


## Composer helper scripts

- ```composer stan```: Run phpstan analysis
- ```composer cs```: Run php cs fixer
- ```composer tests```: Run the tests suits within php fpm docker container
- ```composer qa```: Run the stan and cs command together
- ```composer docker```: Get into the php fpm container
- ```composer up```: Launch the docker nginx server
- ```composer stop```: Stop the docker nginx server
- ```composer rebuild-image```: Rebuild the docker images
- ```composer test-db```: Purge the test database and load fixtures for the test environment
- ```composer reset-db```: Purge the database and load fixtures