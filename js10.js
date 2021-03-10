function byid(id  ) { return document.getElementById(id); }
function cree(type) { return document.createElement (type); }

class wsla10 {
    constructor(din, tbodyID) {
	this.tbodyID = tbodyID;
	this.reqs = {};
	this.do10(din);
    }
    
    do10(din) {
	
	const self = this;
	
	din.forEach(function(r) {
	    const tr = cree('tr');
	    
	    tr.dataset.xiref = r['xiref'];
	    tr.dataset.err   = r['err'];
	    tr.dataset.gold10 = r['gold10'];
	    
	    const td50 = cree('i');
	    td50.innerHTML = r['i'];
	    tr.dataset.i = self.lastI = r['i'];
	    
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
	    
	    byid(self.tbodyID).append(tr);

	}
	);
    }
    
    getMore() {
	const li = this.lastI;
	if (this.reqs[li]) return;
	console.log(li);
	this.reqs[li] = 'pending';
    }
}