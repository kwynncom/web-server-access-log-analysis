<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>kwynn.com user agent counts from web server access log</title>
<script src='utils.js'></script>
<script src='js10.js' ></script>
<?php require_once('fromDisk.php'); ?>
<script>
    var KWYNN_UA_INIT = <?php echo(wsal_ua_standalone_p10::get()); ?>;
    window.onload = function() { new kwua10(); }
</script>
    

</head>
<body>
    <table id='table10'>
    </table>
</body>
</html>
