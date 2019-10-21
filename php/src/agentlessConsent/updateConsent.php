<?php

// SET SOME IMPORTANT VALUES
$pingfed = "https://pingfederate";
$pingfedPort = $_ENV["PF_BASE_PORT"];
$pingdir = "https://localhost";
$pingdirPort = "1443";

// External Consent values
$adapterId = $_ENV["CONSENT_APP"];
$adapterPwd = $_ENV["CONSENT_APP_PWD"];
$adapterCred = base64_encode($adapterId . ":" . $adapterPwd);

// Set values for ConsentAPI calls
$consentDef=$_ENV["CONSENT_DEF"];
$consentAdmin=$_ENV["CONSENT_ADMIN"];
$consentPwd=$_ENV["CONSENT_PWD"];
$consentCred = base64_encode($consentAdmin . ":" . $consentPwd);

// GET SCOPES AND RESUME FROM POST
$finalScopes = $_POST['finalScopes'];
$resumePath = $_POST['resumePath'];
$userId = $_POST['userId'];
$audienceId = $_POST['audienceId'];
$appName = $_POST['appName'];

// build curl object to identify existing consents
// =======================================================================

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_PORT => $pingdirPort,
  CURLOPT_URL => $pingdir. "/consent/v1/consents?definition=" . $consentDef . "&subject=" . $userId . "",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_SSL_VERIFYHOST => false,
  CURLOPT_SSL_VERIFYPEER => false,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "accept: application/json",
    "authorization: Basic " . $consentCred,
    "cache-control: no-cache",
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error: $err";
  exit();
}

// parse response to identify existing consents
// =======================================================================

$responseData = json_decode($response, false);

$existingConsent = $responseData->count;
$grabLatest = $existingConsent - 1;


// there is an existing consent already
if ($existingConsent > 0){
	$existingId = $responseData->_embedded->consents[$grabLatest]->id;
		
		$uidArray = array("scopes" => $finalScopes);


		$data2 = array("status" => "accepted",
			"subject" => $userId,
			"actor" => $userId,
			"data" => $uidArray
		 );

		$data2_string = json_encode($data2);
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_PORT => $pingdirPort,
		  CURLOPT_URL => $pingdir. "/consent/v1/consents/" . $existingId,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_SSL_VERIFYHOST => false,
		  CURLOPT_SSL_VERIFYPEER => false,
		  CURLOPT_CUSTOMREQUEST => "PATCH",
		  CURLOPT_POSTFIELDS => $data2_string,

		  CURLOPT_HTTPHEADER => array(
			"Content-Type: application/json",
			"authorization: Basic " . $consentCred,
		  ),
		));


		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error: $err";
		  exit();
		}

		

} else {


		//one day I won't be lazy and actually pull this from another API call
		$definitionArray = array("id" => $consentDef,
                     "version" => "1.0",
                     "locale" => "en");
                     
                     
		$uidArray = array("scopes" => $finalScopes);


		$data2 = array("status" => "accepted",
			"subject" => $userId,
			"actor" => $userId,
			"audience" => $audienceId,
			"definition" => $definitionArray,
			"dataText" => "This record defines the OAuth Scopes allowed",
			"purposeText" => "Used to tell PF what scopes to include in the access_token",
			"data" => $uidArray
		 );

		$data2_string = json_encode($data2);
		
		$curl = curl_init();


		curl_setopt_array($curl, array(
		  CURLOPT_PORT => $pingdirPort,
		  CURLOPT_URL => $pingdir. "/consent/v1/consents",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_SSL_VERIFYHOST => false,
		  CURLOPT_SSL_VERIFYPEER => false,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => $data2_string,

		  CURLOPT_HTTPHEADER => array(
			"Content-Type: application/json",
			"authorization: Basic " . $consentCred,
		  ),
		));


		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error: $err";
		  exit();
		}

}

//TEST
// PERFORM POST TO PF AS FAR AS APPROVED SCOPES

$finalScopesString = "";

foreach ($finalScopes as $arrayVal)
			{
				$finalScopesString = $finalScopesString . $arrayVal . " ";		
			}
					
$data2 = array("scopes" => $finalScopesString);
$data2_string = json_encode($data2);


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_PORT => $pingfedPort,
  CURLOPT_URL => $pingfed. "/ext/ref/dropoff",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_SSL_VERIFYHOST => false,
  CURLOPT_SSL_VERIFYPEER => false,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $data2_string,

  CURLOPT_HTTPHEADER => array(
	"accept: application/json",
    "authorization: Basic " . $adapterCred,
    "ping.instanceid: " . $adapterId,
    "cache-control: no-cache"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error: $err";
  exit();
}

$responseData = json_decode($response, false);

$newREF = $responseData->REF;

// REDIRECT USER BACK TO PF RESUME URL
$finalURL = $pingfed. ":" . $pingfedPort . $resumePath . "?REF=" . $newREF;

header("Location: $finalURL"); 
exit();

?>