</div><!-- #main -->

<div id="toolbar" class="clearfix">

	<div id="breadcrumbs">
		<script>
		if (location.href.indexOf('city/#/') > -1) {
			document.write('<a href="/">World Map</a>');
		}
		</script>
	</div>

	<div id="logged-module"></div>

	<div id="city-module"></div>
	
	<div class="nav"></div>

	<div id="version">
		<script>document.write(CS.VERSION);</script>
	</div>

</div>

<div id="infobox"></div>

<script src="<?php echo bloginfo('template_url'); ?>/js/script.js"></script>

<script>
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', 'UA-9215814-12']);
	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();

	if (!localStorage.getItem('ADMIN')) {
		_gaq.push(['_trackPageview']);		
	}
</script>

</body>
</html>