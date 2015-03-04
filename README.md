# Webhooks Package for Laravel 4

Webhooks is a pubsub/pubsubhubbub system for Laravel 4 applications.

## Installation via Composer

Add this to you composer.json file, in the require object:

```javascript
"dmyers/laravel-webhooks": "dev-master"
```

After that, run composer install to install Webhooks.

Add the service provider to `app/config/app.php`, within the `providers` array.

```php
'providers' => array(
    // ...
    'Dmyers\Webhooks\WebhooksServiceProvider',
)
```

Add a class alias to `app/config/app.php`, within the `aliases` array.

```php
'aliases' => array(
    // ...
    'Webhooks' => 'Dmyers\Webhooks\Webhooks',
)
```

Publish the package's model, migration, and view.

```console
php artisan model:publish dmyers/laravel-webhooks
php artisan migration:publish dmyers/laravel-webhooks
```

Add the trait to the model you want to act as subscribers on.

```php
use Subscribable;
```

Finally, add the trait to the models you want to publish events on.

```php
use Eventable;

protected static $eventable = [
	'created' => 'object.create',
	'updated' => 'object.updated',
	'deleted' => 'object.delete',
];

protected $eventable_sync = ['id', 'subject'];
```

## Usage

First get an instance of an item type (model):

```php
$object = Model::find(1);
```

Fetch all the activity:

```php
$object->activity(array(
	'id'        => $activity_id, // optional
	'doer_id'   => $doer_id, // optional
	'victim_id' => $victim_id, // optional
	'item_id'   => $item_id, // optional
	'item_type' => $item_type, // optional
	'feed_type' => $feed_type, // optional
));
```

Track an activity event:

```php
$object->addActivity($item_type, $doer_id, $victim_id, $action);
```

Update an activity event:

```php
$object->updateActivity($item_type, $doer_id, $victim_id, $action);
```

Delete an activity event:

```php
$object->deleteActivity($item_type, $doer_id, $victim_id, $action);
```

Display an activity feed:

```php
$object->renderActivityFeed($type, $doer_id, $victim_id);
```

Push a feed into another activity feed:

```php
$object->pushActivityFeed($type, $doer_id, $victim_id);
```