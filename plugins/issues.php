<?php require_once('../Connections/localhost.php'); ?>
<?php require_once('../webassist/mysqli/rsobj.php'); ?>
<?php
$rsIssues = new WA_MySQLi_RS("rsIssues",$localhost,0);
$rsIssues->setQuery("SELECT issue.*, journal.journalVolume, journal.journalNr, journal.journalYear, journal.journalTitle FROM issue INNER JOIN journal ON issue.issueJournalID = journal.journalID WHERE issue.issueJournalID = ? ORDER BY issue.issueID DESC");
$rsIssues->bindParam("i", "".(isset($_GET['jid'])?$_GET['jid']:"")  ."", "-1"); //WAQB_Param1
$rsIssues->execute();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Untitled Document</title>
<link href="../css/bootstrap-4.4.1.css" rel="stylesheet" type="text/css">
</head>

<body>

	
<?php
$wa_startindex = 0;
while(!$rsIssues->atEnd()) {
  $wa_startindex = $rsIssues->Index;
?>
	
	
  <div class="card col-md-4 col-xl-12">
    <div class="card-body">
	  <p class="card-text"><?php echo($rsIssues->getColumnVal("journalTitle")); ?>, <?php echo($rsIssues->getColumnVal("journalYear")); ?>, <?php echo($rsIssues->getColumnVal("journalVolume")); ?>, <?php echo($rsIssues->getColumnVal("journalNr")); ?></p>
      <h5 class="card-title"><?php echo strip_tags($rsIssues->getColumnVal("issueTitle",false)); ?></h5>
		<p class="card-text">Author(s):</p>
      <p class="card-text"><?php echo strip_tags($rsIssues->getColumnVal("issueAutor",false)); ?></p>
		<p class="card-text">From:</p>
      <p class="card-text"><?php echo strip_tags($rsIssues->getColumnVal("issueFrom",false)); ?></p>
	  <p class="card-text">Abstract:</p>
      <p class="card-text"><?php echo strip_tags($rsIssues->getColumnVal("issueSummary",false)); ?></p>
      <p class="card-text">[<?php echo strip_tags($rsIssues->getColumnVal("issueFile",false)); ?></p>
      <p class="card-text">Downloads: <?php echo strip_tags($rsIssues->getColumnVal("issueCount",false)); ?></p>
      <a href="#" class="btn btn-outline-success">Download issue</a> 
    </div>
  </div><br>
  <?php
  $rsIssues->moveNext();
}
$rsIssues->moveFirst(); //return RS to first record
unset($wa_startindex);
unset($wa_repeatcount);
?>
</body>
	
</html>
