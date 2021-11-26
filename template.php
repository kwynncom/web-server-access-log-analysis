<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Web Server Access Log Analysis</title>

<script src='js/js10.js'></script>
<script src='js/js20.js'></script>
<script src='js/init10.js'></script>
<script src='js/dc10.js'></script>

<style>
    .tbody10 { font-family: monospace; }
    .col10   { min-width: 16ex}
    .agent   { max-width: 90ex; }
    .url     { max-width: 50ex; }
    td       { overflow-wrap: anywhere; }
    [data-iref=true][data-xiref=false][data-bot=false] { background-color: rgb(220, 255, 220);}
    [data-err=true] { background-color: red; }
    [data-xiref=true]  { background-color: rgb(255,245,180);}
    [data-bot=true]  { background-color: rgb(255, 204, 204);  } /* order matters */
    [data-gold10=true] { background-color: gold; }
    [data-mine=true] { opacity: 0.3}
    .ref { max-width: 30ex; }
    .butp { position: sticky; top: 0; background-color: white }
</style>

<script>
    function dobtn(bid, isck) {
	document.querySelectorAll('[data-bot="true"], [data-err="true"]').forEach(function (e) { 
	    e.style.display = 'none';
	});
	
	displayControl();
    }
</script>

</head>
<body>
    <div class='butp'>
	<input type='checkbox' checked='checked' onclick='dobtn("bae", this.checked);' /> bots & err
	
    </div>
    <div id='datC10'>
	<table>
	    <thead id='thead10' />
	    <tbody id='tbody10' class='tbody10'/> <!-- id must match above -->
	</table>
    </div>

<script>WSAL_INIT = <?php echo json_encode($WSALA); ?>;
</script>
</body>
</html>

