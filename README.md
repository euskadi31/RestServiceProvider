# Silex Rest Service Provider

[![Build Status](https://img.shields.io/travis/euskadi31/RestServiceProvider/master.svg)](https://travis-ci.org/euskadi31/RestServiceProvider)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/bc7cc439-3c27-4885-9e38-e331f41988c3.svg)](https://insight.sensiolabs.com/projects/bc7cc439-3c27-4885-9e38-e331f41988c3)
[![Coveralls](https://img.shields.io/coveralls/euskadi31/RestServiceProvider.svg)](https://coveralls.io/github/euskadi31/RestServiceProvider)
[![HHVM](https://img.shields.io/hhvm/euskadi31/RestServiceProvider.svg)](https://travis-ci.org/euskadi31/RestServiceProvider)
[![Packagist](https://img.shields.io/packagist/v/euskadi31/rest-service-provider.svg)](https://packagist.org/packages/euskadi31/rest-service-provider)

Adding some REST capabilities to Silex 2.0, so you can more easily build RESTful APIs.

## Install

Add `euskadi31/rest-service-provider` to your `composer.json`:

    % php composer.phar require euskadi31/rest-service-provider:~1.2

## Usage

### Configuration

```php
<?php

$app = new Silex\Application;

$app->register(new \Euskadi31\Silex\Provider\RestServiceProvider);
```

### Field filter

```php
<?php

$app = new Silex\Application;

$app->register(new \Euskadi31\Silex\Provider\RestServiceProvider);

$app->get('/users', function() {
    return $this->json([
        [
            'id'        => 1,
            'username'  => 'John',
            'email'     => 'john@example.com',
            'enabled'   => true
        ],
        [
            'id'        => 2,
            'username'  => 'Jean',
            'email'     => 'jean@example.com',
            'enabled'   => true
        ]
    ]);
});
```

Request:

```http
GET /users?fields=username
```

Response:

```json
[
    {
        "id": 1,
        "username": "John"
    },
    {
        "id": 2,
        "username": "Jean"
    }
]
```

### Jsonp response

```php
<?php

$app = new Silex\Application;

$app->register(new \Euskadi31\Silex\Provider\RestServiceProvider);

$app->get('/users', function() {
    return $this->json([
        [
            'id'        => 1,
            'username'  => 'John',
            'email'     => 'john@example.com',
            'enabled'   => true
        ],
        [
            'id'        => 2,
            'username'  => 'Jean',
            'email'     => 'jean@example.com',
            'enabled'   => true
        ]
    ]);
});
```

Request:

```http
GET /users?callback=Acme.process
```

Response:

```js
/**/Acme.process([
    {
        "id": 1,
        "username": "John",
        "email": "john@example.com",
        "enabled": true
    },
    {
        "id": 2,
        "username": "Jean",
        "email": "jean@example.com",
        "enabled": true
    }
]);
```

### Pretty print response

```php
<?php

$app = new Silex\Application;

$app->register(new \Euskadi31\Silex\Provider\RestServiceProvider);

$app->get('/users', function() {
    return $this->json([
        [
            'id'        => 1,
            'username'  => 'John',
            'email'     => 'john@example.com',
            'enabled'   => true
        ],
        [
            'id'        => 2,
            'username'  => 'Jean',
            'email'     => 'jean@example.com',
            'enabled'   => true
        ]
    ]);
});
```


Request:

```http
GET /users?pretty=0
```

Response:

```json
[{"id":1,"username":"John","email":"john@example.com","enabled":true},{"id":2,"username":"Jean","email":"jean@example.com","enabled":true}]
```

### Error response

```json
{
    "error": {
        "message": "No route found for \u0022GET \/me1\u0022",
        "type": "NotFoundHttpException",
        "code": 404
    }
}
```

## License

RestServiceProvider is licensed under [the MIT license](LICENSE.md).
