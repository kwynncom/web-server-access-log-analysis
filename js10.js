function byid(id  ) { return document.getElementById(id); }
function cree(type) { return document.createElement (type); }

class wsla10 {
    constructor(din, tbodyID) {
	this.tbodyID = tbodyID;
	this.reqs = false;
	this.do10(din);
    }
    
    do10(din) {
	const self = this;
	if (typeof din === 'array') din.forEach(function(r)  { self.loop10(r); });
	else for (const [key, value] of Object.entries(din)) { self.loop10(value);}
	this.reqs = false;
   }
    
    loop10(r) {
	    const tr = cree('tr');
	    
	    tr.dataset.xiref = r['xiref'];
	    tr.dataset.err   = r['err'];
	    tr.dataset.gold10 = r['gold10'];
	    
	    const td50 = cree('i');
	    td50.innerHTML = r['i'];
	    tr.dataset.i = this.lastI = r['i'];
	    
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
	    
	    const td60 = cree('td');
	    td60.innerHTML = r['ref'];
	    td60.className = 'ref';
	    
	    tr.append(td50);
	    tr.append(td10);
	    tr.append(td30);
	    tr.append(td60);
	    tr.append(td20);
	    tr.append(td40);
	    
	    byid(this.tbodyID).append(tr);

	
    }
    
    
    getMore() {
	const li = this.lastI;
	if (this.reqs) return;
	console.log(li);
	this.reqs = 'pending';
	this.netGet(li);
    }
    
    
    netGet(li) {
	const self = this;
	const xhr = new XMLHttpRequest(); 
	xhr.open('GET', 'doit.php?ll=' + li + '&XDEBUG_SESSION_START=netbeans-xdebug');
	xhr.onreadystatechange = function() { 
	    if (this.readyState === 4 && this.status === 200) {
		self.do10(JSON.parse(xhr.responseText));
	    }
	}
	xhr.send();
    }
}