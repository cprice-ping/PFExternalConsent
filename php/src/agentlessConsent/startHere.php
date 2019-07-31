<?php

// Grab from the Environment
// Inject appropriate values in the docker-compose.yaml file
$title = $_ENV["PAGE_TITLE"];
$appUrl = $_ENV["CONSENT_APP_URL"];

// SET SOME IMPORTANT VALUES
$pingfedBase = $_ENV["PF_BASE_URL"];
$pingfedPort = $_ENV["PF_BASE_PORT"];
$pingfed = $pingfedBase . ":" . $pingfedPort;
$clientId = $_ENV["AUTHZ_CLIENT"];
$redirectUri = $appUrl . $_ENV["REDIRECT_URI"];

// formulate PF Authz URL with Param

$pfURL = $pingfed . "/as/authorization.oauth2?client_id=" . $clientId . "&response_type=code&scope=openid%20profile&redirect_uri=" . $redirectUri;

?>


<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title><?php $title ?></title>

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
  <header class="masthead" style="background-image: url('img/post-bg.jpg')">
    <div class="overlay"></div>
    <div class="container">
      <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
          <div class="site-heading">
            <h1>PF OAuth External Consent Demo</h1>
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
            
             <form action="<?php echo $pfURL; ?>" method="post">
 				
 				
				 <?php
						echo "<p align=\"center\"><input value=\"Get OIDC Token\" type=\"submit\" class=\"btn btn-primary float-center\"\/></p>";
				
				?>
           
					   
			</form>
            
           
       
        
        </div>
        <hr>
        
        
        
      </div>
    </div>
  </div>

  <hr>

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
