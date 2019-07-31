<?php

// SET SOME IMPORTANT VALUES
$pingfed = $_ENV["PF_BASE_URL"];
$pingfedPort = $_ENV["PF_BASE_PORT"];
$pingdir = $_ENV["PD_BASE_URL"];
$pingdirPort = $_ENV["PD_BASE_PORT"];

// External Consent values
$adapterId = $_ENV["CONSENT_APP"];
$adapterPwd = $_ENV["CONSENT_APP_PWD"];
$adapterCred = base64_decode($adapterId . ":" . $adapterPwd);

// Set values for ConsentAPI calls
$consentDef=$_ENV["CONSENT_DEF"];
$consentAdmin=$_ENV["CONSENT_ADMIN"];
$consentPwd=$_ENV["CONSENT_PWD"];
$consentCred = base64_decode($consentAdmin . ":" . $consentPwd);

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
		$definitionArray = array("id" => "PF-OAuth",
                     "version" => "1.1",
                     "locale" => "en-US");
                     
                     
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


// PERFORM POST TO PF AS FAR AS APPROVED SCOPES

$data2 = array("scopes" => $finalScopes);



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


<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>OAuth External Consent Demo</title>

  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom fonts for this template -->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href='https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>

  <!-- Custom styles for this template -->
  <link href="css/clean-blog.min.css" rel="stylesheet">

</head>

<body>

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
    <div class="container">
      <a class="navbar-brand" href="index.html"></a>
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        Menu
        <i class="fas fa-bars"></i>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="index.html"></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="about.html"></a>
          </li>
         
        </ul>
      </div>
    </div>
  </nav>

  <!-- Page Header -->
  <header class="masthead" style="background-image: url('img/home-bg.jpg')">
    <div class="overlay"></div>
    <div class="container">
      <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
          <div class="site-heading">
            <h1>External Consent App Approval</h1><br/>
           
           </div>
        </div>
      </div>
    </div>
  </header>

  
  <!-- Footer -->
  <footer>
    <div class="container">
      <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
          
          <p class="copyright text-muted">Copyright &copy; Remy&Chris 2019</p>
        </div>
      </div>
    </div>
  </footer>

  <!-- Bootstrap core JavaScript -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Custom scripts for this template -->
  <script src="js/clean-blog.min.js"></script>

</body>

</html>