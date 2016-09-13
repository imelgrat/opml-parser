OPML-Parser
==================

[![License](https://poser.pugx.org/imelgrat/OPML-Parser/license)](https://packagist.org/packages/imelgrat/OPML-Parser)
[![Latest Stable Version](https://poser.pugx.org/imelgrat/OPML-Parser/v/stable)](https://packagist.org/packages/imelgrat/OPML-Parser)
[![Total Downloads](https://poser.pugx.org/imelgrat/OPML-Parser/downloads)](https://packagist.org/packages/imelgrat/OPML-Parser)

OPML Parser Class: Extract the properties of content from OPML files. 

The class retrieves local or remote OPML file and parses it to extract its content into a PHP iterator.
Each of the iterator elements contains the URLs of the listed content as well all other the properties of each content entry such as: name, link target, description, RSS feed, creation date and content type (RSS, HTML, song, booklist, etc..).

Developed by [Ivan Melgrati](https://twitter.com/imelgrat) 

Requirements
------------

*   PHP >= 5.3.0

Installation
------------

### Composer

The recommended installation method is through
[Composer](http://getcomposer.org/), a dependency manager for PHP. Just add
`imelgrat/OPML-Parser` to your project's `composer.json` file:

```json
{
    "require": {
        "imelgrat/OPML-Parser": "*"
    }
}
```

[More details](http://packagist.org/packages/imelgrat/OPML-Parser) can
be found over at [Packagist](http://packagist.org).

### Manually

1.  Copy `src/OPML-Parser.php` to your codebase, perhaps to the `vendor`
    directory.
2.  Add the `OPML-Parser` class to your autoloader or `require` the file
    directly.

Feedback
--------

Please open an issue to request a feature or submit a bug report. Or even if
you just want to provide some feedback, I'd love to hear. I'm also available on
Twitter as [@imelgrat](https://twitter.com/imelgrat).

Contributing
------------

1.  Fork it.
2.  Create your feature branch (`git checkout -b my-new-feature`).
3.  Commit your changes (`git commit -am 'Added some feature'`).
4.  Push to the branch (`git push origin my-new-feature`).
5.  Create a new Pull Request.
