# Open API

This project auto generate a swagger page but you might want to enhance this documentation which very basic without
further attribute documentation

To make it easier to write your documentation, you can find here some templates to add to your controllers
(working on a solution to make it automatically)

## Templates


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