Locator
=======

a file locator and a simple renderer. 

Locator
-------

A simple file locator, implementing `LocatorInterface`. 

Usage:

```php
$locator = Locator::forge('/path/to/files');
$locator->addRoot('/another/path';
$location = $locator->locate('locate/a/file');
include($location);
```

### Locator.php

Dumb and simple file locator. 

### UnionManager.php

Another locator using Flysystem. 

FileMap
=======

a mapper for various file types. 

Usage:

```php
$map = new FileMap(Locator::forge('/file/map');
$img = $map->render('may/image/sample.jpg');
if (empty($img)) {
    echo 'not found';
}
echo $img[1]; // image/jpg
if (is_resource($fp)) {
    fpassthru($img[0]);
}
```

Use `render` method to find a file, which returns an array containing:

*   0th: resource, or a contained text, 
*   1st: mime type. 

or returns an empty array if not found. 

### emitting extensions

files for emitting as is, such as `jpg` files. 
Finds a file for a path with extensions, such as `sample.jpg`, 
and returns a file resource and mime-type. 

`$FileMap->emit_extensions` shows the list of the extension and associated mime-type.

### viewing extensions

Files for viewing as text for a path without a extension. 
Finds a file for a path without an extension, such as `info`, 
and renders as specified by `$FileMap->view_extensions`. 

*   .php: evaluate as PHP. 
*   .md: converts to html using CommonMark. 
*   .txt, .text: get contents and renders as content. 

CommonMark
----------

CommonMark (AKA Markdown) converter with cache. 

Usage: 

```php
$markUp = CommonMark::forge('/path/to/md', '/cache/dir');
$html = $markUp->getHtml('to/mark/down.md');
```
