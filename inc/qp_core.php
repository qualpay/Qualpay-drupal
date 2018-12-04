<?php 

class Settings {
  
  var $url;
  var $method;
  var $timeout;
  var $verbose;
  
  /**
   * Creates default settings of method type POST, verbose false, a timeout of 20s, and validation of request objects true.
   */
  function Settings() {
    $this->method = Method.POST;
    $this->timeout = 20000;
  }
  
  /**
   * The {@link Method} this HTTP request will use.
   * @param method
   * @return this object for chaining.
   */
  function method( $method ) {
    $this->method = $method;
    return( $this );
  }
  
   /**
   * The {@link Method} this url request will use.
   * @param method
   * @return this object for chaining.
   */
  function url( $url ) {
    $this->url = $url;
    return( $this );
  }
  
  /**
   * Set the timeout of the HTTP request.
   * @param timeout
   * @return this object for chaining.
   */
  function timeout( $timeout ) {
    $this->timeout = $timeout;
    return( $this );
  }
  
  /**
   * Standard output for testing.
   * @param verbose
   * @return this object for chaining.
   */
  function verbose( $verbose ) {
    $this->verbose = $verbose;
    return( $this );
  }

  function getMethod() {
    return( $this->method );
  }

  function getTimeout() {
    return( $this->timeout );
  }

  function isVerbose() {
    return( $this->verbose );
  }
  
  function getUrl() {
    return( $this->url );
  }  
}


class Http {
  const POST    = 'POST';
  const GET     = 'GET';

  var $method         = POST;
  var $timeout        = 20000;     // 20 seconds
  var $request;
  var $httpCode;
  var $httpResponse;
  var $contentType;
  var $rawResponse;
  var $duration;
  var $endpoint;
  var $proxyHost;
  var $authStringEnc;


  /**
   * Create a new HTTP object based on the settings set in {@link Settings}.
   * @param settings
   */
  function Http( $settings ) {
    $this->method   = $settings->getMethod();
    $this->timeout  = $settings->getTimeout();
  }

  /**
   * Processes the HTTP request.
   */
  function run() {
    $startTs = microtime();

    $ch = curl_init();

    if ($this->method == POST ) //Use POST
    {
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request);
      curl_setopt($ch, CURLOPT_URL, $this->endpoint);
      $opts = array(
          'Content-Type: application/json',
          'Content-Length: ' . strlen($this->request)
      );
    }
    else //Use GET(cURL default)
    {
      curl_setopt($ch, CURLOPT_POST, false);
      curl_setopt($ch, CURLOPT_URL, $this->endpoint . '?' . $this->request);
      $opts = array(
          'Content-Type: application/x-www-form-urlencoded'
      );
    }
    if ( $this->authStringEnc !== '' ) {
      array_push($opts,'Authorization: Basic ' . $this->authStringEnc);
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $opts );
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HEADER, 1);

    if ( $this->proxyHost != "" )
    {
      curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
      curl_setopt($ch, CURLOPT_PROXY, $this->proxyHost);
    }

    // version 7.16.2 (version_number 0x071602) and above supports timeouts in milliseconds
    $curlVersion = curl_version();
     if ( $curlVersion['version_number'] >= 464386 ) {
      curl_setopt($ch, CURLOPT_TIMEOUT_MS, $this->timeout);
    }
    else {
      curl_setopt($ch, CURLOPT_TIMEOUT, intval($this->timeout/1000));
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $curlResponse       = curl_exec($ch);
    $headerSize         = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header             = substr($curlResponse, 0, $headerSize);
    $headerArray        = $this->parseResponseHeaders($header);
    $this->rawResponse  = substr($curlResponse, $headerSize);
    $this->contentType  = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $this->httpCode     = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ( isset($headerArray["response_text"]) ) {
      $this->httpResponse = $headerArray["response_text"];
    }
    else {
      $this->httpResponse = $this->httpCode;
    }
    curl_close($ch);
    $this->duration = microtime() - startTs;
  }

  function testRun() {
    $this->rawResponse = '{"rcode":"000","rmsg":"Approved T63362", "pg_id":"8af556ae480811e484b20c4de99f0aaf"}';
    $this->httpCode = 200;
    $this->duration = 500;
    $this->contentType = "application/json";
  }

  function setRequestString( $requestString ) {
    $this->request = $requestString;
  }

  function setHost( $endpoint ) {
    $this->endpoint = $endpoint;
  }

  function getRequestString() {
    return( $this->request );
  }

  function getHttpCode() {
    return( $this->httpCode );
  }

  function getHttpText() {
    return( $this->httpResponse );
  }

  function getResponseContentType() {
    return( $this->contentType );
  }

  function getRawResponse() {
    return( $this->rawResponse );
  }

  function getDuration() {
    return( $this->duration );
  }

  function parseResponseHeaders( $raw_headers ) {
    $headers = array();
    $key = '';

    foreach(explode("\n", $raw_headers) as $i => $h)
    {
      $h = explode(':', $h, 2);

      if (isset($h[1]))
      {
        if (!isset($headers[$h[0]])) {
          $headers[$h[0]] = trim($h[1]);
        }
        elseif (is_array($headers[$h[0]])) {
          $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1])));
        }
        else {
          $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1])));
        }
        $key = $h[0];
      }
      else {
        if (substr($h[0], 0, 1) == "\t") {
          $headers[$key] .= "\r\n\t".trim($h[0]);
        }
        elseif (!$key) {
          if( preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)+\s+(.*)#",trim($h[0]), $out ) ) {
            if ( isset($out[1]) ) { $headers['response_code'] = $out[1]; }
            if ( isset($out[2]) ) { $headers['response_text'] = $out[2]; }
          }
          else {
            $headers[0] = trim($h[0]);
          }
        }
      }
    }
    return( $headers );
  }
  
  function setBasicAuth( $authKey ) {
    if ( $authKey !== '' ) {
      $this->authStringEnc = base64_encode("$authKey:");      
    }
    else {
      unset($this->authStringEnc);
    }
  }
}

class RequestObject {
  var $params     = array();
  
  function RequestObject() {    
  }
  
  /**
   * Sets a specific request field and value to be sent.<br />
   * When name or value are null, the call to setParameter will be ignored.
   * @param name
   * @param value
   * @return this for chaining
   */
  function setParameter( $name, $value ) {
    $this->params[$name] = $value;  // Also replaces existing value
    return( $this );
  }
  
  function removeParameter( $name ) {
    unset($this->params[$name]);
  }
  
  function getParameter( $name ) {
    return( $this->params[$name] );
  }
  
}

class  ResponseObject {

  var $response;
  var $httpCode;
  var $httpText;
  var $rawResponse;
  var $duration;

  function ResponseObject($response, $httpCode, $httpText, $rawResponse, $duration ) {
    $this->response     = $response;
    $this->httpCode     = $httpCode;
    $this->httpText     = $httpText;
    $this->rawResponse  = $rawResponse;
    $this->duration     = $duration;
  }

  /**
   * Get the HTTP code returned by the request (200, 404, etc).
   * @return Http Code Integer
   */
  function getHttpCode() {
    return( $this->httpCode );
  }
  
  /**
   * Get the Textual response of the HTTP code (OK, FORBIDDEN, etc).
   * @return Http Code Text
   */
   function getHttpText() {
    return( $this->httpText );
  }
  
  /**
   * Get the raw text from the HTTP request.
   * @return Raw Response String
   */
   function getRawResponse() {
    return( $this->rawResponse );
  }

  /**
   * Get the round trip time the request took to process.
   * @return Request Duration in ms
   */
  function getDuration() {
    return( $this->duration );
  }
  
  /**
   * Search for a response key, returning it's value.<br />
   * If it is not present - (dash) is returned. 
   * @param Key
   * @return Response Value
   */
   function getResponseValue( $key ) {
    return( isset($this->response[$key]) ? $this->response[$key] : "-" );
  }

  function toString() {
    return( "[HTTP:". $this->httpCode . "] [duration:" . $this->duration + "ms] " );
  }
}

?>