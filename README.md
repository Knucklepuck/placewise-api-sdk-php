# placewise-api-sdk-php
Official Php Client for Placewise Media's REST API.

```php
$pw = new \Placewise\Client($your_email, $your_password);

$result = $pw->get('malls/123/deals',
                   [
                     'page' => [ 'number' => 1, 'size' => 10 ],
                     'include' => 'retailer,images',
                     'fields' => [ 'deals' => 'title', 'retailers' => 'name', 'images' => 'url' ]
                   ]);
```
