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
	    
	    const td50 = cree('i');
	    td50.innerHTML = r['i'];
	    tr.dataset.i = r['i'];
	    
	    const td10 = cree('td');
	    td10.innerHTML = r['date'];
	    td10.className = 'col10';
	    
	    const td20 = cree('td');
	    td20.innerHTML = r['agent'];
	    td20.className = 'agent';
	    
	    tr.dataset.bot = r['bot'];
	    tr.dataset.iref = r['iref'];
	    	    
	    const td30 = cree('td');
	    td30.innerHTML = r['url'];
	    td30.className = 'url';
	    
	    const td40 = cree('td');
	    td40.innerHTML = r['ip'];

	    tr.append(td50);
	    tr.append(td10);
	    tr.append(td30);
	    tr.append(td20);
	    tr.append(td40);
	    
	    byid('tbody10').append(tr);
	}
	);
    }
}