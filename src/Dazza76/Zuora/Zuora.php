<?php
/**
 * Zuora.php
 *
 * @package default
 */


namespace Dazza76\Zuora;

//require_once 'zuora-api/API.php';
//use Dazza76\Zuora\zuora-api\API.php;
use Dazza76\Zuora\lib;

class Zuora {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

  /**
     * The active zuora connection resource id.
     */
    protected $connectionId;

    /**
     * Create a new Zuora connection instance.
     *
     * @param  config
     * @return void
     */
    public function __construct($config)
    {
        $this->connectionId = $this->connect($config);
    }


    /**
     * Establish zuora connection
     * 
     * @param $config
     * @return resource
     * @throws \Exception
     */
    public function connect($config)
    {
	$connectconfig = new \stdClass();
	$connectconfig->wsdl = public_path().'/wsdl/'.$config['wsdl'];
	$instance = \Zuora_API::getInstance($connectconfig);
	$instance->setQueryOptions(100);
	// LOGIN
	$instance->setLocation($config['endpoint']);
	$instance->login($config['username'], $config['password']);
        if ((!$instance))
            throw new \Exception('Zuora connection has failed!');
        return $instance;
    }

    /**
     * Disconnect active connection.
     *
     * @param  config
     * @return void
     */
    public function disconnect()
    {
//        zuora_close($this->connectionId);
    }

	/**
	 *
	 * @param unknown $instance
	 * @param unknown $query
	 * @return unknown
	 */
	public function queryAll($query) {
		$instance = $this->connectionId;
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

		if ($size == 0) {
		} else if ($size == 1) {
				array_push($recordsArray, $records);
			} else {

			$locator = $result->result->queryLocator;
			$newRecords = $result->result->records;
			$recordsArray = array_merge($recordsArray, $newRecords);

			while (!$done && $locator && $moreCount == 0) {

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
				if ($count == 1) {
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


	function uploadUsages($usages){
	$instance = $this->connectionId;
        $zUsages = array();
        foreach($usages as $usage){
                $zUsage = new \Zuora_Usage();
                $zUsage->AccountId = $usage['AccountId'];
                $zUsage->SubscriptionId = $usage['SubscriptionId'];
                $zUsage->Quantity  = $usage['Quantity'];
                $zUsage->StartDateTime = date("Y-m-d\TH:i:s",\strtotime($usage['StartDateTime']));
                $zUsage->EndDateTime = date("Y-m-d\TH:i:s",\strtotime($usage['EndDateTime']));

                $zUsage->UOM = $usage['UOM'];
                $zUsage->ChargeId = $usage['ChargeId'];
                $zUsage->Description =  $usage['Description'];
                $zUsages[] = $zUsage;
        }
	$result = $instance->create($zUsages);
        return $result;
}

	/**
	 *
	 * @param unknown $name
	 * @param unknown $default (optional)
	 * @return unknown
	 */
	public function getPostValue($name, $default=null) {
		$v = $_POST[$name];
		if (!isset($v)) {
			$v = $default;
		}
		return $v;
	}


	/**
	 *
	 * @param unknown $var
	 * @return unknown
	 */
	public function isEmpty($var) {
		return !isset($var) or trim($var)=='';
	}


	/**
	 *
	 * @param unknown $name
	 * @param unknown $currency
	 * @param unknown $status
	 * @return unknown
	 */
	public function makeAccount($name, $currency, $status) {
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


	/**
	 *
	 * @param unknown $firstName
	 * @param unknown $lastName
	 * @param unknown $address1
	 * @param unknown $address2
	 * @param unknown $city
	 * @param unknown $state
	 * @param unknown $country
	 * @param unknown $postalCode
	 * @param unknown $workMail
	 * @param unknown $workPhone
	 * @param unknown $accountId  (optional)
	 * @return unknown
	 */
	public function makeContact($firstName, $lastName, $address1, $address2, $city, $state, $country, $postalCode, $workMail, $workPhone, $accountId=null) {

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


	/**
	 *
	 * @param unknown $creditCardHolderName
	 * @param unknown $address1
	 * @param unknown $address2
	 * @param unknown $city
	 * @param unknown $state
	 * @param unknown $country
	 * @param unknown $postalCode
	 * @param unknown $creditCardType
	 * @param unknown $creditCardNumber
	 * @param unknown $creditCardExpirationMonth
	 * @param unknown $creditCardExpirationYear
	 * @param unknown $accountId                 (optional)
	 * @return unknown
	 */
	public function makePaymentMethod($creditCardHolderName, $address1, $address2, $city, $state, $country, $postalCode, $creditCardType, $creditCardNumber, $creditCardExpirationMonth, $creditCardExpirationYear, $accountId=null) {
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


	/**
	 *
	 * @param unknown $subscriptionName
	 * @param unknown $subscriptionNotes
	 * @return unknown
	 */
	public function makeSubscription($subscriptionName, $subscriptionNotes) {
		$date = date('Y-m-d\TH:i:s', time());

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


	/**
	 *
	 * @param unknown $zSubscriptionData
	 * @param unknown $chargeIds
	 * @param unknown $rateplancharges
	 * @param unknown $productRatePlanId
	 */
	public function setRatePlanData($zSubscriptionData, $chargeIds, $rateplancharges, $productRatePlanId) {
		$zRatePlan = new Zuora_RatePlan();
		$zRatePlan->AmendmentType = 'NewProduct';

		$zRatePlan->ProductRatePlanId = $productRatePlanId;
		$zRatePlanData = new Zuora_RatePlanData($zRatePlan);

		foreach ($chargeIds as $cid) {
			foreach ($rateplancharges as $rc) {
				if ($rc->Id == $cid) {
					$rpc = new Zuora_RatePlanCharge();
					$rpc->ProductRatePlanChargeId = $cid;
					if ($rc->DefaultQuantity>0) {
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


	/**
	 *
	 * @param unknown $subscription
	 * @param unknown $chargeIds
	 * @param unknown $rateplancharges
	 * @param unknown $rateplanId
	 * @return unknown
	 */
	public function makeSubscriptionData($subscription, $chargeIds, $rateplancharges, $rateplanId) {
		$zSubscriptionData = new Zuora_SubscriptionData($subscription);
		setRatePlanData($zSubscriptionData, $chargeIds, $rateplancharges, $rateplanId);
		return $zSubscriptionData;
	}



}
