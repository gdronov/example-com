### Install

`composer require gdronov/example-com`


### Quickstart example

```php
require 'vendor/autoload.php';

$apiClient = new Gdronov\ExampleCom\ApiClient('my-api-key-for-example-com');
$service = new Gdronov\ExampleCom\CommentHandler($apiClient);

// Get comments
$list = $service->getList($count, $offset);

// Add comment
$newCommentId = $service->add('Grigory', 'New comment');

// Edit comment
$service->edit($commentId, 'Alex', 'Edited comment');
```
