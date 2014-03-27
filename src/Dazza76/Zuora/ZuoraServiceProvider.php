<?php namespace Dazza76\Zuora;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use \Config;
//use \Zuora\zuora-api\ClientBuilder;
//date_default_timezone_set('Australia/Sydney');
//use  \Zuora\zuora-api\API.php; 
// require_once 'functions.php';

//$config = new stdClass();
//$config->wsdl = $wsdl;
//$instance = Zuora_API::getInstance($config);
//$instance->setQueryOptions($query_batch_size);
# LOGIN
//$instance->setLocation($endpoint);
//$instance->login($username, $password);

class ZuoraServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot() {
		$this->package( 'dazza76/zuora' );
	}

	/**
	 * Register the service provider.
	 *
	 * @return PHPForce
	 */
	public function register() {
		$this->app[ 'zuora' ] = $this->app->share( function( $app ) {

	    	// connection credentials loaded from config
	        $username = Config::get( 'zuora::username' );
	        $password = Config::get( 'zuora::password' );
	        $wsdl = Config::get( 'zuora::wsdl' );
	        $endpoint = Config::get( 'zuora::endpoint' );
		$config = new stdClass();
		$config->wsdl = $wsdl;
		$instance = Zuora_API::getInstance($config);
		$instance->setQueryOptions($query_batch_size);
		# LOGIN
		$instance->setLocation($endpoint);
		$instance->login($username, $password);
		return $instance;
	    });

    	// Shortcut so developers don't need to add an Alias in app/config/app.php
	    $this->app->booting( function() {
	        $loader = AliasLoader::getInstance();
	        $loader->alias( 'Zuora', 'Dazza76\Zuora\Facades\Zuora' );
	    });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return array();
	}




public function queryAll($instance, $query){

    $moreCount = 0;
    $recordsArray = array();
    $totalStart = time();

    $start = time();
    $result = $instance->query($query);
    $end = time();
    $elapsed = $end - $start;

    $done = $result->result->done;
    $size = $result->result->size;
    $records = $result->result->records;

    if ($size == 0){
    } else if ($size == 1){
        array_push($recordsArray, $records);
    } else {

        $locator = $result->result->queryLocator;
        $newRecords = $result->result->records;
        $recordsArray = array_merge($recordsArray, $newRecords);

        while (!$done && $locator && $moreCount == 0){

            $start = time();
            $result = $instance->queryMore($locator);
            $end = time();
            $elapsed = $end - $start;

            $done = $result->result->done;
            $size = $result->result->size;
            $locator = $result->result->queryLocator;
            print "\nqueryMore";

            $newRecords = $result->result->records;
            $count = count($newRecords);
            if ($count == 1){
                array_push($recordsArray, $newRecords);
            } else {
                $recordsArray = array_merge($recordsArray, $newRecords);
            }

        }
    }

    $totalEnd = time();
    $totalElapsed = $totalEnd - $totalStart;

    return $recordsArray;

}

public function getPostValue($name,$default=null){
        $v = $_POST[$name];
        if(!isset($v)){
                $v = $default;
        }
        return $v;
}
public function isEmpty($var){
        return !isset($var) or trim($var)=='';
}

public function makeAccount($name,$currency,$status){
    $zAccount = new Zuora_Account();
    $zAccount->AllowInvoiceEdit = 1;
    $zAccount->AutoPay = 0;
    $zAccount->Batch = 'Batch1';
    $zAccount->BillCycleDay = 1;
    $zAccount->Currency = $currency;
    $zAccount->Name = $name;
    $zAccount->PaymentTerm = 'Due Upon Receipt';
    $zAccount->Status = $status;

                //$zAccount->CrmId = 'SFDC-1223471249003';
                //$zAccount->PurchaseOrderNumber = 'PO-1223471249003';
                return $zAccount;
}
public function makeContact($firstName,$lastName,$address1,$address2,$city,$state,$country,$postalCode,$workMail,$workPhone,$accountId=null){

          $zBillToContact = new Zuora_Contact();

    $zBillToContact->FirstName = $firstName;
    $zBillToContact->LastName = $lastName;
    $zBillToContact->Address1 = $address1;
    $zBillToContact->Address2 = $address2;
    $zBillToContact->City = $city;
    $zBillToContact->State = $state;
    $zBillToContact->Country = $country;
    $zBillToContact->PostalCode = $postalCode;
    $zBillToContact->WorkEmail = $workMail;
    $zBillToContact->WorkPhone = $workPhone;
                $zBillToContact->AccountId = $accountId;

                return  $zBillToContact;
}
public function makePaymentMethod($creditCardHolderName, $address1,$address2, $city, $state, $country, $postalCode, $creditCardType, $creditCardNumber, $creditCardExpirationMonth, $creditCardExpirationYear,$accountId=null){
          $zPaymentMethod = new Zuora_PaymentMethod();
    $zPaymentMethod->AccountId = $accountId;

    $zPaymentMethod->CreditCardAddress1 = $address1;
    $zPaymentMethod->CreditCardAddress2 = $address2;
    $zPaymentMethod->CreditCardCity = $city;
    $zPaymentMethod->CreditCardCountry = $country;
    $zPaymentMethod->CreditCardExpirationMonth = $creditCardExpirationMonth;
    $zPaymentMethod->CreditCardExpirationYear = $creditCardExpirationYear;
    $zPaymentMethod->CreditCardHolderName = $creditCardHolderName;
    $zPaymentMethod->CreditCardNumber = $creditCardNumber;
    $zPaymentMethod->CreditCardPostalCode = $postalCode;
    $zPaymentMethod->CreditCardState = $state;
    $zPaymentMethod->CreditCardType = $creditCardType;

    $zPaymentMethod->Type = 'CreditCard';
    return $zPaymentMethod;
}
public function makeSubscription($subscriptionName, $subscriptionNotes){
          $date = date('Y-m-d\TH:i:s',time());

          $zSubscription = new Zuora_Subscription();

    $zSubscription->Name = $subscriptionName;
                $zSubscription->Notes = $subscriptionNotes;

    $zSubscription->ContractAcceptanceDate = $date;
    $zSubscription->ContractEffectiveDate = $date;

    $zSubscription->InitialTerm = 12;
    $zSubscription->RenewalTerm = 12;
    $zSubscription->ServiceActivationDate = $date;

    $zSubscription->TermStartDate=$date;
                $zSubscription->Status = 'Active';
                $zSubscription->Currency = 'USD';
                $zSubscription->AutoRenew = 1;

                return  $zSubscription;
}
public function setRatePlanData($zSubscriptionData,$chargeIds,$rateplancharges,$productRatePlanId){
          $zRatePlan = new Zuora_RatePlan();
    $zRatePlan->AmendmentType = 'NewProduct';

    $zRatePlan->ProductRatePlanId = $productRatePlanId;
    $zRatePlanData = new Zuora_RatePlanData($zRatePlan);

    foreach($chargeIds as $cid){
        foreach($rateplancharges as $rc){
                if($rc->Id == $cid){
                                        $rpc = new Zuora_RatePlanCharge();
                            $rpc->ProductRatePlanChargeId = $cid;
                            if($rc->DefaultQuantity>0){
                                $rpc->Quantity =  1;
                                }
                            $rpc->TriggerEvent ="ServiceActivation";

                            $zPlanChargeData = new Zuora_RatePlanChargeData($rpc);


                            $zRatePlanData->addRatePlanChargeData($zPlanChargeData);
                }
        }
    }

  $zSubscriptionData->addRatePlanData($zRatePlanData);

}

public function makeSubscriptionData($subscription,$chargeIds,$rateplancharges,$rateplanId){
         $zSubscriptionData = new Zuora_SubscriptionData($subscription);
   setRatePlanData($zSubscriptionData,$chargeIds,$rateplancharges,$rateplanId);
         return $zSubscriptionData;
}



}
