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

class Response
{

	/**
	 * Status Messages indexed by Status Code
	 * Obtained from: http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
	 *
	 * @var array
	 */
	static private $defaultStatusMessages = array(
		// Specific to PHP Solr Client
		0 => "Communication Error",

		// Informational 1XX
		100 => "Continue",
		101 => "Switching Protocols",

		// Successful 2XX
		200 => "OK",
		201 => "Created",
		202 => "Accepted",
		203 => "Non-Authoritative Information",
		204 => "No Content",
		205 => "Reset Content",
		206 => "Partial Content",

		// Redirection 3XX
		300 => "Multiple Choices",
		301 => "Moved Permanently",
		302 => "Found",
		303 => "See Other",
		304 => "Not Modified",
		305 => "Use Proxy",
		307 => "Temporary Redirect",

		// Client Error 4XX
		400 => "Bad Request",
		401 => "Unauthorized",
		402 => "Payment Required",
		403 => "Forbidden",
		404 => "Not Found",
		405 => "Method Not Allowed",
		406 => "Not Acceptable",
		407 => "Proxy Authentication Required",
		408 => "Request Timeout",
		409 => "Conflict",
		410 => "Gone",
		411 => "Length Required",
		412 => "Precondition Failed",
		413 => "Request Entity Too Large",
		414 => "Request-URI Too Long",
		415 => "Unsupported Media Type",
		416 => "Request Range Not Satisfiable",
		417 => "Expectation Failed",

		// Server Error 5XX
		500 => "Internal Server Error",
		501 => "Not Implemented",
		502 => "Bad Gateway",
		503 => "Service Unavailable",
		504 => "Gateway Timeout",
		505 => "HTTP Version Not Supported"
	);

	private $statusCode;

	private $statusMessage;

	private $contentType;

	private $responseBody;

	private $effectiveUrl;

	private $mimeType = 'text/plain';

	private $encoding = 'UTF-8';

	public function __construct($statusCode, $contentType, $responseBody, $url)
	{
		$this->statusCode = $statusCode;
		$this->statusMessage = self::getDefaultStatusMessage($statusCode);
		$this->responseBody = (string) $responseBody;
		$this->effectiveUrl = $url;

		if (!empty($contentType)) {
			$contentTypeParts = explode(';', $contentType, 2);

			if (array_key_exists(0, $contentTypeParts)) {
				$this->mimeType = trim($contentTypeParts[0]);
			}

			if (array_key_exists(1, $contentTypeParts)) {
				$encodingParts = explode('=', $contentTypeParts[1]);

				if (array_key_exists(1, $encodingParts)) {
					$this->encoding = trim($encodingParts[1]);
				}
			}
		}
	}

	public function getEffectiveUrl()
	{
		return $this->effectiveUrl;
	}

	/**
	 * @return mixed
	 */
	public function getStatusCode()
	{
		return $this->statusCode;
	}

	/**
	 * @return string
	 */
	public function getStatusMessage()
	{
		return $this->statusMessage;
	}

	/**
	 * @return string
	 */
	public function getMimeType()
	{
		return $this->mimeType;
	}

	/**
	 * @return string
	 */
	public function getEncoding()
	{
		return $this->encoding;
	}

	public function getBody()
	{
		return $this->responseBody;
	}

	/**
	 * Return status message that is representing the given status code
	 *
	 * @param $statusCode
	 *
	 * @return string
	 */
	static public function getDefaultStatusMessage($statusCode)
	{
		$message = "Unknown Status";

		if (array_key_exists($statusCode, self::$defaultStatusMessages)) {
			$message = self::$defaultStatusMessages[$statusCode];
		}

		return $message;
	}
}