<?php

/**
 * This file has code referenced from "bigcommerce-api-php" project, under MIT License
 *
 *  Published name: bigcommerce-api-php
 *  URL: https://github.com/bigcommerce/bigcommerce-api-php/blob/master/src/Bigcommerce/Api/Connection.php
 *  Description: PHP client for connecting to the Bigcommerce V2 REST API.
 *  Authors: Bigcommerce
 *  License: MIT License
 *
 *  Copyright (C) Bigcommerce, 2019.
 *  All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace vwo\HttpHandler;

use vwo\Error\ClientError;
use vwo\Error\ServerError;
use vwo\Error\NetworkError;
use vwo\Constants\HttpRetries;
/**
 * HTTP connection.
 *
 * @codeCoverageIgnore
 */
class Connection
{
    /**
     * XML media type.
     */
    const MEDIA_TYPE_XML = 'application/xml';
    /**
     * JSON media type.
     */
    const MEDIA_TYPE_JSON = 'application/json';
    /**
     * Default urlencoded media type.
     */
    const MEDIA_TYPE_WWW = 'application/x-www-form-urlencoded';
    /**
     * @var resource cURL resource
     */
    private $curl;
    /**
     * @var array Hash of HTTP request headers.
     */
    private $headers = array();
    /**
     * @var array Hash of headers from HTTP response
     */
    private $responseHeaders = array();
    /**
     * The status line of the response.
     *
     * @var string
     */
    private $responseStatusLine;
    /**
     * @var string response body
     */
    private $responseBody;
    /**
     * @var boolean
     */
    private $failOnError = false;
    /**
     * Manually follow location redirects. Used if CURLOPT_FOLLOWLOCATION
     * is unavailable due to open_basedir restriction.
     *
     * @var boolean
     */
    private $followLocation = false;
    /**
     * Maximum number of redirects to try.
     *
     * @var int
     */
    private $maxRedirects = 20;
    /**
     * Number of redirects followed in a loop.
     *
     * @var int
     */
    private $redirectsFollowed = 0;
    /**
     * Deal with failed requests if failOnError is not set.
     *
     * @var string|FALSE
     */
    private $lastError = false;
    /**
     * Determines whether the response body should be returned as a raw string.
     */
    private $rawResponse = false;
    /**
     * Determines the default content type to use with requests and responses.
     */
    private $contentType;

    /**
     * Initializes the connection object.
     */
    public function __construct()
    {
        if (!defined('STDIN')) {
            define('STDIN', fopen('php://stdin', 'r'));
        }
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_HEADERFUNCTION, array($this, 'parseHeader'));
        curl_setopt($this->curl, CURLOPT_WRITEFUNCTION, array($this, 'parseBody'));
        // Set to a blank string to make cURL include all encodings it can handle (gzip, deflate, identity) in the 'Accept-Encoding' request header and respect the 'Content-Encoding' response header
        curl_setopt($this->curl, CURLOPT_ENCODING, '');
        if (!ini_get("open_basedir")) {
            curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        } else {
            $this->followLocation = true;
        }
        $this->setTimeout(HttpRetries::DEFAULT_TIMEOUT);
    }

    /**
     * Set a default timeout for the request. The client will error if the
     * request takes longer than this to respond.
     *
     * @param int $timeout number of seconds to wait on a response
     */
    public function setTimeout($timeout)
    {
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($this->curl, CURLOPT_TIMEOUT_MS, $timeout * 1000); // Forces a hard limit in milliseconds
    }

    /**
     * Throw an exception if the request encounters an HTTP error condition.
     *
     * <p>An error condition is considered to be:</p>
     *
     * <ul>
     *    <li>400-499 - Client error</li>
     *    <li>500-599 - Server error</li>
     * </ul>
     *
     * <p><em>Note that this doesn't use the builtin CURL_FAILONERROR option,
     * as this fails fast, making the HTTP body and headers inaccessible.</em></p>
     *
     * @param bool $option the new state of this feature
     */
    public function failOnError($option = true)
    {
        $this->failOnError = $option;
    }

    /**
     * Add a custom header to the request.
     *
     * @param string $header
     * @param string $value
     */
    public function addHeader($header, $value)
    {
        $this->headers[$header] = "$header: $value";
    }

    /**
     * Remove a header from the request.
     *
     * @param string $header
     */
    public function removeHeader($header)
    {
        unset($this->headers[$header]);
    }

    /**
     * Return an representation of an error returned by the last request, or FALSE
     * if the last request was not an error.
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * Make an HTTP POST request to the specified endpoint.
     *
     * @param string $url  URL to which we send the request
     * @param mixed  $body Data payload (JSON string or raw data)
     *
     * @return mixed
     */
    public function post($url, $body)
    {
        $this->addHeader('Content-Type', $this->getContentType());
        if (!is_string($body)) {
            $body = json_encode($body);
        }
        $this->initializeRequest();
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_PUT, false);
        curl_setopt($this->curl, CURLOPT_HTTPGET, false);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $body);
        curl_exec($this->curl);
        return $this->handleResponse();
    }

    /**
     * Get the MIME type that should be used for this request.
     *
     * Defaults to application/json
     */
    private function getContentType()
    {
        return ($this->contentType) ? $this->contentType : self::MEDIA_TYPE_JSON;
    }

    /**
     * Clear previously cached request data and prepare for
     * making a fresh request.
     */
    private function initializeRequest()
    {
        $this->responseBody = '';
        $this->responseHeaders = array();
        $this->lastError = false;
        $this->addHeader('Accept', $this->getContentType());
        curl_setopt($this->curl, CURLOPT_POST, false);
        curl_setopt($this->curl, CURLOPT_PUT, false);
        curl_setopt($this->curl, CURLOPT_HTTPGET, false);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
    }

    /**
     * Check the response for possible errors and handle the response body returned.
     *
     * If failOnError is TRUE, a client or server error is raised, otherwise returns FALSE
     * on error.
     */
    private function handleResponse()
    {
        if (curl_errno($this->curl)) {
            throw new NetworkError(curl_error($this->curl), curl_errno($this->curl));
        }
        $body = ($this->rawResponse) ? $this->getBody() : json_decode($this->getBody(), 1);
        $status = $this->getStatus();
        if ($status >= 400 && $status <= 499) {
            if ($this->failOnError) {
                throw new ClientError($body, $status);
            } else {
                $this->lastError = $body;
                return false;
            }
        } elseif ($status >= 500 && $status <= 599) {
            if ($this->failOnError) {
                throw new ServerError($body, $status);
            } else {
                $this->lastError = $body;
                return false;
            }
        }
        if ($this->followLocation) {
            $this->followRedirectPath();
        }
        $body['httpStatus'] = $status;
        return $body;
    }

    /**
     * Access the content body of the response
     *
     * @return string
     */
    public function getBody()
    {
        return $this->responseBody;
    }

    /**
     * Access the status code of the response.
     *
     * @return mixed
     */
    public function getStatus()
    {
        return curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
    }

    /**
     * Recursively follow redirect until an OK response is received or
     * the maximum redirects limit is reached.
     *
     * Only 301 and 302 redirects are handled. Redirects from POST and PUT requests will
     * be converted into GET requests, as per the HTTP spec.
     */
    private function followRedirectPath()
    {
        $this->redirectsFollowed++;
        if ($this->getStatus() == 301 || $this->getStatus() == 302) {
            if ($this->redirectsFollowed < $this->maxRedirects) {
                $location = $this->getHeader('Location');
                $forwardTo = parse_url($location);
                if (isset($forwardTo['scheme']) && isset($forwardTo['host'])) {
                    $url = $location;
                } else {
                    $forwardFrom = parse_url(curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL));
                    $url = $forwardFrom['scheme'] . '://' . $forwardFrom['host'] . $location;
                }
                $this->get($url);
            } else {
                $errorString = "Too many redirects when trying to follow location.";
                throw new NetworkError($errorString, CURLE_TOO_MANY_REDIRECTS);
            }
        } else {
            $this->redirectsFollowed = 0;
        }
    }

    /**
     * Access given header from the response.
     *
     * @param string $header Header name to retrieve
     *
     * @return string|void
     */
    public function getHeader($header)
    {
        if (array_key_exists($header, $this->responseHeaders)) {
            return $this->responseHeaders[$header];
        }
        // Do case-insensitive search
        foreach ($this->responseHeaders as $k => $v) {
            if (strtolower($k) == strtolower($header)) {
                return $v;
            }
        }
    }

    /**
     * Make an HTTP GET request to the specified endpoint.
     *
     * @param string     $url     URL to retrieve
     * @param array|bool $query   Optional array of query string parameters
     * @param int        $timeout Optional timeout for the request
     *
     * @return mixed
     */
    public function get($url, $query = false, $timeout = 13)
    {
        $this->initializeRequest();
        if (is_array($query)) {
            $url .= '?' . http_build_query($query);
        }
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_POST, false);
        curl_setopt($this->curl, CURLOPT_PUT, false);
        curl_setopt($this->curl, CURLOPT_HTTPGET, true);
        if ($timeout) {
            curl_setopt($this->curl, CURLOPT_TIMEOUT, $timeout);
        }
        $response = curl_exec($this->curl);
        if ($response === false) {
            return false;
        }
        return $this->handleResponse();
    }

    /**
     * Access the message string from the status line of the response.
     *
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->responseStatusLine;
    }

    /**
     * Return the full list of response headers
     */
    public function getHeaders()
    {
        return $this->responseHeaders;
    }

    /**
     * Close the cURL resource when the instance is garbage collected
     */
    public function __destruct()
    {
        curl_close($this->curl);
    }

    /**
     * Method that appears unused, but is in fact called by curl
     *
     * @param  resource $curl
     * @param  string   $body
     * @return int
     */
    private function parseBody($curl, $body)
    {
        $this->responseBody .= $body;
        return strlen($body);
    }

    /**
     * Method that appears unused, but is in fact called by curl
     *
     * @param  resource $curl
     * @param  string   $headers
     * @return int
     */
    private function parseHeader($curl, $headers)
    {
        if (!$this->responseStatusLine && strpos($headers, 'HTTP/') === 0) {
            $this->responseStatusLine = $headers;
        } else {
            $parts = explode(': ', $headers);
            if (isset($parts[1])) {
                $this->responseHeaders[$parts[0]] = trim($parts[1]);
            }
        }
        return strlen($headers);
    }
}