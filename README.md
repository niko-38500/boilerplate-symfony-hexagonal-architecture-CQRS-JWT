# API boilerplate

This API is designed with hexagonal architecture, DDD and CQRS.
This boilerplate embeds an authentication and authorization by JWT for a user.

This project can run either with the standard web development sever or with the nginx sever configured in docker which I highly recommend.

> Note that **this API is not production ready** yet

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

## Project structure

### Main concepts
As mentioned above this project is built with hexagonal architecture which means that the business logic is as decoupled as possible from the infrastructure.
But I applied also some concepts of the DDD (Domain Driven Design), mostly the separation of the code by domain, for example actually we have a User domain which contains all that refer to the user (like his name let suggest) so for example if your website contains a blog section obliviously you will have a Blog domain.

Each domains will contains 3 directories 

- ```Infrastructure```: Contains the code that depends on library or current framework, should not have business logic
- ```Domain```: Contains all the business logic (use cases) and entities this layer should **not** have dependencies with the other layers. It is the core of your application
- ```Presentation```: Contains the code that handle an incoming request and producing a response, like controllers, view models, templates etc...

```
.
├── User
│   ├── Infrastructure
│   ├── Domain
│   └── Presentation
├── FrameworkInfrastructure
│   ├── Infrastructure
│   ├── Domain
│   └── Presentation
└── Blog
    ├── Infrastructure
    ├── Domain
    └── Presentation
```

Zoom into a domain, let's take the example of the blog domain :

```
Blog
├── Presentation
│   ├── Controller
│   │    ├── GetPostController.php
│   │    └── UpdatePostController.php
│   └── DTO
│        └── PostDTO.php
├── Infrastructure
│   ├── CommandHandler
│   │    └── UpdatePostCommandHandler.php
│   ├── QueryHandler
│   │    └── GetPostBySlugQueryHandler.php
│   └── Repository
│        └── PostRepository.php
└── Domain
    ├── Command
    │    └── UpdatePostCommand.php
    ├── Query
    │    └── GetPostBySlugQuery.php
    └── UseCase
         ├── UpdatePost.php
         └── GetPost.php
```



Some people also use an application directory, but personally, I prefer not to because I think there is already a lot of abstraction. Therefore, I don't think it's worth adding more.

Also, I don't use the concept of request and response boundary which are juste simple value object that transport the data from the presentation layer to the domain layer and vice versa, I prefer create DTOs to convert the request into owned objects and for the response return something from the use cases if necessary and build the response from the controller.

### Open API

This project auto generate a swagger page but you might want to enhance this documentation which very basic without 
further attribute documentation

To make it easier to write your documentation, you can find here some templates to add to your controllers 
(working on a solution to make it automatically)

#### Templates


**GET endpoint :**
```php
#[
    Route('/api/v', name: '', methods: ['GET']),
    OA\Get(
        description: '',
        summary: '',
        parameters: [
            new OA\Parameter(
                name: '',
                description: '',
                in: '',
                required: 
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: ''),
            new OA\Response(response: 400, description: ''),
        ]
    ),
    OA\Tag(name: '', description: '')
]
```

**POST endpoint :**

```php
#[
    Route('/api/v', name: '', methods: ['POST']),
    OA\Post(
        description: '',
        summary: '',
        requestBody: new OA\RequestBody(required: true),
        responses: [
            new OA\Response(response: 200, description: 'User account is validated'),
            new OA\Response(response: 404, description: 'Validation token is not present or not found'),
        ]
    ),
    OA\Tag(name: 'User', description: 'Actions related to the user')
]
```

Be sure to add the ```#[MapRequestPayload]``` attribute with a DTO on your endpoint method signature to automatically 
add the "requestBody" doc

### Event listeners

I use Symfony event listener to catch exceptions and return a JsonResponse instead of let the exception bubble up

You can add some custom exception to the listener and return a response in function of which exception is thrown which allow to handle useCase failure on the edge

### CQRS (Command Query Responsibility Segregation)

At first a little def of query and command
- query: request something from a data source (GET)
- command: Will apply a mutation to a data source (POST, PATCH, PUT ...)

With this pattern you have to create a query or a command for every single action and domain 

e.g: GetUserByIdQuery, GetUserByEmailQuery, UpdateUsernameCommand, UpdateUserPasswordCommand

This may not seem practical (and for a small application, you're right), but for a medium or large application it starts to be interesting because it allows a high level of scalability, indeed for each command and/or request you can for example change data source or apply specific logic individually

## User authentication et authorization

To log in a user you can send POST request with {email: string, password: string} to /login_check  which will return the JWT

Once the token in your possession you have to place it into your http request header ```Authorization: Beerer YOUR_TOKEN```.
It will automatically grant or deny access to the ressource depending on the token validity or the access_control config into ```security.yaml``` 

This API is stateless which means that it does not keep any context of previous requests, however you can retrieve the current user by the JWT thanks to the ```UserProvider``` service 

### Social authentication/authorization

oAuth2 is used to handle the authentication/authentication beside the classic email password

#### Workflow

Since this application is a pure api it is not possible to redirect the client to the provider
connection page, so to make this auth possible we should in a first time fetch the URL to the provider authentication page.
Then you should redirect to your front end page that will resend a request to the api to finish the registration process.

Here a sequence diagram of the workflow.
![oAuth_workflow_sequence_diagram.png](doc%2FoAuth_workflow_sequence_diagram.png)

#### Add an authenticator provider

In order to add an authentication provider you have to register your app to the desired authenticator provider (e.g google, apple and so on).

Once you have registered your app to your favorite provider, it will provide you a client id and a client secret, you will have to 
fill them into the ```config/packages/knpu_oauth2_client.yaml``` file (not fill the keys directly into the file instead put it into the .env/.env.local file(s))

Then let's integrate this provider into your app :

1. Install from composer the provider package (cf https://github.com/knpuniversity/oauth2-client-bundle?tab=readme-ov-file#step-1-download-the-client-library)
2. Create an OAuthLogger by creating a class that extends ```App\FrameworkInfrastructure\Infrastructure\Security\OAuth\Logger\AbstractOAuthLogger.php``` and implements its methods (cf ```App\FrameworkInfrastructure\Infrastructure\Security\OAuth\Logger\GithubLogger```)
3. Register your logger into the factory ```App\FrameworkInfrastructure\Infrastructure\Security\OAuth\Factory\OAuthLoggerFactory```
   1. Fill the constant ```AVAILABLE_LOGGER``` the value must match the provider in ```config/packages/knpu_oauth2_client.yaml``` the key could be whatever you want.
   2. Add to the "match" statement your logger previously created (the constant allow to notify the available loggers on errors)
4. In the User entity add a column for the id of your current provider id (e.g. googleId, appleId and so on) make the property as string to avoid inconsistency between the different providers id indeed the providers could use an integer or maybe something else as a UUID

And that it with this procedure you will be able to authenticate with your new provider

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