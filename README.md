# php-proxy

A PHP library that provides a way to intercept and modify the behavior of objects by intercepting method calls, property accesses, and property assignments and forwarding them to a target object. 

The goal of this library is to provide a flexible and extensible way to intercept and modify the behavior of objects without modifying the target object itself.

## Installation

```
composer require jessegall/proxy
```

## Usage

1. [Cache interactions](#cache-interactions)
2. [Intercept interactions](#intercept-interactions)
3. [Cancel interactions](#cancel-interactions)
4. [Exception handling](#exception-handling)
5. [Listeners](#listeners)

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
But it is very easy to create your own custom cache handler to implements a persistent cache.

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

#### Interaction hash

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
    
    public function handle(Interacts $interaction, object $caller = null): void 
    {
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
use JesseGall\Proxy\Forwarder\Contracts\Intercepts;
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

---

### Cancel interactions

Interactions can be cancelled by changing their status to cancelled, failed or fulfilled.
When an interaction no longer has the status pending, the interaction will not be forwarded to the target.

```php
use JesseGall\Proxy\Interactions\Status;

$proxy->registerInterceptor(function(Interacts $interaction) {
    // Signal the interaction has been cancelled
    $interaction->setStatus(Status::CANCELLED);
     
    // Signal the interaction failed
    $interaction->setStatus(Status::FAILED);  
    
    // Signal the interaction was already fulfilled
    $interaction->setStatus(Status::FULFILLED);  
})
```

#### Examples

In this example we limit a users access to a certain api method

```php
use JesseGall\Proxy\Interactions\Contracts\Interacts;
use JesseGall\Proxy\Interactions\CallInteraction;
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
            if (! ($interaction instanceof CallInteraction)) {
               return; // Return if the interaction is not a method call 
            }
            
            $user = user();

            $method = $interaction->getMethod();

            if ($user->getApiCalls($method) >= 5) {
                $interaction->setStatus(Status::CANCELLED);

                $interaction->setResult(new ApiLimitResponse()); 
            } else {
                $user->incrementApiCalls($method);
            }
        });
    }

}

$api = new Api();

$proxy = new Proxy($api);

$proxy->getPosts(); // Interceptor will run before forwarding the interaction
```

Of course, it is also possible to simply throw an exception in the interceptor

```php
use JesseGall\Proxy\Proxy;
use JesseGall\Proxy\Interactions\Status;

$proxy->registerInterceptor(function(Interacts $interaction) {
    ... 
    throw new Exception("You are not allowed to do this");
})
```

---

### Exception handling

Exceptions thrown by an interaction can be caught using exception handlers

#### Register with closure

```php
use JesseGall\Proxy\Proxy;
use JesseGall\Proxy\Forwarder\Strategies\Exceptions\ExecutionException;

$proxy = new Proxy($target);

$proxy->getForwarder()->registerExceptionHandler(function (ExectionException $exception) {
    ...
});
```

#### Register with class instance

```php
use JesseGall\Proxy\Proxy;
use JesseGall\Proxy\Forwarder\Strategies\Exceptions\ExecutionException;
use JesseGall\Proxy\Forwarder\Contracts\HandlesFailedStrategies;

class MyExceptionHandler implements HandlesFailedStrategies {

    public function handle(ExecutionException $exception): void
    {
        ...
    }

}

$proxy = new Proxy($target);

$proxy->getForwarder()->registerExceptionHandler(new MyExceptionHandler());

```

#### Register with class string

```php
use JesseGall\Proxy\Proxy;

$proxy = new Proxy($target);

$proxy->getForwarder()->registerExceptionHandler(MyExceptionHandler::class);

```

#### Register multiple with an array

```php
use JesseGall\Proxy\Proxy;
use JesseGall\Proxy\Forwarder\Strategies\Exceptions\ExecutionException;

$proxy = new Proxy($target);

$proxy->getForwarder()->registerExceptionHandler([
    function (ExectionException $exception) {
        ...
    },
    new MyExceptionHandler(),
    MyExceptionHandler::class,
]);
```

#### Examples

In this example the target api has a request limit.
The exception handler will catch the 'too many request' exception and wait until we're able to make new requests.

```php
use JesseGall\Proxy\Forwarder\Strategies\Exceptions\ExecutionException;
use JesseGall\Proxy\Proxy;

$proxy = new Proxy($api);

$handler = function (ExecutionException $exception) {
    $attempts = 1;

    $original = $exception->getException();

    do {
        if ($original->getCode() !== 429) {
            return; // Return if the exception is not a 'too many request' exception
        }

        if ($attempts >= 10) {
            return; // Return when max attempts reached
        }

        sleep(10); // Wait 10 seconds before trying again

        try {
            $exception->getStrategy()->execute();

            $exception->setShouldThrow(false); // Don't throw exception after successful execution

            return;
        } catch (ExecutionException $exception) {
            $original = $exception->getException();
        }

        $attempts++;
    } while (true);
};

$proxy->getForwarder()->registerExceptionHandler($handler);

```

---

### Listeners

Listeners can be used to listen to concluded interactions.

#### Register with closure

```php
use JesseGall\Proxy\Proxy;
use JesseGall\Proxy\ConcludedInteraction;

$proxy = new Proxy($target);

$proxy->registerListener(function (ConcludedInteraction $interaction) {
    ...
});
```

#### Register with class instance

```php
use JesseGall\Proxy\Proxy;
use JesseGall\Proxy\ConcludedInteraction;
use JesseGall\Proxy\Contracts\Listener;

class MyListener implements Listener {

    public function handle(ConcludedInteraction $interaction): void
    {
        ...
    }

}

$proxy = new Proxy($target);

$proxy->registerListener(new MyListener());
```

#### Register with class string

```php
use JesseGall\Proxy\Proxy;
use JesseGall\Proxy\ConcludedInteraction;
use JesseGall\Proxy\Contracts\Listener;

$proxy = new Proxy($target);

$proxy->registerListener(MyListener::class);
```

#### Register multiple with an array

```php
use JesseGall\Proxy\Proxy;
use JesseGall\Proxy\ConcludedInteraction;
use JesseGall\Proxy\Contracts\Listener;

$proxy = new Proxy($target);

$proxy->registerListener([
    function(ConcludedInteraction $interaction) {
        ...
    },
    new MyListener(),
    MyListener::class
]);
```
