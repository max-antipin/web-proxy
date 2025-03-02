window.onscroll = (new function(){
	var btn = document.getElementById("go_to_top");
	if(!btn)
	 {
		this.Run = function(){};
		return;
	 }
	var prev_show = false, offset = parseInt(btn.getAttribute('data-offset'));
	if(isNaN(offset) || offset < 1) offset = 200;
	btn.onclick = function()
	 {
		var top = 60, interval, delta = 10, func = function()
		 {
			top -= delta;
			if(top <= 0)
			 {
				clearInterval(interval);
				top = 0;
			 }
			else delta = Math.round(delta * delta * 0.2);
			window.scrollTo(0, top);
		 };
		func();
		interval = setInterval(func, 50);
	 };
	this.Run = function()
	 {
		var top = parseInt(window.pageYOffset);
		if(isNaN(top)) top = parseInt(document.documentElement.scrollTop);
		var show = top > offset;
		if(show != prev_show)
		 {
			if(show) btn.className = btn.className.replace(' _hidden', '');
			else btn.className += ' _hidden';
		 }
		prev_show = show;
	 }
}).Run;
window.onscroll();