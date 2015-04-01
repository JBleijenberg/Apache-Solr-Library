<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the General Public License (GPL 3.0)
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/GPL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category    Apache Solr Library
 * @package     Apache Solr Library
 * @author      Jeroen Bleijenberg <jeroen@maxserv.com>
 *
 * @copyright   Copyright (c) 2015 MaxServ (http://www.maxserv.com)
 * @license     http://opensource.org/licenses/GPL-3.0 General Public License (GPL 3.0)
 */
namespace Apache\Solr;

use \Apache\Solr\HttpTransport\TransportInterface;
use \Apache\Solr\Exception as SolrException;

class Service
{

	protected $host;

	protected $port;

	protected $path;

	protected $httpTransport;

	protected $queryStringDelimiter = '&';

	/**
	 * Solr Writer.
	 * Default is json. Valid writers can be found at https://cwiki.apache.org/confluence/display/solr/Response+Writers
	 *
	 * @var string
	 */
	protected $writer = 'json';

	/**
	 * Servlet mappings
	 */
	const PING_SERVLET = 'admin/ping';

	const UPDATE_SERVLET = 'update';

	const SEARCH_SERVLET = 'select';

	const THREADS_SERVLET = 'admin/threads';

	const EXTRACT_SERVLET = 'update/extract';

	/**
	 * Init Solr Service object
	 *
	 * @param $host string e.g localhost
	 * @param $port integer e.g 8180
	 * @param $path string e.g /solr/
	 * @param \Apache\Solr\HttpTransport\TransportInterface $httpTransport
	 */
	public function __construct($host, $port, $path, $httpTransport = null)
	{
		$this->setHost($host);
		$this->setPort($port);
		$this->setPath($path);

		if ($httpTransport instanceof HttpTransport\TransportInterface) {
			$this->setHttpTransport($httpTransport);
		}
	}

	public function setHost($host)
	{
		$this->host = $host;

		return $this;
	}

	public function getHost()
	{
		return $this->host;
	}

	public function setPort($port)
	{
		$this->port = $port;

		return $this;
	}

	public function setPath($path)
	{
		$this->path = trim($path, '/');
	}

	public function getPath()
	{
		return $this->path;
	}

	public function setHttpTransport(TransportInterface $httpTransport)
	{
		$this->httpTransport = $httpTransport;

		return $this;
	}

	public function getHttpTransport()
	{
		if (!$this->httpTransport) {
			$this->httpTransport = new HttpTransport\Curl;
		}

		return $this->httpTransport;
	}

	/**
	 * Replace the default query string delimiter (&)
	 *
	 * @param $delimiter
	 *
	 * @return $this
	 */
	public function setQueryStringDelimiter($delimiter)
	{
		$this->queryStringDelimiter = $delimiter;

		return $this;
	}

	/**
	 * Generate and return requested url
	 *
	 * @param string $type
	 * @param array $parameters
	 *
	 * @return string
	 */
	public function getUrl($type = self::PING_SERVLET, array $parameters = array())
	{
		$qsa = "?wt={$this->writer}";

		if (!empty($parameters)) {
			$qsa = '&' . http_build_query($parameters, null, $this->queryStringDelimiter);
		}

		return "http://{$this->host}:{$this->port}/{$this->path}/{$type}{$qsa}";
	}

	/**
	 * Escape a value for special query characters.
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	static public function escape($value)
	{
		$pattern = '/(\+|-|&&|\|\||!|\(|\)|\{|}|\[|]|\^|"|~|\*|\?|:|\\\)/';
		$replace = '\\\$1';

		return preg_replace($pattern, $replace, $value);
	}

	/**
	 * Escape a value meant to be contained in a phrase for special query characters
	 *
	 * @param $phrase
	 *
	 * @return mixed
	 */
	static public function escapePhrase($phrase)
	{
		$pattern = '/("|\\\)/';
		$replace = '\\\$1';

		return preg_replace($pattern, $replace, $phrase);
	}

	/**
	 * Ping to see if the connection to the server is available
	 *
	 * @param int $timeout
	 *
	 * @return bool|mixed
	 */
	public function ping($timeout = 2)
	{
		$start = microtime(true);

		$httpTransport = $this->getHttpTransport();

		$response = $httpTransport->performHeadRequest($this->getUrl(self::PING_SERVLET), $timeout);

		return ($response->getStatusCode() == 200) ? microtime(true) - $start : false;
	}

	public function getThreads()
	{
		$httpTransport = $this->getHttpTransport();

		$response = $httpTransport->performGetRequest($this->getUrl(self::THREADS_SERVLET));

		if ($response->getStatusCode() !== 200) {
			throw new SolrException($response->getStatusMessage());
		}

		return $response;
	}
}