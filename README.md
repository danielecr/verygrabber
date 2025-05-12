# VeryGrabber

smartango/verygrabber scrape html following a json schema definition.

It is designed to grab an array of elements, such as table rows or list of DIVs.

## Usage

```php
use \smrtg\VeryGrabber\GrabFromSchema;

$doc = file_get_contents(dirname(__FILE__).'/data/file.html');
$grab = new GrabFromSchema($doc);

$schema = file_get_contents(dirname(__FILE__).'/data/schema.json');

$data = $grab->getStruct($schema);
```

See tests/data/schema.json for the json schema definition: it follows a recursive descending parser concept in the DOM
