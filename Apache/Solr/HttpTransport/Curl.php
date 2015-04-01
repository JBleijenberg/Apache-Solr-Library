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
namespace Apache\Solr\HttpTransport;

class Curl implements TransportInterface
{

	/**
	 * Hold cURL resource
	 *
	 * @var resource
	 */
	private $curl;

	/**
	 * Initialize curl with some default options.
	 */
	public function __construct()
	{
		$this->curl = curl_init();

		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_HEADER, false);
	}

	/**
	 * @param $url
	 * @param null $timeout
	 *
	 * @return \Apache\Solr\HttpTransport\Response
	 */
	public function performHeadRequest($url, $timeout = null)
	{
		if ($timeout === null || $timeout < 0.0) {
			$timeout = 2;
		}

		curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($this->curl, CURLOPT_NOBODY, true);

		$response = curl_exec($this->curl);

		$statusCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
		$contentType = curl_getinfo($this->curl, CURLINFO_CONTENT_TYPE);

		return new Response($statusCode, $contentType, $response);
	}

	/**
	 * Close cURL session
	 */
	public function __destruct()
	{
		curl_close($this->curl);
	}
}