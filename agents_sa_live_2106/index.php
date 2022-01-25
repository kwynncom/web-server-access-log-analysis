<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>kwynn.com user agent counts from web server access log</title>
<script src='/opt/kwynn/js/utils.js'></script>
<script>

<?php function getJSON_kwua() { return file_get_contents('/var/large_www/user_agents_2021_06.json'); } ?>

var KWYNN_UA_INIT = <?php echo(getJSON_kwua()); ?>;
	
class kwua10 {
		
    constructor() {
		this.totLinesCk = 0;
		this.p10(KWYNN_UA_INIT.human_read);
		this.p20(KWYNN_UA_INIT.user_agents);  
    }
    
    p10(h) {
		byid('from').innerHTML = h.minDate;
		byid('to'  ).innerHTML = h.maxDate;
		inht('lines', h.lines);
		inht('days', h.days);
		inht('lpd' , h.lpd);
		inht('linesBot', h.linesBot);
		inht('botp', h.botp);
    }
    
    p20(biga) {
		const self = this;
		biga.forEach(function(a, i) {
			const tr   = cree('tr');
			const td10 = cree('td');
			const cnt = a['count'];
			self.totLinesCk += cnt;
			td10.innerHTML = cnt;
			td10.className = 'tar c10';
			const td20 = cree('td');
			td20.innerHTML = a['_id'];
			td20.style.backgroundColor = a['bot'] ? '#ffcfcf' : 'white';
			tr.append(td10);
			tr.append(td20);
			byid('tbody10').append(tr);

			return;
		});
    }
}

window.addEventListener('DOMContentLoaded', (event) => { new kwua10();  });

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
    .ovs { font-size: 120%; font-family: sans-serif; }
</style>

</head>
<body>
    <div class='ovs'>
    <p><a href='/t/21/12/ua/'>2021/12 - next version</a></p>
    <p><a href='/t/20/10/ua/'>2020/10 - previous version</a></p>
	<p><a href='https://github.com/kwynncom/web-server-access-log-analysis/tree/96d8fdc217f307cf2574d73f80123978134727c3/agent_sa'>source code</a>
	(ancestor code, from which this is derived)</p>
    </div>
    <table id="table05">
	<tbody><tr><th>from</th><th>to</th></tr>
	<tr><td id="from">2021/02/25 17:33:31 UTC -05:00</td><td id="to">2021/06/03 19:16:51 UTC -04:00</td></tr>
    </tbody></table>
    <div class="d07parent">
    <div class="t07">
    <table id="table07">
	<tbody><tr><td>lines</td><td id="lines">234,514</td></tr>
	<tr><td>bots</td> <td id="linesBot">170,844</td></tr>
	<tr><td>bots</td> <td id="botp">73%</td></tr>
	<tr><td>days</td> <td id="days">98</td></tr>
	<tr><td>lpd</td><td id="lpd">2392</td></tr>
    </tbody></table>
    </div>
    <p class="rjs"><a href="/var/large_www/user_agents_2021_06.json">get raw JSON</a></p>   
    </div>
    
    <table>
	<thead><tr><th class="tal">count</th><th class="ua">user agent</th></tr></thead>
	<tbody id="tbody10">
        </tbody>
    </table>
	
</body>
</html>
