    var WSAL_INIT = false;
    window.onload = function() { 
	
	const tbid = 'tbody10'; // must match below
	
	const tmo = new wsla10(WSAL_INIT, tbid); 
	new scrolling(tmo);
    }
