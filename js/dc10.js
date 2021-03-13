// display control

function displayControl() {
	document.querySelectorAll('[data-bot="true"], [data-err="true"]').forEach(function (e) { 
	    e.style.display = 'none';    
    });
}