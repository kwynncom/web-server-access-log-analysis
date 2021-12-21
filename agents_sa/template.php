<!DOCTYPE html>
<html lang='en'>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
<meta name='viewport' content='width=device-width, initial-scale=1.0' />

<title>browser user agent counts</title>
</head>
<body>
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
</body>
</html>

