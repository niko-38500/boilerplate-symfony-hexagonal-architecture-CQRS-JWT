# User authentication et authorization

To log in a user you can send POST request with {email: string, password: string} to /login_check  which will return the JWT

Once the token in your possession you have to place it into your http request header ```Authorization: Beerer YOUR_TOKEN```.
It will automatically grant or deny access to the ressource depending on the token validity or the access_control config into ```security.yaml```

This API is stateless which means that it does not keep any context of previous requests, however you can retrieve the current user by the JWT thanks to the ```UserProvider``` service 

## Social authentication/authorization

oAuth2 is used to handle the authentication/authentication beside the classic email password

### Workflow

Since this application is a pure api it is not possible to redirect the client to the provider
connection page, so to make this auth possible we should in a first time fetch the URL to the provider authentication page.
Then you should redirect to your front end page that will resend a request to the api to finish the registration process.

Here a sequence diagram of the workflow.

![oAuth_workflow_sequence_diagram.png](resource%2FoAuth_workflow_sequence_diagram.png)

### Add an authenticator provider

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