# one-signal
测试消息推送用OneSignal

## Installation

### Composer
Execute the following command to get the latest version of the package:

```terminal
composer require jybtx/one-signal
```

### Laravel

#### >= laravel5.5

ServiceProvider will be attached automatically

#### Other

In your `config/app.php` add `Jybtx\OneSignal\Providers\OneSignalServiceProvider::class` to the end of the `providers` array:

```php
'providers' => [
    ...
    Jybtx\\OneSignal\\Providers\\OneSignalServiceProvider::class,
],
'aliases'  => [
    ...
    "OneSignal": Jybtx\OneSignal\Faceds\OneSignalFacade::class,
]
```
Publish Configuration

```shell
php artisan vendor:publish --provider "Jybtx\OneSignal\Providers\OneSignalServiceProvider"
```

## Usage

### Register a message push ID for the user at registration time
```php
use OneSignal;
OneSignal::registerPlayerId($identifier,$device_type,$device_os='',$device_model='');
```

### Send messages to all users
```php
OneSignal::sendMessageAllUsers($title,$txt,$time=null,$data = array());
```

### Based on OneSignal PlayerIds sending
```php
OneSignal::sendMessageSomeUser($title,$txt,$users,$data = array());
```

### Cancellation notice
```php
OneSignal::revokeMessage($notifId);
```

### send Message For Tags
```php
OneSignal::sendMessageUsingTags($title,$txt, $tags, $url = NULL, $data = NULL, $buttons = NULL, $subtitle = NULL);
```

### View notifications
```php
OneSignal::getNotifications($limit = NULL, $offset = NULL, $kind = 1 );
```

### Generate a compressed CSV export of all of your current user data
```php
OneSignal::getAllUserToExportCsv();
```
# License
MIT