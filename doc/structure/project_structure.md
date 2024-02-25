# Project structure

## Main concepts
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

## Event listeners

I use Symfony event listener to catch exceptions and return a JsonResponse instead of let the exception bubble up

You can add some custom exception to the listener and return a response in function of which exception is thrown which allow to handle useCase failure on the edge

## CQRS (Command Query Responsibility Segregation)

At first a little def of query and command
- query: request something from a data source (GET)
- command: Will apply a mutation to a data source (POST, PATCH, PUT ...)

With this pattern you have to create a query or a command for every single action and domain

e.g: GetUserByIdQuery, GetUserByEmailQuery, UpdateUsernameCommand, UpdateUserPasswordCommand

This may not seem practical (and for a small application, you're right), but for a medium or large application it starts to be interesting because it allows a high level of scalability, indeed for each command and/or request you can for example change data source or apply specific logic individually
