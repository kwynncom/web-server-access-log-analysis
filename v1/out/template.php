<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Web Server Access Log Analysis</title>
<script src='out/js10.js'></script>
<script>
    var WSAL_INIT = false;
    window.onload = function() { new wsla10(); }
</script>
</head>
<body><div id='datC10'></div>

<script>WSAL_INIT = <?php echo json_encode($WSALA); ?>;
</script>
</body>
</html>

