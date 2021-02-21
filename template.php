<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Web Server Access Log Analysis</title>

<style>
    .tbody10 { font-family: monospace; }
    .col10   { min-width: 16ex}
    .agent   { max-width: 90ex; }
    .url     { max-width: 60ex; }
    td       { overflow-wrap: anywhere; }
    [data-bot=true]  { background-color: rgb(255, 204, 204);  }
    [data-iref=true] { background-color: rgb(204, 255, 204);}
</style>


<script src='js10.js'></script>
<script>
    var WSAL_INIT = false;
    window.onload = function() { new wsla10(); }
</script>


</head>
<body>
    <div id='datC10'>
	<table>
	    <thead id='thead10' />
	    <tbody id='tbody10' class='tbody10'/>
	</table>
    </div>

<script>WSAL_INIT = <?php echo json_encode($WSALA); ?>;
</script>
</body>
</html>

