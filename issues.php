<?php require_once('webassist/framework/framework.php'); ?>
<?php require_once('webassist/framework/library.php'); ?>
<?php
if("" === ""){
	$WA_issues_1699875145132 = new WA_Include("plugins/issues.php");
	require($WA_issues_1699875145132->BaseName);
	$WA_issues_1699875145132->Initialize(true);
}?><?php
if("" === ""){
	$WA_info_1699882881253 = new WA_Include("plugins/info.php");
	require($WA_info_1699882881253->BaseName);
	$WA_info_1699882881253->Initialize(true);
}
?><!doctype html>
<html><!-- InstanceBegin template="/Templates/forestry-ideas-2023.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Untitled Document</title>
<link href="css/bootstrap-4.4.1.css" rel="stylesheet" type="text/css">
</head>

<body>
<div class="container" id="header">
  <div class="jumbotron" id="jumbotron">
    <h1 class="display-4">Forestry deas</h1>
    <p class="lead">This is a simple hero unit, a simple jumbotron-style component for calling extra attention to featured content or information.</p>
    <hr class="my-4">
    <p>It uses utility classes for typography and spacing to space content out within the larger container.</p>
    <p class="lead"> <a class="btn btn-primary btn-lg" href="#" role="button">Learn more</a> </p>
  </div>
</div>
<div class="container" id="navigator">
  <nav class="navbar navbar-expand-lg navbar-light bg-light"> <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent1" aria-controls="navbarSupportedContent1" aria-expanded="false" aria-label="Toggle navigation"> <span class="navbar-toggler-icon"></span> </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent1">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active"> <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a> </li>
        <li class="nav-item"> <a class="nav-link" href="#">Link</a> </li>
        <li class="nav-item dropdown"> <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown1" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Dropdown </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown1"> <a class="dropdown-item" href="#">Action</a> <a class="dropdown-item" href="#">Another action</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#">Something else here</a> </div>
        </li>
        <li class="nav-item"> <a class="nav-link disabled" href="#">Disabled</a> </li>
      </ul>
      <form class="form-inline my-2 my-lg-0">
        <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
      </form>
    </div>
  </nav>
</div>
<br>
<div class="container" id="mainContent">
  <div class="row" id="mainContentRow">
    <div class="col-xl-8" id="leftContentCol"><!-- InstanceBeginEditable name="leftContentCol" -->
		<?php echo((isset($WA_issues_1699875145132))?$WA_issues_1699875145132->Body:"") ?>		
	<!-- InstanceEndEditable --></div>
    <div class="col-xl-4" id="rightContentCol"><!-- InstanceBeginEditable name="rightContentCol" -->
		<?php echo((isset($WA_info_1699882881253))?$WA_info_1699882881253->Body:"") ?>
		<!-- InstanceEndEditable --></div>
</div>
</div>
<br>
<div class="container" id="footer">
  <div class="row">
    <div class="col-xl-4">Content goes here</div>
    <div class="col-xl-4">Content goes here</div>
    <div class="col-xl-4">Content goes here</div>
  </div>
</div>
		
<script src="js/jquery-3.4.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap-4.4.1.js"></script>
</body>
<!-- InstanceEnd --></html>