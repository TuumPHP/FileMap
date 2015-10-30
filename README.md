FileMap
=======

a simple file mapper for various file types. 

### License

MIT License

### PSR

PSR-1, PSR-2, and PSR-4.


Usage
-----

```php
$map = FileMap::forge(__DIR__.'/map', __DIR__.'/cache');
$img = $map->render('images/sample.jpg');
if (empty($img)) {
    echo 'not found';
}
if (is_resource($fp)) {
    header("Content-Type: ".$img[1]); // Content-Type: image/jpg
    fpassthru($img[0]);
}
```

The `render` method will find a file, then returns an array containing:

*   0th: resource, or a contained text, 
*   1st: mime type, 

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
