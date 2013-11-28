	<div id="infobox"></div>

</div><!-- #main -->

<div id="toolbar" class="clearfix">

	<!-- Need:

		-Breadcrumbs
		-User module
		-City module
		-Not logged in module
	-->
	<div id="breadcrumbs"></div>

	<div id="user"></div>
	<div id="log-in-out"></div>
	
	<div class="nav"></div>

	<div id="version">
		<script>document.write(VERSION);</script>
	</div>
	
	

</div>

<script src="<?php echo bloginfo('template_url'); ?>/js/script.js"></script>

<script>
	var _gaq = _gaq || [];
	if (!localStorage.getItem('ADMIN')) {
		_gaq.push(['_setAccount', 'UA-9215814-12']);
		_gaq.push(['_trackPageview']);

		(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	}
</script>

</body>
</html>