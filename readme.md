# Loyalty Corp Test

## Installation into a Laravel instance

> **Note:** This package requires at least PHP 7.1 

#### Clone a new Laravel instance

```
[user@desktop dev]$ git clone git@github.com:laravel/laravel.git
```

#### Add repositories to composer.json

Since the repositories aren't on packagist you'll need to specify where they are.

```
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:sjdaws/loyaltycorp-mailchimp.git"
        },
        {
            "type": "vcs",
            "url": "git@github.com:sjdaws/loyaltycorp-test.git"
        }
    ],
```

#### Add both packages to the require section of composer.json
 
You will also need felixkiss/uniquewith-validator to validate unique emails within a list

```
    "require": {
        // Other requirements
        ...
        "sjdaws/loyaltycorp-mailchimp": "dev-master",
        "sjdaws/loyaltycorp-test": "dev-master",
        "felixkiss/uniquewith-validator": "^3.1"
    },
```

### Run composer update to install all packages

```
[user@desktop laravel]$ composer update
```

### Add TestServiceProvider to config/app.php

You will also need to add the uniquewith-validator service provider.

```
    'providers' => [
        // A bunch of preinstalled providers
        ...
        Sjdaws\LoyaltyCorpTest\Providers\TestServiceProvider::class, 
        Felixkiss\UniqueWithValidator\ServiceProvider::class,
    ],
```

### Publish assets:

```
[user@desktop laravel]$ php artisan vendor:publish
```

### Add Mailchimp API key to .env

You will also need to ensure database and queue are set up, so may as well do that at the same time

```
    MAILCHIMP_APIKEY=somerandomkey-us1
```
    
### Create jobs and failed jobs migrations for queue runner

```
[user@desktop laravel]$ php artisan queue:table
[user@desktop laravel]$ php artisan queue:failed-table
````

### Run migrations

```
[user@desktop laravel]$ php artisan migrate
```
    
### Start queue listener

```
[user@desktop laravel]$ php artisan queue:listen
```

    
### View in browser

The package will be available at http://SERVER_NAME/mailchimp

## Known issues

There are several known issues due to time constraints:
* It does not handle users who aren't allowed to resubscribe. If a user unsubscribes (themselves) and they're cleaned and resubscribe then unsubscribe again (themselves) it's not possible to subscribe them again via API. This package doesn't handle this situation and will throw an exception.
* It does not poll for changes after dispatching a job to the queue, you'll need to manually refresh after the job succeeds to get the changes to show
* It will have issues with larger lists as Mailchimp's API can be unreliable from Australia. Once the list gets large it may have issues performing some actions such as sync members. There is no transaction tracking so the job will start from 0 every time rather than restarting where it left off.
* It is designed as a Laravel only package and won't work as a standalone package.
* It relies pretty heavily on validation and validating data before passing it to Mailchimp in the hope it can catch any error before it occurs but i It assumes success by default. Because there is no polling for a result if Mailchimp returns an error (timeout or something else unexpected) it will throw an exception but the user won't be notified, it will store the failed job in the failed jobs table if set up.
* It only handles subscribe/unsubscribe for members. Members who aren't subscribed are assumed to be unsubscribed so users who are cleaned or pending will show as unsubscribed when updating. The true status is shown in view member.
* It has no tests as the Laravel implementation of phpunit requires some adjustments from the norm.
* The UI is horrible.
