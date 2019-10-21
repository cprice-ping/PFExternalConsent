<?php

// GET AGENTLESS FROM POST
$refId = $_POST['REF'];
$resumePath = $_POST['resumePath'];

// SET SOME IMPORTANT VALUES
$title = $_ENV["PAGE_TITLE"];
$pingfed = "https://pingfederate";
$pingfedPort = $_ENV["PF_BASE_PORT"];

// External Consent values
$adapterId = $_ENV['CONSENT_APP'];
$adapterPwd = $_ENV['CONSENT_APP_PWD'];
$adapterCred = base64_encode($adapterId . ":" . $adapterPwd);

// Show all Variables defined
$allVars = get_defined_vars();
print_r($allVars);

 $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_PORT => $pingfedPort,
  CURLOPT_URL => $pingfed . "/ext/ref/pickup?REF=" . $refId,
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
    "authorization: Basic " . $adapterCred,
    "ping.instanceid: " . $adapterId,
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


// parse response

$responseData = json_decode($response, false);
$escapeUserKey = "chainedattr.USER_KEY";
$escapeAppName = "com.pingidentity.adapter.input.parameter.application.name";
$escapeScopes = "com.pingidentity.adapter.input.parameter.oauth.scope";
$escapeAudience = "com.pingidentity.adapter.input.parameter.oauth.client.id";


$userKey = $responseData->$escapeUserKey;
$appName =  $responseData->$escapeAppName;
$requestedScopes = $responseData->$escapeScopes;
$audienceId = $responseData->$escapeAudience;

$requestedScopesArray = explode(" ",$requestedScopes);


?>


<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title><?php echo $title; ?></title>

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
            <h2><?php  echo $appName  ?></h2>
            <h3>You are logged in as <?php  echo $userKey  ?></h3>
           </div>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <div class="container">
    <div class="row">
      <div class="col-lg-8 col-md-10 mx-auto">
        <div class="post-preview">
            <h2 class="post-title">
              <?php  echo $appName  ?> is asking for the following data:
            </h2>
            
           
					 		<p class="post-meta"> Note that the Profile scope is required. </p>
            
            <form action="updateConsent.php" method="post">
				<input type="hidden" name="resumePath" value="<?php echo $resumePath;?>">
				<input type="hidden" name="userId" value="<?php echo $userKey;?>">
				<input type="hidden" name="audienceId" value="<?php echo $audienceId;?>">
				<input type="hidden" name="appName" value="<?php echo $appName;?>">
				
				<?php
				
					foreach ($requestedScopesArray as $arrayVal){
						echo "<input type=\"checkbox\" id=\"finalScopes\" name=\"finalScopes[]\" value=\" " . $arrayVal . " \"> ";	
						echo $arrayVal;
						echo "<br/>";		
					}
					
					echo "<p><input type=\"submit\" value=\"WOOOO\" class=\"btn btn-primary float-right\"\/>Yes, I consent</p>";
				
					
				?>
				
		   </form>
            
            
           
       
        
        </div>
        
        
        
        
      </div>
    </div>
  </div>

  <hr>

  <!-- Footer -->
  <footer>
    <div class="container">
      <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
          
          <p class="copyright text-muted">Copyright &copy; Remy & Chris 2019</p>
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
