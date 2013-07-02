<?php 
get_header(); 
the_post(); 

// Get user info
global $current_user;
get_currentuserinfo();

// Get city ID
$ID = get_the_ID();

// This is an admin-only page
if (current_user_can('switch_themes')) {

	if (isset($_POST['buildregion'])) {
		build_region($ID);
	}
?>

<style>

	label {
		display: block;
	}

	.fields {
		background-color: #fff;
		display: none;
		padding: 20px;
		position: absolute;
		top: 30px;
		left: 30px;
		width: 360px;
		z-index: 9999;
	}

	.fields input[type="radio"],
	.fields input[type="checkbox"] {
		margin-right: 5px;
	}

	.fields input[type="number"] {
		float: right;
		width: 40px;
	}

	.fields > div {
		width: 45%;
	}

	.tile:hover .fields {
		display: block
	}
</style>

<form id="map" class="clearfix" method="POST">

	<?php 
	show_region_map($ID, $resources, $terrain);

	// show the city's geographic neighbors
	show_city_neighbors($geo, $ID);
	?>

	<input class="button" name="buildregion" id="buildregion" type="submit" value="Save Changes">

</form><!-- #map -->

<h2>Data overlays:</h2>
<ul id="overlays">
	<?php
	foreach ($resources as $resource => $values) { ?>
		<li data-overlay="<?= $resource; ?>"><?= ucfirst($resource); ?></li>
	<?php } ?>
	<li data-overlay="remove">Remove overlay</li>
</ul>

<script>
	(function($){
		var value;
		$('.tile input').change(function(){
			$this = $(this);
			if ($this.is(':radio')) {
				value = $this.val();
				// show the tile switching terrain but keep the .tile class
				$this.closest('.tile').removeClass().addClass('tile ' + value);
			} else if ($this.is(':checkbox')) {
				// If checking, need a number input for resource value
				if ($this.is(':checked')) {
					// If there's already one (i.e. had been hidden and reshown),
					// switch from hidden to number
					if ($this.next('input').length > 0) {
						$this.next('input').show();
					// If there isn't one, add input to the DOM and set name
					} else {
						$this.after('<input type="number" name="' + $this.attr('id') + '">');
					}
				// If unchecking, hide the input and set to delete upon submitting
				} else {
					$this.next('input').hide().val('0');
				}
			}
		});

		var body = $('body');
		$('#overlays li').click(function(){
			$this = $(this);
			$this.css('color', '#fff').siblings().css('color', '#000');
			body.attr('data-overlay', $this.attr('data-overlay'));
		});
	})(jQuery);
</script>

<?php }

get_footer(); ?>