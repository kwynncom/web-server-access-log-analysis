class kwua10 {
    constructor() {
	this.totLinesCk = 0;
	this.p10(KWYNN_UA_INIT.human_read);
	this.p20(KWYNN_UA_INIT.user_agents);  
	console.info('tot lines check = ' + this.totLinesCk);
    }
    
    p10(h) {
	byid('from').innerHTML = h.minDate;
	byid('to'  ).innerHTML = h.maxDate;
	inht('lines', h.lines);
	inht('days', h.days);
	inht('lpd' , h.lpd);
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