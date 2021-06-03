<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>kwynn.com user agent counts from web server access log</title>
<script src='utils.js'></script>
<script src='js10.js' ></script>
<?php require_once('datToWeb.php'); ?>
<script>
    var KWYNN_UA_INIT = <?php 
	echo(agent_to_web::getJSON()); 
    ?>;
    window.onload = function() { new kwua10(); }
</script>
<style>
    body { font-family: monospace; }
    .tar { text-align: right; }
    .tal { text-align: left ;}
    th.ua { text-align: left; padding-left: 5ex; }
    .c10 { padding-right: 2ex; }
    #table05 { font-size: 130% }
    #table07 { font-size: 120%; margin-bottom: 0ex; }
    #from, #to { padding: 0.9ex; }
    #to { padding-left: 2ex }
    #lines, #days, #lpd, #linesBot, #botp { text-align: right; padding-left: 2ex }
    p.rjs { font-size: 120%; display: inline-block; margin-top: 0; margin-bottom: 0; position: relative; top: -3ex; left: 3ex; }
    div.t07 { display: inline-block; margin-bottom: 0; padding-bottom: 0; }
    div.d07parent { margin-bottom: 0.3ex; }
</style>
</head>
<body>

    <table id='table05'>
	<tr><th>from</th><th>to</th></tr>
	<tr><td id='from'></td><td id='to'></td></tr>
    </table>
    <div class='d07parent'>
    <div class='t07'>
    <table id='table07'>
	<tr><td>lines</td><td id='lines'></td></tr>
	<tr><td>bots</td> <td id='linesBot'> </td></tr>
	<tr><td>bots</td> <td id='botp'> </td></tr>
	<tr><td>days</td> <td id='days'> </td></tr>
	<tr><td>lpd</td><td id='lpd'></td></tr>
    </table>
    </div>
    <p class='rjs'><a href='getJSON.php'>get raw JSON</a></p>    
    </div>
    
    <table>
	<thead><tr><th class='tal'>count</th><th class='ua'>user agent</th></tr></thead>
	<tbody id='tbody10'></tbody>
    </table>
</body>
</html>
