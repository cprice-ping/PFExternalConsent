<?php
// build curl object to grab related person
// =======================================================================

$curl = curl_init();

// build context

//get real IP address
// ======================================================================
function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

// get http referer
// =======================================================================

$httpReferer = $_SERVER['HTTP_REFERER'];
$ipAddress = getRealIpAddr();
$now = time();


// get stuff from form post
// =======================================================================

$userId = $_POST['userId'];
$acRelatedPersonUID = $_POST['acRelatedPersonUID'];
$existingId = $_POST['existingId'];

$definitionArray = array("id" => "share-profile-data",
                     "version" => "1.0",
                     "locale" => "en-US");

$uidArray = array("relatedPerson" => $acRelatedPersonUID);

$visitArray = array("timestamp" => $now,
		      "urlVisit" => $httpReferer,
              "ipAddr" => $ipAddress); 

$data2 = array("status" => "accepted",
		"subject" => $userId,
		"actor" => $userId,
		"audience" => "acConsentMgmtApp",
        "definition" => $definitionArray,
		"titleText" => "Share profile data",
		"dataText" => "Share profile data",
		"purposeText" => "Share profile data",
        "data" => $uidArray,
		"consentContext" => $visitArray
		 );

$data2_string = json_encode($data2);


curl_setopt_array($curl, array(
  CURLOPT_PORT => "1443",
  CURLOPT_URL => "https://pingdirectory:1443/consent/v1/consents/" . $existingId,
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
    "authorization: Basic Y249ZG1hbmFnZXI6MkRpcmVjdG9yeU0wcmUh"
  ),
));


$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error: $err";
  exit();
}

// parse response to grab consentID
// =======================================================================

$responseData = json_decode($response, false);

$consentId = $responseData->id;





?>


<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>AnyCompany Consent Management</title>

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
            <h1>Manage My Consents</h1>
            <span class="subheading">AnyCompany</span>
            <span class="subheading">Logged in as <?php echo $userId; ?></span
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
              Consent Accepted <br/><?php echo $consentId; ?>
            </h2>
            
            
    
       
        <div class="clearfix">
          <a class="btn btn-primary float-right" href="index.php">Go back &larr;</a>
        </div>
        
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
          
          <p class="copyright text-muted">Copyright &copy; AnyCompany 2019</p>
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
