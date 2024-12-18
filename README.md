OPML Parser
==================

[![GitHub license](https://img.shields.io/github/license/imelgrat/opml-parser.svg?style=flat-square)](https://github.com/imelgrat/opml-parser/blob/master/LICENSE)
[![GitHub release](https://img.shields.io/github/release/imelgrat/opml-parser.svg?style=flat-square)](https://github.com/imelgrat/opml-parser/releases)
[![Total Downloads](https://poser.pugx.org/imelgrat/opml-parser/downloads)](https://packagist.org/packages/imelgrat/opml-parser)
[![GitHub issues](https://img.shields.io/github/issues/imelgrat/opml-parser.svg?style=flat-square)](https://github.com/imelgrat/opml-parser/issues)
[![GitHub stars](https://img.shields.io/github/stars/imelgrat/opml-parser.svg?style=flat-square)](https://github.com/imelgrat/opml-parser/stargazers)


OPML Parser Class: Extract the properties of content from OPML files. 

A file with the OPML file extension is an Outline Processor Markup Language file. It's saved using the XML format, and is used to exchange information between applications regardless of the operating system.

The OPML file format is often seen used as the import/export format for RSS feed reader programs. Since a file of this format can hold a collection of RSS subscription information, it's the ideal format for backing up or sharing RSS feeds.
The class retrieves local or remote OPML file and parses it to extract its content into a PHP iterator. Each of the iterator elements contains the URLs of the listed content as well all other the properties of each content entry such as: name, link target, description, RSS feed, creation date and content type (RSS, HTML, song, booklist, etc..).


Developed by [Ivan Melgrati](https://twitter.com/imelgrat) 

Requirements
------------

*   PHP >= 5.3.0

Installation
------------

### Composer

The recommended installation method is through
[Composer](http://getcomposer.org/), a dependency manager for PHP. Just add
`imelgrat/opml-parser` to your project's `composer.json` file:

```json
{
    "require": {
        "imelgrat/opml-parser": "*"
    }
}
```

[More details](http://packagist.org/packages/imelgrat/opml-parser) can
be found over at [Packagist](http://packagist.org).

### Manually

1.  Copy `src/opml-parser.php` to your codebase, perhaps to the `vendor`
    directory.
2.  Add the `OPML_Parser` class to your autoloader or `require` the file
    directly.

Then, in order to use the OPML class, you need to invoke the "use" operator to bring the class into skope.

```php
<?php
use imelgrat\OPML_Parser\OPML_Parser;

$parser = new OPML_Parser();

// Get OPML from URL
$parser->ParseLocation('http://www.bbc.co.uk/podcasts.opml', null);

// Walk through each item in the same way as we would if $parser were a string (thanks to the Iterator interface)
foreach ($parser as $key => $item)
{
	echo "<p> Item: " . $key . '</p><ul>';
	foreach ($item as $attribute => $value)
	{
		echo '<li>' . '<strong>' . $attribute . '</strong>:' . $value . '</li>';
	}
	echo '</ul>';
	echo '<p>&nbsp;</p>';

}
?>
```

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
