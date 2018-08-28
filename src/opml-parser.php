<?php
	/**
	 * A PHP-based OPML (Outline Processor Markup Language) Parser Class. Extracts the properties of content from OPML files
	 *
	 * @package opml-parser
	 * @author    Ivan Melgrati
	 * @version   2.2.1
	 */

	namespace imelgrat\OPML_Parser;

	if (!class_exists('OPML_Parser'))
	{
		/**
		 * A PHP-based OPML (Outline Processor Markup Language) Parser Class. Extracts content from OPML files.
		 * 
		 * @author    Ivan Melgrati
		 * @copyright Copyright 2018 by Ivan Melgrati
		 * @license   MIT https://github.com/imelgrat/OPML-Parser/blob/master/LICENSE
		 * @version   2.2.1
		 * @link https://en.wikipedia.org/wiki/OPML
		 * @link http://dev.opml.org/spec2.html
		 * @link http://www.phpclasses.org/package/4026-PHP-Extract-the-properties-of-content-from-OPML-files.html
		 */
		class OPML_Parser implements \Iterator
		{
			/**
			 * Resource handle to an XML parser to be used by the other XML functions.
			 * 
			 * @access protected
			 * @var resource $parser 
			 */
			protected $parser = null;

			/**
			 * Position inside OPML list (used for iterating over OPML results)
			 * 
			 * @access protected
			 * @var integer $position 
			 */
			protected $position = 0;

			/**
			 * Array containing all parsed items
			 * 
			 * @access protected
			 * @var array $opml_contents 
			 */
			protected $opml_contents = array();

			/**
			 * String containing the unparsed OPML string
			 * 
			 * @access protected
			 * @var string $unparsed_opml 
			 */
			protected $unparsed_opml = '';

			/**
			 * Outline attributes we wish to map and their mapping names (only the most common attributes were added, more attributes may be added later)
			 * 
			 * @access protected
			 * @var $opml_map_vars 
			 */
			protected $opml_map_vars = array(
				'ID' => 'id', // Unique element ID
				'TYPE' => 'type', // Element type (audio, feed, playlist, etc)
				'URL' => 'url', // Location of the item. Depending on the value of the type attribute, this can be either a single audio stream or audio playlist, a remote OPML file containing a playlist of audio items, or a remote OPML file to browse.
				'HTMLURL' => 'html_url', // Top-level link element
				'TEXT' => 'title', // Specifies the title of the item.
				'TITLE' => 'title', // Specifies the title of the item.
				'LANGUAGE' => 'language', // The value of the top-level language element
				'TARGET' => 'link_target', // The target window of the link
				'VERSION' => 'version', // Varies depending on the version of RSS that's being supplied. RSS1 for RSS 1.0; RSS for 0.91, 0.92 or 2.0; scriptingNews for scriptingNews format. There are no known values for Atom feeds, but they certainly could be provided.
				'DESCRIPTION' => 'description', // The top-level description element from the feed.
				'XMLURL' => 'xml_url', // The http address of the feed
				'CREATED' => 'created', // Date-time that the outline node was created
				'IMAGEHREF' => 'imageHref', // A link to an image related to the element (.e.g. a song poster)
				'ICON' => 'icon', // A link to an icon related to the element (.e.g. a radio-station's icon)
				'F' => 'song', // When used in OPML playlists, it's used to specify the song's filename.
				'BITRATE' => 'bitrate', // Used to specify the bitrate of an audio stream, in kbps.
				'MIME' => 'mime', //  Enter the MIME type of the stream/file.
				'DURATION' => 'duration', // If the item is not a live radio stream, set duration to the playback duration in seconds to ensure the progress bar is displayed correctly. This is especially helpful for VBR files where our bitrate detection may not work properly.
				'LISTENERS' => 'listeners', // Used to display the number of listeners currently listening to an audio stream.
				'CURRENT_TRACK' => 'current_track', // Used to display the track that was most recently playing on a radio station.
				'GENRE' => 'genre', //The genre of a stream may be specified with this attribute.
				'SOURCE' => 'source', // The source of the audio. This is currently used to describe, for instance, how a concert was recorded.
				);

			/**
			 * Constructor.
			 *
			 * @return OPML_Parser
			 */
			public function OPML_Parser()
			{
				$this->parser = null;
				$this->opml_contents = array();
				$this->position = 0;
			}

			/**
			 * OPML_Parser::rewind()
			 * This rewinds the iterator to the beginning. 
			 * 
			 * @return void
			 */
			public function rewind()
			{
				$this->position = 0;
			}

			/**
			 * OPML_Parser::current()
			 * Return the current element
			 * 
			 * @return mixed The current element
			 */
			public function current()
			{
				return $this->opml_contents[$this->position];
			}

			/**
			 * OPML_Parser::key()
			 * Return the key of the current element
			 * 
			 * @return scalar The key of the current element
			 */
			public function key()
			{
				return $this->position;
			}

			/**
			 * OPML_Parser::next()
			 * Move he iterator to the next entry. 
			 * 
			 * @return void
			 */
			public function next()
			{
				++$this->position;
			}

			/**
			 * OPML_Parser::valid()
			 * Checks if current position is valid
			 * 
			 * @return boolean Returns TRUE if the current position is valid (if the element exists)
			 */
			public function valid()
			{
				return isset($this->opml_contents[$this->position]);
			}

			/**
			 * OPML_Parser::getOPMLFile()
			 * Fetch Contents of Page (from file or URL). Queries are performed using cURL and, if not available, using file_get_contents() 
			 *
			 * @param string $location The location (file or URL) of the OPML file 
			 * @param  resource $context stream context from `stream_context_create()`. Contexts can be passed to most filesystem related stream creation functions (i.e. fopen(), file(), file_get_contents(), etc...). 
			 * @return string contents of the page at $location
			 */
			protected function getOPMLFile($location = '', $context = null)
			{
				if (in_array('curl', get_loaded_extensions()))
				{
					$options = array(
						CURLOPT_RETURNTRANSFER => true, // return web page
						CURLOPT_HEADER => false, // don't return headers
						CURLOPT_FOLLOWLOCATION => true, // follow redirects
						CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
						CURLOPT_ENCODING => "", // handle compressed
						CURLOPT_USERAGENT => "test", // name of client
						CURLOPT_AUTOREFERER => true, // set referrer on redirect
						CURLOPT_CONNECTTIMEOUT => 120, // time-out on connect
						CURLOPT_TIMEOUT => 120, // time-out on response
						);

					$ch = curl_init($location);
					curl_setopt_array($ch, $options);
					$contents = curl_exec($ch);
				}
				else
				{
					$contents = file_get_contents($location, false, $context);
				}
				return $contents;
			}

			/**
			 * OPML_Parser::ParseElementStart()
			 * The XML tag-open handler. It is used here to parse and store attributes from outline tags 
			 *
			 * @param resource $parser A reference to the XML parser calling the handler. 
			 * @param string $tagName The name of the element (tag) for which this handler is called. If case-folding is in effect for this parser, the element name will be in uppercase letters. 
			 * @param array $attrs The element's attributes (if any).The keys of this array are the attribute names, the values are the attribute values.Attribute names are case-folded on the same criteria as element names.
			 */
			protected function ParseElementStart($parser, $tagName, $attrs)
			{
				$map = $this->opml_map_vars;

				// Parse attributes if entered an "outline" tag
				if ($tagName == 'OUTLINE')
				{
					$node = array();

					foreach (array_keys($this->opml_map_vars) as $key)
					{
						if (isset($attrs[$key]))
						{
							$node[$key] = $attrs[$key];
						}
					}

					$this->opml_contents[] = $node;
				}
			}

			/**
			 * OPML_Parser::ParseElementEnd()
			 * The XML tag-close handler. It is used for processing closed tags (not used in this class but can be overloaded in child classes)
			 *
			 * @param resource $parser A reference to the XML parser calling the handler. 
			 * @param string $tagName The name of the element (tag) for which this handler is called. If case-folding is in effect for this parser, the element name will be in uppercase letters. 
			 */
			protected function ParseElementEnd($parser, $tagName)
			{
				// nothing to do.
			}

			/**
			 * OPML_Parser::ParseElementCharData()
			 * The XML char data handler. It is used for processing char data (not used in this class but can be overloaded in child classes)
			 *
			 * @param resource $parser A reference to the XML parser calling the handler. 
			 * @param string $data contains the character data as a string. Character data handler is called for every piece of a text in the XML document. It can be called multiple times inside each fragment (e.g. for non-ASCII strings). 
			 */
			protected function ParseElementCharData($parser, $data)
			{
				// nothing to do.
			}

			/**
			 * OPML_Parser::Parser()
			 * Parse the OPML data (resulting data stored in $opml_contents)
			 *
			 * @param string $XMLdata A reference to the XML parser calling the handler. 
			 */
			protected function Parser($XMLdata)
			{
				// Reset iterator
				$this->position = 0;

				$this->parser = xml_parser_create();

				xml_set_object($this->parser, $this);

				xml_set_element_handler($this->parser, array(&$this, 'ParseElementStart'), array(&$this, 'ParseElementEnd'));

				xml_set_character_data_handler($this->parser, array(&$this, 'ParseElementCharData'));

				xml_parse($this->parser, $XMLdata);

				xml_parser_free($this->parser);
			}

			/**
			 * OPML_Parser::ParseLocation()
			 * Parse contents from OPML file or URL
			 *
			 * @param string $location The location (file or URL) of the OPML file 
			 * @param  resource $context stream context from `stream_context_create()`. Contexts can be passed to most filesystem related stream creation functions (i.e. fopen(), file(), file_get_contents(), etc...). 
			 */
			public function ParseLocation($location, $context = null)
			{
				$this->unparsed_opml = trim($this->getOPMLFile($location, $context));
				$this->Parser($this->unparsed_opml);
			}

			/**
			 * OPML_Parser::ParseOPML()
			 * Parse contents from OPML string
			 *
			 * @param string $opml The unparsed OPML string 
			 */
			public function ParseOPML($opml)
			{
				$this->unparsed_opml = trim($opml);
				$this->Parser($this->unparsed_opml);
			}

			/**
			 * OPML_Parser::getUnparsedOPML()
			 * Get the unparsed OPML string
			 *
			 * @return string The unparsed OPML string 
			 */
			public function getUnparsedOPML()
			{
				return $this->unparsed_opml;
			}

			/**
			 * OPML_Parser::setAttribute()
			 * Add (or replace) an OPML attribute to parser's attribute list
			 *
			 * @param string $attribute The new attribute to parse (whitespace replaced by underscores)
			 * @param string $mapped_attribute The attribute's name to be returned. Defaults to the same attribute's name (in lowercase form)
			 */
			public function setAttribute($attribute, $mapped_attribute = '')
			{
				$attribute = strtoupper(preg_replace('/\s+/', '_', trim($attribute)));
				if ($mapped_attribute != '')
				{
					$mapped_attribute = strtoupper(preg_replace('/\s+/', '_', trim($mapped_attribute)));
				}
				else
				{
					$mapped_attribute = strtolower($attribute);
				}

				$this->opml_map_vars[$attribute] = $mapped_attribute;
			}

			/**
			 * OPML_Parser::unsetAttribute()
			 * Remove an OPML attribute to parser's attribute list
			 *
			 * @param string $attribute The attribute to remove (whitespace replaced by underscores)
			 */
			public function unsetAttribute($attribute)
			{
				$attribute = strtoupper(preg_replace('/\s+/', '_', trim($attribute)));

				unset($this->opml_map_vars[$attribute]);
			}
		}
	}

?>