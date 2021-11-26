class scrolling {
    constructor(tmo) {
	
	const ratio = 0.75;
	
	window.onscroll = function(ev) {
		if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight * ratio) {
		    tmo.getMore();
		}
	};
    }
}