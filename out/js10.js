function byid(id  ) { return document.getElementById(id); }
function cree(type) { return document.createElement (type); }

class wsla10 {
    constructor() {
	this.do10()
    }
    
    do10() {
	const a = WSAL_INIT;
	
	WSAL_INIT.forEach(function(r) {
	    const tr = cree('tr');
	    const td10 = cree('td');
	    td10.innerHTML = r['ds10'];
	    td10.className = 'col10';
	    tr.append(td10);
	    
	    const td20 = cree('td');
	    td20.innerHTML = r['agentp30'];
	    
	    if (r['bot']) tr.className = 'red';
	    	    
	    tr.append(td20);
	    const td30 = cree('td');
	    td30.innerHTML = r['url'];
	    tr.append(td30);
	    
	    byid('tbody10').append(tr);
	}
	);
    }
}