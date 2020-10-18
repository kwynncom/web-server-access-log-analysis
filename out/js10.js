class wsla10 {
    constructor() {
	this.do10()
    }
    
    do10() {
	const a = WSAL_INIT;
	
	WSAL_INIT.forEach(function(r) {
	    console.log(r.ip);
	}
	);
    }
}