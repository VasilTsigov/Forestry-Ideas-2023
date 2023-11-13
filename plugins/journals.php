<?php require_once('../Connections/localhost.php'); ?>
<?php require_once('../webassist/mysqli/rsobj.php'); ?>
<?php
$rsJournal = new WA_MySQLi_RS("rsJournal",$localhost,0);
$rsJournal->setQuery("SELECT journal.* FROM journal ORDER BY journal.journalID DESC");
$rsJournal->execute();
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

<div class="container">
  <?php
$wa_startindex = 0;
while(!$rsJournal->atEnd()) {
  $wa_startindex = $rsJournal->Index;
?>
    <div class="row">
      <div class="col-xl-7">Journal <?php echo($rsJournal->getColumnVal("journalTitle")); ?>, <?php echo($rsJournal->getColumnVal("journalYear")); ?>, Vol.<?php echo($rsJournal->getColumnVal("journalVolume")); ?>, No.  <?php echo($rsJournal->getColumnVal("journalNr")); ?></div>
      <div class="col-xl-2"><a href="../issues.php?jid=<?php echo($rsJournal->getColumnVal("journalID")); ?>">Content</a></div>
      <div class="col-xl-3"><a href="#">download</a></div>
    </div>
    <?php
  $rsJournal->moveNext();
}
$rsJournal->moveFirst(); //return RS to first record
unset($wa_startindex);
unset($wa_repeatcount);
?>
</div>
</body>
</html>