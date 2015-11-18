FileMap
=======

A file mapper that maps a path to various file types, such as images, text, and markdown file.

finds a file based on a given path, and returns the file resource or contents (string), associated mime-type, and other information. 

### License

MIT License

### PSR

PSR-1, PSR-2, and PSR-4.


Usage
-----

```php
$map = Tuum\Locator\FileMap::forge(
    __DIR__.'/map',   // root dir where mapped files exist. 
    __DIR__.'/cache'  // give cache dir to convert md files.
);

// render the file based on path.
$found = $map->render('images/sample.jpg');
if (!$found->found()) {
    echo 'not found';
}
header('Content-Type: '.$found->getMimeType());
if ($fp = $found->getResource()) {
    fpassthru($fp);
} else {
    echo $found->getContents();
}
```

The `render` method will find a file, then returns an `FileInfo` object which has methods like:

### `FileInfo` Object

FileInfor have methods such as:

*   `FileInfo::found(): bool`: returns if a file for a given path is found. 
*   `FileInfo::getMimeType(): string|null`: returns a mime types for the file.
*   `FileInfo::getResource(): resource|null`: returns a resource for images, etc.
*   `FileInfo::getContents(): string`: returns a contents of a text files. if a resource is given, this methods returns the content of the file resource. 

depending on the type of file, the `FileInfo` object may have a file resource or a file's contents as string. Try retriving a file resource first, then file contents if no resource is found. 

Emissions
---------

### Emitting based on Extensions

The FileMap will emit the file as a resource, if

*   the path has an extension (i.e. `sample.jpg`), and
*   the extension is defined in `$map->emit_extension`, and 
*   found the file at the path. 

The following list some of the predefined extensions and associated mime-type:

```php
    public $emit_extensions = [
        'pdf'  => 'application/pdf',
        'gif'  => 'image/gif',
    ];
```

To add your extensions, 

```php
$map->addEmitExtension('swf', 'application/x-shockwave-Flash');
```

### Viewing File Content

The FileMap will emit the file content as a text, if

*   not handled by the emitting by extension, and
*   a file exists with extensions defined in `$map->view_extensions`. 

To add an extension for viewing files,

```php
$map->addViewExtension('twig', 
    function(FileInfo $found) use($twig) {
        $found->setContents($twig->render($found->getLocation()));
        return $found;
    }, 
    'text/html');
```

The following shows the pre-defined extensions and the behavior of rendering. In all cases, it returns mime-type as `text/html`.

*   .php: evaluate the file as PHP. 
*   .txt, .text: get contents and put the string inside `<pre>` html tag. 
*   .md: converts the file to html using `MarkUp` object. 



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