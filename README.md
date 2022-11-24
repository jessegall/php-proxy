# php-proxy

A package that allows you to easily cache, intercept and modify interactions between objects.

## Installation

```
composer require jessegall/proxy
```

## What can it do?

### Cache interactions

By wrapping any object in a proxy you can easily enable cache for any interaction. This counts for method calls and
accessing/setting a property

```php

use JesseGall\Proxy\Interactions\Contracts\Interacts;
use JesseGall\Proxy\Proxy;

class Api {

    public function getUsers(): array
    {
       ...
    }
    
    public function getProduct(string $id): Product 
    {
        ...
    }

}

$api = new Api();

$proxy = new Proxy($api);

// Make sure to enable the cache
$proxy->setCacheEnabled(true);

// The result wil be cached after the initial call
$users = $proxy->getUsers();

// All calls of the same method will return the cached result
$cachedUser = $proxy->getUsers();

// Also work with arguments
$product = $proxy->getProduct(1);
$cachedProduct = $proxy->getProduct(1); // Returns Cached product
$otherProduct = $proxy->getProduct(2); // Product with id 2 is NOT cached

```

#### Clear cache

```php
$proxy->getCacheHandler()->clear();
```

#### Custom cache handler

By default the cache only remembers the interactions for the duration of the program.
But it is very easy to create your own custom cache handler that implements a persistent cache.

```php
use JesseGall\Proxy\Contracts\HandlesCache;

class CustomCacheHandler implements HandlesCache
{

    public function put(ConcludedInteraction $concluded): bool
    {
        ...
    }

    public function get(Interacts $interaction): ConcludedInteraction
    {
        ...
    }

    public function has(Interacts $interaction): bool
    {
        ...
    }

    public function clear(): void
    {
        ...
    }
    
}

$proxy->setCacheHandler(new CustomCacheHandler());

```

Each interaction has a unique hash that can be used to cache the interaction

```php
$hash = $interaction->toHash();
```

---

### Intercept interactions

Interceptors can be used to intercept any interaction with an object.

#### Register with closure
```php
use JesseGall\Proxy\Interactions\Contracts\Interacts;
use JesseGall\Proxy\Proxy;

$proxy = new Proxy($target);

$proxy->getForwarder()->registerInterceptor(function (Interacts $interacts) {
    ...
});
```

#### Register with class instance
```php
use JesseGall\Proxy\Interactions\Contracts\Interacts;
use JesseGall\Proxy\Proxy;
use JesseGall\Proxy\Forwarder\Contracts\Intercepts;

class MyInterceptor implements Intercepts {
    
    public function handle(Interacts $interaction, object $caller = null) {
        ...
    }
    
}

$proxy = new Proxy($target);

$proxy->getForwarder()->registerInterceptor(new MyInterceptor());
```

#### Register with class string
```php
use JesseGall\Proxy\Proxy;

$proxy = new Proxy($target);

$proxy->getForwarder()->registerInterceptor(MyInterceptor::class);
```

#### Register multiple with an array
```php
use \JesseGall\Proxy\Forwarder\Contracts\Intercepts;
use JesseGall\Proxy\Proxy;

$proxy = new Proxy($target);

$proxy->getForwarder()->registerInterceptor([
    function(Intercepts $interaction) {
        ...
    }, 
    new MyInterceptor(),
    MyInterceptor::class,
]);
```

#### Example

In this example an interceptor is used to log interactions 

```php
class Target
{

    public string $property = 'value';

    public function someMethod(): void
    {
        ...
    }

}

$target = new Target();

$proxy = new Proxy($target);

$proxy->getForwarder()->registerInterceptor(function (Interacts $interaction) {
    // Log method call
    if ($interaction instanceof InvokesMethod) {
        log("Method {$interaction->getMethod()} of target called at " . time());
    }

    // Log property change
    if ($interaction instanceof MutatesProperty) {
        log("Property {$interaction->getProperty()} changed to {$interaction->getValue()} " . time());
    }
    
    if ($interaction instanceof  RetrievesProperty) {
    
    }
});

$proxy->someMethod(); // Will log the method call
$proxy->property = 'new value'; // Will log that the property changed
```

### Cancel interactions

Interactions can be cancelled by changing their status to cancelled, failed or fulfilled.
When an interaction no longer has the status pending, the interaction will not be forwarded to the target.

```php
use JesseGall\Proxy\Interactions\Status;

$proxy->registerInterceptor(function(Interacts $interaction) {
    $interaction->setStatus(Status::CANCELLED); // Signal the interaction has been cancelled
    $interaction->setStatus(Status::FAILED);  // Signal the interaction failed
    $interaction->setStatus(Status::FULFILLED);  // Signal the interaction was already fulfilled
})
```

#### Example

For this example lets say you have an api class, and you want to limit the method calls per user without refactoring the
api class.

```php
use JesseGall\Proxy\Interactions\Contracts\Interacts;
use JesseGall\Proxy\Interactions\Contracts\InvokesMethod;
use JesseGall\Proxy\Interactions\Status;
use JesseGall\Proxy\Proxy;

class Api
{

    public function getPosts(): array
    {
        ...
    }

}

class ApiProxy extends Proxy
{

    public function __construct()
    {
        parent::__construct(new Api());

        $this->forwarder->registerInterceptor(function (Interacts $interaction) {
            if ($interaction instanceof InvokesMethod) {
                $user = user();

                $method = $interaction->getMethod();

                if ($user->getApiCalls($method) >= 5) {
                    $interaction->setStatus(Status::CANCELLED);

                    $interaction->setResult([
                        'message' => 'You reached the api call limit'
                    ]);
                } else {
                    $user->incrementApiCalls($method);
                }
            }
        });
    }

}
```

Of course, it is also possible to simply throw an exception in the interceptor

```php
use JesseGall\Proxy\Interactions\Status;

$proxy->registerInterceptor(function(Interacts $interaction) {
    ... 
    throw new Exception("You are not allowed to do this");
})
```

## Exception handlers

```php
    Documentation WIP
```