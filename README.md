FileMap
=======

a file mapper for various file types.

### License

MIT License

### PSR

PSR-1, PSR-2, and PSR-4.


Usage
-----

```php
// build file map.
$map_dir   = __DIR__.'/map';
$cache_dir = __DIR__.'/cache';
$map       = Tuum\Locator\FileMap::forge($map_dir, $cache_dir);

// render the file based on path.
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

### Resource or Text

The FileMap returns

*   resource if extension is defined in $map->emit_extensions, and
*   text if no extension in path, but found a file with extensions defined in $map->view_extensions.


Emissions
---------

### Emitting Extensions

The FileMap will emit the file as a resource, if

*   the path contains an extension and
*   the extension is defined in `$map->emit_extension`,

such as `sample.jpg` .

add extension and associated mime-type into `$map->emit_extensions` to allow more types.

```php
$map->emit_extensions['swf'] = 'application/x-shockwave-Flash';
```

### Viewing Extensions

The FileMap will emit the file as a text, if

*   no extension in the path, and
*   a file exists with extensions defined in `$map->view_extensions`,

such as 'content' as a path and 'content.text' exists.

*   .php: evaluate as PHP. 
*   .md: converts to html using CommonMark. 
*   .txt, .text: get contents and renders as content. 

MarkUp for CommonMark
----------

`MarkUp` class converts common-mark (AKA Markdown) file to HTML.
Construction Set markdown file root directory of markdown files, and cache directory,

Usage: 

```php
$markUp = Tuum\Locator\MarkUp::forge('/path/to/md', '/cache/dir');
$html = $markUp->getHtml('to/mark/down.md');
```

The `$markUp` will converts the CommonMark to HTML only when cached html file is not found.