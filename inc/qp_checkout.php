<?php 
require_once ( __DIR__ .'/qp_core.php');


class Preferences {
   
  /**
   *  Identifies the Transaction type of the payment gateway request when  the customer  
   *  submits the payment data on the checkout page. 
   *
   */
  /** Authorization Only **/
  const AUTH      = "auth";
  /** Authorization followed by immediate capture **/
  const SALE      = "sale";
  
  const DAYS      = 0;
  const HOURS     = 1;
  const MIN       = 2;
  const SEC       = 3;
     
  /**
   *  Do not use camelCase variable names here as these 
   *  variables are used as the field names for the JSON
   */
  var $notification_url;
  var $success_url;
  var $failure_url;
  var $allow_partial_payments;
  var $email_receipt;
  var $request_type;
  var $expire_in_secs;
  
  /**
   * Returns the Notification URL. This URL is provided by the merchant and it will be notified
   * when a checkout payment is made. Qualpay will  send  a 
   * Post  message to  the URL.  
   * @return the notification URL
   */
  function getNotificationUrl() {
    return( $this->notification_url );
  }
  
  /**
   * Sets the Notification URL. This URL is provided by the merchant and it will be notified
   * when a checkout payment is made. Qualpay will  send  a 
   * Post  message to  the URL. 
   * @param notificationUrl the notification URL
   * @return the {@link Preferences} instance
   */
  function setNotificationUrl( $notificationUrl ) {
    $this->notification_url = $notificationUrl;
    return( $this );
  }
  
  /**
   * Returns the success URL, $a URL to  which the customer  will  be  directed to after a successful  payment
   * @return the success URL
   */
  function getSuccessUrl() {
    return( $this->success_url );
  }
  
  /** Sets the success URL,  a URL to  which the customer  will  be  directed to after a successful  payment
   * @param successUrl the success URL
   * @return the {@link Preferences} instance
   */
  function setSuccessUrl( $successUrl ) {
    $this->success_url = $successUrl;
    return( $this );
  }
  
  /**
   * Returns the success URL, a URL to  which the customer  will  be  directed to after a  payment decline
   * @return the failure URL
   */
  function getFailureUrl() {
    return( $this->failure_url );
  }
  
  /** Sets the failure URL,  a URL to  which the customer  will  be  directed to after a payment decline
   * @param failureURL the failure URL
   * @return the {@link Preferences} instance
   */
  function setFailureUrl( $failureUrl ) {
    $this->failure_url = $failureUrl;
    return( $this );
  }
  
  /**
   * Returns a Boolean object  that indicates if the customer can edit the transaction amount on Qualpay checkout.
   * @return true if customer can make partial payments
   */
  function allowPartialPayments() {
    return( $this->allow_partial_payments );
  }
  
  /**
   * Sets the flag that indicates if customer can edit  the transaction amount  on Qualpay checkout.When  set to  true, the 
   * customer  can make  changes to  the payment amount. 
   * @param allowPartialPayments true if customer can make partial payments
   * @return the {@link Preferences} instance
   */
  function setAllowPartialPayments( $allowPartialPayments ) {
    $this->allow_partial_payments = $allowPartialPayments;
    return( $this );
  }
  
  /**
   * Returns a boolean object  that indicates if the receipt will be e-mailed to  the customer. 
   * @return true if receipt will be e-mailed 
   */
  function emailReceipt() {
    return( $this->email_receipt );
  }
  
  /**
   * Sets a boolean variable that indicates if the receipt e-mail will be sent to the customer. If set to true
   * and the customer  email address is  provided, the receipt is  sent  to  the customer
   * @param emailReceipt true if the receipt will be e-mailed
   * @return the {@link Preferences} instance
   */
  function setEmailReceipt( $emailReceipt ) {
    $this->email_receipt = $emailReceipt;
    return( $this );
  }
  
  /**
   * Returns the request type.  A request type represents the payment gateway transaction type when the customer submits the 
   * payment data  on  the checkout  page. 
   * @return the request type. See {@link RequestType} for valid values
   */
  function getRequestType() {
    return( $this->request_type );
  }
  
  /**
   * Sets the request type. A request type represents the payment gateway transaction type when the customer submits the 
   * payment data  on  the checkout  page.
   * @param requestType  the request type. See {@link RequestType} for valid values
   * @return the {@link Preferences} instance
   */
  function setRequestType( $requestType ) {
    $this->request_type = $requestType;
    return( $this );
  }
  
  /**
   * Returns the time  period  for which the  checkout link will  be  valid in  seconds.
   * @return the expiration time period
   */
  function getExpireInSecs() {
    return( $this->expire_in_secs );
  }
  
  /**
   * Sets the time period for which the checkout link will be valid in seconds.
   * @param expireInSecs the expiration time period
   * @return the {@link Preferences} instance
   */
  function setExpireInSecs( $expireInSecs ) {
    $this->expire_in_secs = $expireInSecs;
    return( $this );
  }
  
  /**
   * Sets the time period for which the checkout link will be valid.
   * @param num the expiration time
   * @param unit expiration time unit - Days, Hours, Minutes, Seconds
   * @return the {@link Preferences} instance
   */
  function setExpireIn($num, $unit) {
    switch( $unit ) {
      case self::DAYS:
        $this->setExpireInSecs($num * 24 * 60 * 60);
        break;
      case self::HOURS:
        $this->setExpireInSecs($num * 60 * 60);
        break;
      case self::MIN:
        $this->setExpireInSecs($num * 60);
        break;
      case self::SEC:
        $this->setExpireInSecs($num);
        break;
    }
    return( $this );
  }  
  
  /**
   * Returns a Json String representation of the Preferences object 
   * @return JSON String representation of the {@link Preferences} instance
   */
  function getJsonString(){
    // encode the params as JSON and remove any fields with a value of null
    return( preg_replace('/,\s*"[^"]+":null|"[^"]+":null,?/', '', json_encode($this)) );
  }
}

class Transaction {  
  var $txnMap;
  
  function Transaction ( $txn ) {
    $this->txnMap = $txn;
  }
  
  function getTransactionValue( $key ) {
    return( $this->txnMap[$key] );
  }
}

class CheckoutSettings extends Settings {

  const UNIT_TEST     = "https://donotuse.qualpay.com";
  const QUALPAY_TEST  = "https://app-test.qualpay.com/service/api/checkout";
  const QUALPAY_PROD  = "https://app.qualpay.com/service/api/checkout";
  
	var $securityKey;	

	function CheckoutSettings() {
		parent::url(QUALPAY_TEST);
	}
	
  function method( $method ) {
    if( $method !== Http::POST && $method !== Http::GET ) {
      throw new QpRuntimeException("Qualpay Checkout only supports POST and GET transactions.");
    }
    return( parent::method($method) );
  }

  /**
	 * Sets the security key which used to identify the merchant
	 * @param key
	 */
	function credentials( $key ) {
		$this->securityKey = $key;
		return( $this );
	}
	
	/** @return the Security Key */
	function getSecurityKey() {
		return( $this->securityKey );
	}
	
}

class CheckoutRequest extends RequestObject {
  
  /**
   * Returns the total amount of the transaction including sales tax (if applicable).
   * @return the total transaction amount
   */
  function getAmtTran() {
    return( $this->getParameter("amt_tran") );
  }
  
  /**
   * Sets the total amount of the transaction including sales tax(if applicable)
   * @param amtTran a float value representing the total amount of the transaction
   * @return The {@link CheckoutRequest} instance
   */
  function setAmtTran( $amtTran ) {
    $this->setParameter("amt_tran", $amtTran);
    return( $this );
  }
  
  /**
   * Gets the ISO numeric currency code for the transaction. 
   * @return the ISO numeric currency code for the transaction
   */
  function getTranCurrency() {
    return( $this->getParameter("tran_currency") );
  }
  
  /**
   * Sets the ISO numeric currency code for the transaction. If the profile_id field is  provided, 
   * the profile's currency will override this value
   * @param tranCurrency the ISO numeric currency code for the transaction
   * @return The {@link CheckoutRequest} instance
   */
  function setTranCurrency( $tranCurrency ) {
    $this->setParameter("tran_currency", $tranCurrency);
    return( $this );
  }
  
  /**
   * Gets the Qualpay Payment gateway Profile ID for the transaction. 
   * @return the profile ID to be used when making a checkout payment
   */
  function getProfileId() {
    return( $this->getParameter("profile_id") );
  }
  
  /**
   * The unique Qualpay Payment Gateway Profile ID to be used when making Checkout payments. 
   * Use this if you have multiple gateway profiles for the same currency or if you prefer the checkout payments
   * to be processed using a specific profile ID. If set, the profile ID derives the currency to be used for payments
   * @param profileId the payment gateway profile Identifier
   * @return The {@link CheckoutRequest} instance
   */
  function setProfileId( $profileId ) {
    $this->setParameter("profile_id", $profileId);
    return( $this );
  }
  
  /**
   * Gets the qualpay checkout preferences
   * @return The {@link Preferences} instance
   */
  function getPreferences() {
    return( $this->getParameter("preferences") );
  }
  
  /**
   * Sets the Qualpay Checkout Preferences. Preferences set here overrides the preferences set in Qualpay
   * Manager. 
   * @param preferences the {@link Preferences} instance
   * @return The {@link CheckoutRequest} instance
   */
  function setPreferences( $preferences ) {
    $this->setParameter("preferences", $preferences );
    return( $this );
  }
  
  /**
   * Gets the customer first name
   * @return the customer first name
   */
  function getCustomerFirstName() {
    return( $this->getParameter("customer_first_name") );
  }
  
  /**
   * Sets the customer  first name.
   * @param customerFirstName the customer first name
   * @return The {@link CheckoutRequest} instance
  */
  function setCustomerFirstName( $customerFirstName ) {
    $this->setParameter("customer_first_name", $customerFirstName);
    return( $this );
  }
  
  /**
   * Gets the customer last name
   * @return the customer last name
  */
  function getCustomerLastName() {
    return( $this->getParameter("customer_last_name") );
  }
  
  /**
   * Sets the customer  last  name.
   * @param customerLastName the customer last name
   * @return The {@link CheckoutRequest} instance
   */
  function setCustomerLastName( $customerLastName ) {
    $this->setParameter("customer_last_name", $customerLastName);
    return( $this );
  }
  
  /**
   * Gets the customer email address.
   * @return the customer email address
  */
  function getCustomerEmail() {
    return( $this->getParameter("customer_email") );
  }
  
  /**
   * Sets the customer email address.
   * @param customerEmail the customer email address
   * @return The {@link CheckoutRequest} instance
   */
  function setCustomerEmail( $customerEmail ) {
    $this->setParameter("customer_email", $customerEmail);
    return( $this );
  }
  
  /**
   * Gets the customer phone number
   * @return the customer phone number
   */
  function getCustomerPhone() {
    return( $this->getParameter("customer_phone") );
  }
  
  /**
   * Sets the customer phone number.
   * @param customerPhone the customer phone number
   * @return The {@link CheckoutRequest} instance
   */
  function setCustomerPhone( $customerPhone ) {
    $this->setParameter("customer_phone", $customerPhone);
    return( $this );
  }
  
  /**
   * Gets the billing address of the customer
   * @return the billing address
   */
  function getBillingAddr1() {
    return( $this->getParameter("billing_addr1") );
  }
  
  /** 
   * Sets the billing address of the customer
   * @param billingAddr1 the billing address
   * @return The {@link CheckoutRequest} instance
   */
  function setBillingAddr1( $billingAddr1 ) {
    $this->setParameter("billing_addr1", $billingAddr1);
    return( $this );
  }
  
  /**
   * Gets the billing city of the customer
   * @return the billing city
   */
  function getBillingCity() {
    return( $this->getParameter("billing_city") );
  }
  
  /**
   * Sets the billing city of the customer
   * @param billingCity the billing city
   * @return The {@link CheckoutRequest} instance
   */
  function setBillingCity( $billingCity) {
    $this->setParameter("billing_city", $billingCity);
    return( $this );
  }
  
  /**
   * Gets the billing state of the customer
   * @return the billing state
   */
  function getBillingState() {
    return( $this->getParameter("billing_state") );
  }
  
  /**
   * Sets the billing state of the customer
   * @param billingState the billing state
   * @return The {@link CheckoutRequest} instance
   */
  function setBillingState( $billingState ) {
    $this->setParameter("billing_state", $billingState);
    return( $this );
  }
  
  /**
   * Gets the Billing zip code of the customer
   * @return the billing zip code
   */
  function getBillingZip() {
    return( $this->getParameter("billing_zip") );
  }
  
  /**
   * Sets the Billing zip code of the customer
   * @param billingZip the billing zip code
   * @return The {@link CheckoutRequest} instance
   */
  function setBillingZip( $billingZip ) {
    $this->setParameter("billing_zip", $billingZip);
    return( $this );
  }
  
  /**
   * Gets the purchase identifier (also referred to as the Invoice Number) generated by the merchant.
   * @return the purchase identifier
   */
  function getPurchaseId() {
    return( $this->getParameter("purchase_id") );
  }
  
  /**
   *  Sets the  purchase  identifier  (also referred  to  as  the invoice number) generated by  the merchant.
   * @param purchaseId the purchase Identifier
   * @return The {@link CheckoutRequest} instance
   */
  function setPurchaseId( $purchaseId ) {
    $this->setParameter("purchase_id", $purchaseId);
    return( $this );
  }
  
  /**
   * Gets a merchant provided reference value that will be stored with the transaction data and will be 
   * included  with the transaction  data  reported  in  the Qualpay Manager.
   * @return the merchant reference number
  */
  function getMerchRefNum() {
    return( $this->getParameter("merch_ref_num") );
  }
  
  /**
   * Sets the merchant reference number which is  a merchant provided reference value that will be stored with the transaction data and will be 
   * included  with the transaction  data  reported  in  the Qualpay Manager
   * @param merchRefNum the merchant reference number
   * @return The {@link CheckoutRequest} instance
  */
  function setMerchRefNum( $merchRefNum ) {
    $this->setParameter("merch_ref_num", $merchRefNum);
    return( $this );
  }
  
  
}

class CheckoutResponse extends ResponseObject {
  var $status;

  function CheckoutResponse($response, $httpCode, $httpText, $rawResponse, $duration, $status) {
    parent::ResponseObject($response, $httpCode, $httpText, $rawResponse, $duration);
    $this->status = $status;
  }
  
  /**
   * Checkout resource creation return code
   * @return return code
   */
  function getCode(){
    return( $this->getResponseValue("code") );
  }
  
  /**
   * Checkout resource creation response message
   * @return message
   */
  function getMessage(){
    return( $this->getResponseValue("message") );
  }

  /**
   * A unique identifier for a checkout resource. 
   * @return checkout identifier
   */
  function getCheckoutId() {
    return( $this->getResponseValue("checkout_id") );
  }
  
  /**
   * The link to the checkout page. 
   * @return checkout link
   */
  function getCheckoutLink() {
    return( $this->getResponseValue("checkout_link") );
  }
  
  /**
   * Qualpay generated internal identifier to identify a merchant. 
   * @return the merchant identifier
   */
  function getMerchantId() {
    return( $this->getResponseValue("merchant_id") );
  }
  
  /**
   * This field contains the timestamp when the checkout_link will  expire. The timestamp is in 
   * ISO 8601 standard  format - "yyyy-MMdd'T'HH:mm:ss.SSSZ"
   * @return the checkout link expiry timestamp
   */
  function getExpiryTime() {
    return( $this->getResponseValue("expiry_time") );
  }
  
  /**
   * Returns the time the checkout resource was created. The timestamp is in ISO  8601  standard  format
   * - "yyyy-MMdd'T'HH:mm:ss.SSSZ"
   * @return checkout resource created timestamp
   */
  function getCreationTime(){
    return( $this->getResponseValue("creation_time") );
  } 
  
  /**
   * Returns the total amount of the transaction including sales tax (if applicable).
   * @return the total transaction amount
   */
  function getAmtTran(){
    return( $this->getResponseValue("amt_tran") );
  } 
  
  /**
   * Gets the ISO numeric currency code for the transaction. 
   * @return the ISO numeric currency code for the transaction
   */
  function getTranCurrency(){
    return( $this->getResponseValue("tran_currency") );
  } 
  
  /**
   * Gets the purchase identifier (also referred to as the Invoice Number) generated by the merchant.
   * @return the purchase identifier
   */
  function getPurchaseId(){
    return( $this->getResponseValue("purchase_id") );
  }
  
  /**
   * Returns status of response. 
   * @return true if successful
   */
  function isSuccessful() {
    return( $this->status );
  }
  
  /**
   * Gets a list of transaction Objects. A transaction contains details about a payment gateway request made against the checkout.
   * A checkout resource can have multiple transaction objects if partial payment is enabled. This method returns a list of such transaction objects
   * for a specific checkout
   * @return a {@link List} of {@link Transaction} instances
   */
  function getTransactions(){
    return( isset($this->response["transactions"]) ? $this->response["transactions"] : array() );
  }
  
}

class Checkout {
  var $settings;

  /**
   * @param settings An instance of {@link CheckoutSettins}.
   */
  function Checkout( $settings ) {
    $this->settings = $settings;
  }
  
  /**
   * Creates a new Checkout resource
   * @param requestObject An instance of {@link CheckoutRequest}
   * @return An instance of {@link CheckoutResponse}
   */
  function create( $requestObject ) {
    $reqBody  = $this->parseRequest($requestObject);
    $endpoint = $this->settings->getUrl();
    
    if ( $this->settings->isVerbose() ) {
      echo "Request to " . $endpoint . "\n";
      echo "Request Body:\n" . $reqBody . "\n";
    }
    $this->settings->method(Http::POST);
    $http = new Http($this->settings);
    $http->setHost($endpoint);
    $http->setRequestString($reqBody);
    $http->setBasicAuth($this->settings->getSecurityKey());

    $http->run();
    $resp = $this->parseResponse($http);
    if($this->settings->isVerbose()) {
      echo "Response Body:\n" . $resp->getRawResponse() . "\n";
    }
    return( $resp );
  }
  
  /**
   * Queries a checkout resource based on checkout identifier
   * @param checkoutId the checkout Identifier
   * @return An instance of {@link CheckoutResponse}
   */
  function read( $checkoutId ) {
    $endpoint = $this->settings->getUrl() . "/" . $checkoutId;
    
    if($this->settings->isVerbose()) {
      echo "Request to " . $endpoint . "\n";
    }
    $this->settings->method(Http::GET);
    $http = new Http($this->settings);
    $http->setHost($endpoint);
    $http->setBasicAuth($this->settings->getSecurityKey());

    $http->run();
    $resp = $this->parseResponse($http);
    if($this->settings->isVerbose()) {
      echo "Response Body:\n" . $resp->getRawResponse() . "\n";
    }
    return( $resp );
  }
  
  /**
   * Parses the input request object and generates a JSON String. This string will serve as
   * a request to the API
   * @param req an instance of {@link CheckoutRequest}
   * @return a json string representation of the request
   */
  function parseRequest( $req ) {
    // encode the params as JSON and remove any fields with a value of null
    return( preg_replace('/,\s*"[^"]+":null|"[^"]+":null,?/', '', json_encode($req->params)) );
  }

  /**
   * Parses the response from the API and generates a CheckoutResponse object
   * @param an instance of {@link Http}
   * @return an instance of {@link CheckoutResponse}
   */
  function parseResponse( $http ) {
     $isSuccessful  = false;
     $respValues    = array();

     if( "application/json" != $http->getResponseContentType() ) {
       $respValues["rmsg"]   = "Communication Error (Unsupported response format ".$http->getResponseContentType().")";
       $respValues["rcode"]  = "999";
     }
     else {
       $responseBody = $http->getRawResponse();
       if( strlen(trim($responseBody)) > 0) {
         $jsonObj               = json_decode($responseBody);
         $respValues["code"]    = $jsonObj->code;
         $respValues["message"] = $jsonObj->message;         
         if ( isset($jsonObj->data) ) {
           foreach($jsonObj->data as $key=>$value){
             $respValues[$key] = $value;
           }
         }
         if ( $jsonObj->code === 0 ) {
           $isSuccessful = true;
         }         
       }
     }

     $gResp = new CheckoutResponse($respValues,
         $http->getHttpCode(),
         $http->getHttpText(),
         $http->getRawResponse(),
         $http->getDuration(),
         $isSuccessful
         );
     return( $gResp );
  }
  
  function getResponseObject( $responseBody ) {
    $resp = json_decode($responseBody);
    return( $resp );
  }
  
}

?>