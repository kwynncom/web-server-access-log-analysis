<!DOCTYPE html>
<html lang='en'>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
<meta name='viewport' content='width=device-width, initial-scale=1.0' />

<title>browser user agent counts</title>

<style>
	body  { font-family: sans-serif; }
	.exdp { font-size: 120%; }
</style>


</head>
<body>

<p class='exdp'><a href='discussion.html'>explanation / discussion</a></p>
	
<div>
	<a href='?json=1'>get raw JSON</a>
</div>
	
<table id='table05'>
	<tr><th>from</th><th>to</th></tr>
	<tr>
		<td id='from'><?php echo($dateSHu); ?></td>
		<td id='to'  ><?php echo($dateEHu); ?></td>
	</tr>
</table>

<table>
	<tr><td>lines</td><td><?php echo(   $numLinesS); ?></td></tr>
	<tr><td>bots </td><td><?php echo($botNumLinesS); ?></td></tr>
	<tr><td>bots </td><td><?php echo($botPer);      ?></td></tr>
	<tr><td>days </td><td><?php echo($days  );      ?></td></tr>		
	<tr><td>lpd </td><td><?php echo($lpd  );      ?></td></tr>
</table>
	
<table>
	<tr><th>rank</th><th>count</th><th>%</th><th>b</th><th>user agent</th></tr>
	
	<?php echo($bigATabHT); ?>
	
</table>
	
</body>
</html>

