<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			xml
 * @subpackage		rss
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			0.1.1
 */


/** Spoon class */
require_once 'spoon/spoon.php';

/** Spoon RSS execption class */
require_once 'spoon/xml/rss/exception.php';

/** Spoon RSSItem class */
require_once 'spoon/xml/rss/rss_item.php';

/** Spoon File class */
require_once 'spoon/filesystem/file.php';

/** Spoon HTTP class */
require_once 'spoon/http/http.php';


/**
 * This base class provides all the methods used by RSS-files
 *
 * @package			xml
 * @subpackage		rss
 *
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			0.1.1
 */
class SpoonRSS
{
	/**
	 * Xml header
	 *
	 * @var	string
	 */
	const RSS_HEADER = "Content-Type: application/xml; charset=";


	/**
	 * Categories
	 *
	 * @var	array
	 */
	private $categories = array();


	/**
	 * The charset
	 *
	 * @var	string
	 */
	private $charset = 'ISO-8859-15';


	/**
	 * Cloud properties
	 *
	 * @var	array
	 */
	private $cloud;


	/**
	 * Copyright
	 *
	 * @var	string
	 */
	private $copyright;


	/**
	 * Description
	 *
	 * @var	string
	 */
	private $description;


	/**
	 * Docs
	 *
	 * @var	string
	 */
	private $docs;


	/**
	 * Generator
	 *
	 * @var	string
	 */
	private $generator;


	/**
	 * Image properties
	 *
	 * @var	array
	 */
	private $image = array();


	/**
	 * Items
	 *
	 * @var	array
	 */
	private $items = array();


	/**
	 * Language
	 *
	 * @var	string
	 */
	private $language;


	/**
	 * Last build date
	 *
	 * @var	string
	 */
	private $lastBuildDate;


	/**
	 * Link
	 *
	 * @var	string
	 */
	private $link;


	/**
	 * Managing editor
	 *
	 * @var	string
	 */
	private $managingEditor;


	/**
	 * Publication date
	 *
	 * @var	int
	 */
	private $publicationDate;


	/**
	 * Rating
	 *
	 * @var	string
	 */
	private $rating;


	/**
	 * Days that will be skipped
	 *
	 * @var	array
	 */
	private $skipDays = array();


	/**
	 * Hours that will be skipped
	 *
	 * @var	array
	 */
	private $skipHours = array();


	/**
	 * Must the items be sort on publication date?
	 *
	 * @var	bool
	 */
	private $sort = true;


	/**
	 * The sortingmethod
	 *
	 * @var	string
	 */
	private static $sortingMethod = 'desc';


	/**
	 * Title
	 *
	 * @var	string
	 */
	private $title;


	/**
	 * Time to life
	 *
	 * @var	int
	 */
	private $ttl;


	/**
	 * Webmaster
	 *
	 * @var	string
	 */
	private $webmaster;


	/**
	 * The default constructor
	 *
	 * @return	void
	 * @param	string $title
	 * @param	string $link
	 * @param	string $description
	 * @param	array[optional] $items
	 */
	public function __construct($title, $link, $description, $items = array())
	{
		// set properties
		$this->setTitle($title);
		$this->setLink($link);
		$this->setDescription($description);

		// loop items and add them
		foreach ($items as $item) $this->addItem($item);
	}


	/**
	 * Adds a category for the feed
	 *
	 * @return	void
	 * @param	string $category
	 * @param	string[optional] $domain
	 */
	public function addCategory($category, $domain = null)
	{
		// build array
		$aCategory['category'] = (string) $category;
		if($domain) $aCategory['domain'] = (string) $domain;

		// set property
		$this->categories[] = $aCategory;
	}


	/**
	 * Add an item to the feed
	 *
	 * @return	void
	 * @param	SpoonRSSItem
	 */
	public function addItem($item)
	{
		// allowed classes, will be extended
		$aAllowedClasses = array('SpoonRSSItem');

		// validate
		if(!in_array(get_class($item), $aAllowedClasses)) throw new SpoonRSSException('The specified item (type: '.get_class($item).') isn\'t  valid.');

		// set property
		$this->items[] = $item;
	}


	/**
	 * Add a day to skip
	 *  the default value is sunday
	 *  * - addSkipDay
	 *
	 * @return	void
	 * @param	string $day
	 */
	public function addSkipDay($day)
	{
		$aAllowedDays = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saterday');

		// redefine var
		$day = (string) SpoonFilter::getValue(strtolower($day), $aAllowedDays, 'sunday');

		// validate
		if(in_array($day, $this->skipDays)) throw new SpoonRSSException('This ('. $day .') day is already added.');

		// set property
		$this->skipDays[] = ucfirst($day);
	}


	/**
	 * Add a hour to skip
	 *  default is 0
	 *
	 * @return	void
	 * @param 	int
	 */
	public function addSkipHour($hour)
	{
		$aAllowedHours = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23);

		// redefine var
		$hour = (int) SpoonFilter::getValue($hour, $aAllowedHours, 0);

		// validate
		if(!in_array($hour, $aAllowedHours)) throw new SpoonRSSException('This ('. $hour .') isn\'t a valid hour. Only '. join(', ', $aAllowedHours) .' are allowed.)');
		if(in_array($hour, $this->skipHours)) throw new SpoonRSSException('This ('. $hour .') hour is already added.');

		// set property
		$this->skipHours[] = (int) $hour;
	}


	/**
	 * Build the xmlfile
	 *
	 * @return	string
	 */
	private function buildXML()
	{
		// sort if needed
		if($this->getSorting()) $this->sort();

		// init xml
		$xml = '<?xml version="1.0" encoding="'. strtolower($this->getCharset()) .'" ?>'."\n";
		$xml .= '<rss version="2.0">'."\n";
		$xml .= '<channel>'."\n";

		// insert title
		$xml .= '	<title>'. $this->getTitle() .'</title>'."\n";

		// insert link
		$xml .= '	<link>'. $this->getLink() .'</link>'."\n";

		// insert description
		$xml .= '	<description>'."\n";
		$xml .= '		<![CDATA['."\n";
		$xml .= '		'. $this->getDescription() ."\n";
		$xml .= '		]]>'."\n";
		$xml .= '</description>'."\n";

		// insert image if needed
		$imageProperties = $this->getImage();
		if(!empty($imageProperties))
		{
			$image = $this->getImage();

			$xml .= '	<image>'."\n";
			$xml .= '		<title>'. $image['title'] .'</title>'."\n";
			$xml .= '		<url>'. $image['url'] .'</url>'."\n";
			$xml .= '		<link>'. $image['link'] .'</link>'."\n";
			if(isset($image['width']) && $image['width'] != '') $xml .= '		<width>'. $image['title'] .'</width>'."\n";
			if(isset($image['height']) && $image['height'] != '') $xml .= '		<height>'. $image['title'] .'</height>'."\n";
			if(isset($image['description']) && $image['description'] != '') $xml .= '		<description><![CDATA['. $image['title'] .']]></description>'."\n";
			$xml .= '	</image>'."\n";
		}

		// insert last build date
		if($this->getLastBuildDate() != '') $xml .= '	<lastBuildDate>'. $this->getLastBuildDate('r') .'</lastBuildDate>'."\n";

		// insert publication date
		if($this->getPublicationDate() != '') $xml .= '	<pubDate>'. $this->getPublicationDate('r') .'</pubDate>'."\n";

		// insert time to live
		if($this->getTTL() != '') $xml .= '	<ttl>'. $this->getTTL() .'</ttl>'."\n";

		// insert managing editor
		if($this->getManagingEditor() != '') $xml .= '	<managingEditor>'. $this->getManagingEditor() .'</managingEditor>>'."\n";

		// insert webmaster
		if($this->getWebmaster() != '') $xml .= '	<webmaster>'. $this->getWebmaster() .'></webmaster>'."\n";

		// insert copyright
		if($this->getCopyright() != '') $xml .= '	<copyright>'. $this->getCopyright() .'</copyright>'."\n";

		// insert categories
		$categories = $this->getCategories();
		if(!empty($categories))
		{
			foreach ($this->getCategories() as $category)
			{
				if(isset($category['domain']) && $category['domain'] != '') $xml .= '	<category domain="'. $category['domain'] .'"><![CDATA['. $category['category'] .']]></category>'."\n";
				else $xml .= '	<category><![CDATA['. $category['category'] .']]</category>'."\n";
			}
		}

		// insert rating
		if($this->getRating() != '') $xml .= '	<rating>'. $this->getRating() .'</rating>'."\n";

		// insert generator
		if($this->getGenerator() != '') $xml .= '	<generator><![CDATA['. $this->getGenerator() .']]></generator>'."\n";

		// insert language
		if($this->getLanguage() != '') $xml .= '	<language>'. $this->getLanguage() .'</language>'."\n";

		// insert docs
		if($this->getDocs() != '') $xml .= '	<docs>'. $this->getDocs() .'</docs>'."\n";

		// insert skipdays
		$skipDays = $this->getSkipDays();
		if(!empty($skipDays))
		{
			$xml .= '	<skipDays>'."\n";
			foreach ($skipDays as $day) $xml .= '	<day>'.$day.'</day>'."\n";
			$xml .= '	</skipDays>'."\n";
		}

		// insert skiphours
		$skipHours = $this->getSkipHours();
		if(!empty($skipHours))
		{
			$xml .= '	<skipHours>'."\n";
			foreach ($skipHours as $hour) 			$xml .= '	<hour>'.$hour.'</hour>'."\n";
			$xml .= '	</skipHours>'."\n";
		}

		// insert cloud
		$cloudProperties = $this->getCloud();
		if(!empty($cloudProperties))
		{
			$cloud = $this->getCloud();
			$xml .= '	<cloud domain="'. $cloud['domain'] .'" port="'. $cloud['port'] .'" path="'. $cloud['path'] .'" registerProce-dure="'. $cloud['register_procedure'] .'" protocol="'. $cloud['protocol'] .'" />'."\n";
		}

		// insert items
		foreach ($this->getItems() as $item)
		{
			$xml .= $item->parse();
		}

		// add endtags
		$xml .= '</channel>'."\n";
		$xml .= '</rss>'."\n";

		return $xml;
	}


	/**
	 * Compare Object for sorting on publication date
	 *
	 * @return	int
	 * @param	SpoonRSSItem $object1
	 * @param	SpoonRSSItem $object2
	 * @param	string[optional] $sortingMethod
	 */
	private static function compareObjects($object1, $object2)
	{
		// validate
		if(get_class($object1) != 'SpoonRSSItem') throw new SpoonRSSException('This isn\'t a valid object.');
		if(get_class($object2) != 'SpoonRSSItem') throw new SpoonRSSException('This isn\'t a valid object.');

		// if the object have the same publicationdate there are equal
		if($object1->getPublicationDate() == $object2->getPublicationDate()) return 0;

		if(SpoonRSS::$sortingMethod == 'asc')
		{
			// if the publication date is greater then the other return 1, so we known howto sort
			if($object1->getPublicationDate() > $object2->getPublicationDate()) return 1;

			// if the publication date is smaller then the other return -1, so we known howto sort
			if($object1->getPublicationDate() < $object2->getPublicationDate()) return -1;
		}
		else
		{
			// if the publication date is greater then the other return -1, so we known howto sort
			if($object1->getPublicationDate() > $object2->getPublicationDate()) return -1;

			// if the publication date is smaller then the other return 1, so we known howto sort
			if($object1->getPublicationDate() < $object2->getPublicationDate()) return 1;
		}
	}


	/**
	 * Retrieves the categories for a feed
	 *
	 * @return	void
	 */
	public function getCategories()
	{
		return $this->categories;
	}


	/**
	 * Get the charset
	 *
	 * @return	string
	 */
	public function getCharset()
	{
		return $this->charset;
	}


	/**
	 * Get the cloud
	 *
	 * @return	array
	 */
	public function getCloud()
	{
		return $this->cloud;
	}


	/**
	 * Get the copyright
	 *
	 * @return	string
	 */
	public function getCopyright()
	{
		return $this->copyright;
	}


	/**
	 * Get the description
	 *
	 * @return	string
	 */
	public function getDescription()
	{
		return $this->description;
	}


	/**
	 * Get the docs
	 *
	 * @return	string
	 */
	public function getDocs()
	{
		return $this->docs;
	}


	/**
	 * Get the generator
	 *
	 * @return	string
	 */
	public function getGenerator()
	{
		return $this->generator;
	}


	/**
	 * Retrieves the image properties
	 *
	 * @return	array
	 */
	public function getImage()
	{
		return $this->image;
	}


	/**
	 * Retrieves the items
	 *
	 * @return	array
	 */
	public function getItems()
	{
		return $this->items;
	}


	/**
	 * Get the language
	 *
	 * @return	string
	 */
	public function getLanguage()
	{
		return $this->language;
	}


	/**
	 * Get the last build date
	 *
	 * @return	mixed
	 */
	public function getLastBuildDate($format = null)
	{
		// set time if needed
		if($this->lastBuildDate == null) $this->lastBuildDate = time();

		// format if needed
		if($format) $date = date((string) $format, $this->lastBuildDate);
		else $date = $this->lastBuildDate;

		// return
		return $date;
	}


	/**
	 * Get the link
	 *
	 * @return	string
	 */
	public function getLink()
	{
		return $this->link;
	}


	/**
	 * Get the managing editor
	 *
	 * @return	string
	 */
	public function getManagingEditor()
	{
		return $this->managingEditor;
	}


	/**
	 * Get the publication date
	 *
	 * @return	format
	 */
	public function getPublicationDate($format = null)
	{
		// set time if needed
		if($this->publicationDate == null) $this->publicationDate = time();

		// format if needed
		if($format) $date = date((string) $format, $this->publicationDate);
		else $date = $this->publicationDate;

		// return
		return $date;
	}


	/**
	 * Get the rating
	 *
	 * @return	string
	 */
	public function getRating()
	{
		return $this->rating;
	}


	/**
	 * Get the raw XML
	 *
	 * @return	string
	 */
	public function getRawXML()
	{
		return $this->buildXML();
	}


	/**
	 * Retrieves the days to skip
	 *
	 * @return	array
	 */
	public function getSkipDays()
	{
		return $this->skipDays;
	}


	/**
	 * Retrieves the hours to skip
	 *
	 * @return	array
	 */
	public function getSkipHours()
	{
		return $this->skipHours;
	}


	/**
	 * Get if sorting status
	 *
	 * @return	bool
	 */
	public function getSorting()
	{
		return $this->sort;
	}


	/**
	 * Get the sorting method
	 *
	 * @return	string
	 */
	public function getSortingMethod()
	{
		return self::$sortingMethod;
	}


	/**
	 * Get the title
	 *
	 * @return	array
	 */
	public function getTitle()
	{
		return $this->title;
	}


	/**
	 * Get the time to life
	 *
	 * @return	int
	 */
	public function getTTL()
	{
		return $this->ttl;
	}


	/**
	 * Get the webmaster
	 *
	 * @return	string
	 */
	public function getWebmaster()
	{
		return $this->webmaster;
	}


	/**
	 * Parse the feed and output the feed into the browser
	 *
	 * @return	void
	 * @param	bool[optional] $headers
	 */
	public function parse($headers = true)
	{
		// set headers
		if($headers) SpoonHTTP::setHeaders(self::RSS_HEADER . $this->getCharset());

		// output
		echo $this->buildXML();

		// stop here
		exit;
	}


	/**
	 * Write the feed into a file
	 *
	 * @return	void
	 * @param	string $path
	 */
	public function parseToFile($path)
	{
		// get xml
		$xml = $this->buildXML();

		// write content
		SpoonFile::setFileContent((string) $path, $xml, false, true);
	}


	/**
	 * Reads an feed into a SpoonRSS object
	 *
	 * @return	SpoonRSS
	 * @param	string $url
	 * @param	string[optional] $type
	 */
	public static function readFromFeed($url, $type = 'url')
	{
		$aAllowedTypes = array('url', 'string');

		// redefine var
		$url = (string) $url;
		$type = (string) SpoonFilter::getValue($type, $aAllowedTypes, 'url');

		// validate
		if(!in_array($type, $aAllowedTypes)) throw new SpoonRSSException('This ('. $type .') isn\'t allowed. Only '. join(', ', $aAllowedTypes) .' are allowed.');
		if($type == 'url' && !SpoonFilter::isURL($url)) throw new SpoonRSSException('This ('. $url .') isn\'t a valid url.');

		// load xmlstring
		if($type == 'url')
		{
			// check if allow_url_fopen is enabled
			if(ini_get('allow_url_fopen') == 0) throw new SpoonRSSException('allow_url_fopen should be enabled, if you want to get a remote url.');

			// open the url
			$handle = @fopen($url, 'r');

			// validate the handle
			if($handle === false) throw new SpoonRSSException('Something went wrong while retrieving the url.');

			// read the string
			$xmlString = @stream_get_contents($handle);

			// close the hanlde
			@fclose($handle);
		}

		// not that url
		else $xmlString = $url;

		// convert to simpleXML
		$xml = @simplexml_load_string($xmlString);

		// validate the feed
		if($xml === false) throw new SpoonRSSException('Invalid rss-string.');

		// validate the feed again
		if(!$xml->channel || !$xml->channel->title || !$xml->channel->link || !$xml->channel->description) throw new SpoonRSSException('This ('. $url .') isn\'t a valid feed.');

		// get title, link and description
		$title = (string) $xml->channel->title;
		$link = (string) $xml->channel->link;
		$description = (string) $xml->channel->description;

		// create instance
		$rss = new SpoonRSS($title, $link, $description);

		// add items
		foreach ($xml->channel->item as $item)
		{
			$item = SpoonRSSItem::readFromXML($item);
			$rss->addItem($item);
		}

		// add category
		if($xml->channel->category)
		{
			foreach ($xml->channel->category as $category)
			{
				if(isset($category['domain'])) $rss->addCategory((string) $category, (string) $category['domain']);
				else $rss->addCategory((string) $category);
			}
		}

		// add skip day
		if($xml->channel->skipDays)
		{
			foreach ($xml->channel->skipDays->day as $day) $rss->addSkipDay((string) $day);
		}

		// add skip hour
		if($xml->channel->skipHours)
		{
			foreach ($xml->channel->skipHours->hour as $hour) $rss->addSkipHour((int) $hour);
		}

		// set cloud
		if(isset($xml->channel->cloud['domain']) && isset($xml->channel->cloud['port']) && isset($xml->channel->cloud['path']) && isset($xml->channel->cloud['registerProce-dure']) && isset($xml->channel->cloud['protocol']))
		{
			Spoon::dump('mekker');
			// read attributes
			$cloudDomain = (string) $xml->channel->cloud['domain'];
			$cloudPort = (int) $xml->channel->cloud['port'];
			$cloudPath = (string) $xml->channel->cloud['path'];
			$cloudRegisterProcedure = (string) $xml->channel->cloud['registerProce-dure'];
			$cloudProtocol = (string) $xml->channel->cloud['protocol'];

			// set property
			$rss->setCloud($cloudDomain, $cloudPort, $cloudPath, $cloudRegisterProcedure, $cloudProtocol);
		}

		// set copyright
		if($xml->channel->copyright)
		{
			$copyright = (string) $xml->channel->copyright;
			$rss->setCopyright($copyright);
		}

		// set docs
		if($xml->channel->docs)
		{
			$docs = (string) $xml->channel->docs;
			$rss->setDocs($docs);
		}

		// set generator if it is present
		if($xml->channel->generator)
		{
			$generator = (string) $xml->channel->generator;
			$rss->setGenerator($generator);
		}

		// set image if it is present
		if($xml->channel->image->title && $xml->channel->image->url && $xml->channel->image->link)
		{
			// read properties
			$imageTitle = (string) $xml->channel->image->title;
			$imageUrl = (string) $xml->channel->image->url;
			$imageLink = (string) $xml->channel->image->link;

			// read optional properties
			if($xml->channel->image->width) $imageWidth = (int) $xml->channel->image->width;
			else $imageWidth = null;
			if($xml->channel->image->height) $imageHeight = (int) $xml->channel->image->height;
			else $imageHeight = null;
			if($xml->channel->image->description) $imageDescription = (string) $xml->channel->image->description;
			else $imageDescription = null;

			// set image
			$rss->setImage($imageUrl, $imageTitle, $imageLink, $imageWidth, $imageHeight, $imageDescription);
		}

		// set language if its is present
		if($xml->channel->language)
		{
			$language = (string) $xml->channel->language;
			$rss->setLanguage($language);
		}

		// set last build date if it is present
		if($xml->channel->lastBuildDate)
		{
			$lastBuildDate = (int) strtotime($xml->channel->lastBuildDate);
			$rss->setLastBuildDate($lastBuildDate);
		}

		// set managing editor
		if($xml->channel->managingEditor)
		{
			$managingEditor = (string) $xml->channel->managingEditor;
			$rss->setManagingEditor($managingEditor);
		}

		// set publication date
		if($xml->channel->pubDate)
		{
			$publicationDate = (int) strtotime($xml->channel->pubDate);
			$rss->setPublicationDate($publicationDate);
		}

		// set rating
		if($xml->channel->rating)
		{
			$rating = (string) $xml->channel->rating;
			$rss->setRating($rating);
		}

		// set ttl
		if($xml->channel->ttl)
		{
			$ttl = (int) $xml->channel->ttl;
			$rss->setTTL($ttl);
		}

		// set webmaster
		if($xml->channel->webmaster)
		{
			$webmaster = (string) $xml->channel->webmaster;
			$rss->setWebmaster($webmaster);
		}

		// return
		return $rss;
	}


	/**
	 * Set the charset
	 *
	 * @return	void
	 * @param	string[optional] $charset
	 */
	public function setCharset($charset = 'ISO-8859-15')
	{
		$aAllowedCharsets = array('iso-8859-1', 'iso-8859-15', 'utf-8');

		// set property
		$this->charset = (string) SpoonFilter::getValue(strtolower($charset), $aAllowedCharsets, 'iso-8859-15');
	}


	/**
	 * Set the cloud for the feed
	 *
	 * @return	void
	 * @param	string $domain
	 * @param	int $port
	 * @param	string $path
	 * @param	string $registerProcedure
	 * @param	string $protocol
	 */
	public function setCloud($domain, $port, $path, $registerProcedure, $protocol)
	{
		$aAllowedProtocols = array('xml-rpc', 'soap', 'http-post');

		// set properties
		$this->cloud['domain'] = (string) $domain;
		$this->cloud['port'] = (int) $port;
		$this->cloud['path'] = (string) $path;
		$this->cloud['register_procedure'] = (string) $registerProcedure;
		$this->cloud['protocol'] = (string) SpoonFilter::getValue($protocol, $aAllowedProtocols, 'xml-rpc');
	}


	/**
	 * Set the copyright
	 *
	 * @return	void
	 * @param	string $copyright
	 */
	public function setCopyright($copyright)
	{
		$this->copyright = (string) $copyright;
	}


	/**
	 * Set the description for the feed
	 *
	 * @return	void
	 * @param	string $description
	 */
	public function setDescription($description)
	{
		$this->description = (string) $description;
	}


	/**
	 * Set the doc for the feed
	 *
	 * @return	void
	 * @param	string $docs
	 */
	public function setDocs($docs)
	{
		$this->docs = (string) $docs;
	}


	/**
	 * Set the generator for the feed
	 *
	 * @return	void
	 * @param	string[optional] $generator
	 */
	public function setGenerator($generator = null)
	{
		if($generator == null) $generator = 'Spoon/'.SPOON_VERSION;
		$this->generator = (string) $generator;
	}


	/**
	 * Set the image for the feed
	 *
	 * @return	void
	 * @param	string $url
	 * @param	string $title
	 * @param	string $link
	 * @param	int[optional] $width
	 * @param	int[optional] $height
	 * @param	string[optional] $description
	 */
	public function setImage($url, $title, $link, $width = null, $height = null, $description = null)
	{
		// redefine vars
		$url = (string) $url;
		$link = (string) $link;

		// validate
		if(!SpoonFilter::isURL($url)) throw new SpoonRSSException('This ('. $url .')isn\'t a valid url.');
		if(!SpoonFilter::isURL($link)) throw new SpoonRSSException('This ('. $link .') isn\'t a valid link.');

		// set properties
		$this->image['url'] = $url;
		$this->image['title'] = (string) $title;
		$this->image['link'] = $link;
		if($width) $this->image['width'] = (int) $width;
		if($height) $this->image['height'] = (int) $height;
		if($description) $this->image['description'] = (string) $description;
	}


	/**
	 * Set the language for the feed
	 *
	 * @return	void
	 * @param	string $language
	 */
	public function setLanguage($language)
	{
		$this->language = (string) $language;
	}


	/**
	 * Set the last build date for the feed
	 *
	 * @return	void
	 * @param	int[optional] $lastBuildDate
	 */
	public function setLastBuildDate($lastBuildDate = null)
	{
		if($lastBuildDate) $lastBuildDate = time();
		$this->lastBuildDate = (int) $lastBuildDate;
	}


	/**
	 * Set the link for the feed
	 *
	 * @return	void
	 * @param	string $link
	 */
	public function setLink($link)
	{
		// redefine vars
		$link = (string) $link;

		// validate
		if(!SpoonFilter::isURL($link)) throw new SpoonRSSException('This ('. $link .') isn\'t a valid link');

		// set property
		$this->link = $link;
	}


	/**
	 * Set the managing editor for the feed
	 *
	 * @return	void
	 * @param	string $managingEditor
	 */
	public function setManagingEditor($managingEditor)
	{
		$this->managingEditor = (string) $managingEditor;
	}


	/**
	 * Sets the publication date for the feed
	 *
	 * @return	void
	 * @param	int[optional] $publicationDate
	 */
	public function setPublicationDate($publicationDate = null)
	{
		if($publicationDate) $publicationDate = time();
		$this->publicationDate = (int) $publicationDate;
	}


	/**
	 * Sets the rating
	 *
	 * @return	void
	 * @param	string $rating
	 */
	public function setRating($rating)
	{
		$this->rating = (string) $rating;
	}


	/**
	 * Set if sorting status
	 *
	 * @return	void
	 * @param	bool[optional] $sorting
	 */
	public function setSorting($on = true)
	{
		$this->sort = (bool) $on;
	}


	/**
	 * Set the sorting method
	 *
	 */
	public function setSortingMethod($sortingMethod = 'desc')
	{
		$aAllowedSortingMethods = array('asc', 'desc');

		// set sorting method
		self::$sortingMethod = SpoonFilter::getValue($sortingMethod, $aAllowedSortingMethods, 'desc');
	}


	/**
	 * Set the title for the feed
	 *
	 * @return	void
	 * @param	string $title
	 */
	public function setTitle($title)
	{
		$this->title = (string) $title;
	}


	/**
	 * Set time to live for the feed
	 *
	 * @return	void
	 * @param	int $ttl
	 */
	public function setTTL($ttl)
	{
		$this->ttl = (int) $ttl;
	}


	/**
	 * Sets the webmaster for the feed
	 *
	 * @return	void
	 * @param	string $webmaster
	 */
	public function setWebmaster($webmaster)
	{
		$this->webmaster = (string) $webmaster;
	}


	/**
	 * Sort the item on publication date
	 *
	 * @return	void
	 */
	private function sort()
	{
		// get items
		$items = $this->getItems();

		// sort
		uasort($items, array('SpoonRSS', 'compareObjects'));

		// set items
		$this->items = $items;
	}

}

?>