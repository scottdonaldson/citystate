<?php 

/* --------------- *\
   LIBRARY FUNDING
\* --------------- */

/* In this order of business:

   0. See if the user has given any funding.
   	  Adjust values in the city accordingly.

   1. Pick a random city from the current user's cities
   2. See if there is a library.
   3. If so, ask for extra funding for books (will increase education).
   4. If not, see if we're passed the desired population.
   5. If so, decrease some happiness but give some funding.
   6. If not, we don't really do anything.
*/

if (isset($_POST['submit'])) { 
	$funding = $_POST['funding'];
	$link = $_POST['link'];
	$city = $_POST['city'];
	$ID = $_POST['id'];
	?>

<div class="container">
	<div class="module">
		<h2 class="header active">Library</h2>
		<div class="content visible clearfix">
			<img src="<?php echo bloginfo('template_url'); ?>/images/library.png" class="alignleft" alt="Library" />
				<?php 
				// If you were a jerk and gave nothing...
				if ($funding == 0) { ?>
				<p>That's not funny. You tell the citizens of <a href="<?php echo $link; ?>"><?php echo $city; ?></a> you'll give them extra funding, and then write them a check for nothing? Unbelievable.</p>
				<p>The story breaks later that day, and people are <strong>not happy</strong> about it. The overall mood of the city takes a hit.</p>
				<?php $happy = get_post_meta($ID, 'happiness', true);
					  update_post_meta($ID, 'happiness', floor(.85*$happiness));
				
				} elseif ($funding < 50) { ?>
				<p>You gave <?php echo $funding; ?> in funding to the library in <a href="<?php echo $link; ?>"><?php echo $city; ?></a>. Every little bit counts, and this is enough to purchase <?php echo rand(5, 3*$funding); ?> books for the library. That's gotta help somewhat!</p>
				<?php $edu = get_post_meta($ID, 'education', true);
					  if ($edu < 100) {
						  update_post_meta($ID, 'education', $edu + 1);
					  }

				} elseif ($funding < 100) { ?>
				<p>You gave <?php echo $funding; ?> in funding. That's great! It's enough to purchase <?php echo rand(100, 3*$funding); ?> books for the library in <a href="<?php echo $link; ?>"><?php echo $city; ?></a>. Those shelves are filling right up!</p>
				<?php $edu = get_post_meta($ID, 'education', true);
					  if ($edu < 99) {
						  update_post_meta($ID, 'education', $edu + 2);
					  } elseif ($edu < 100) {
					  	  update_post_meta($ID, 'education', $edu + 1);
					  }

				} elseif ($funding < 1000) { ?>
				<p>You gave <?php echo $funding; ?> in funding! That's fantastic! It's enough to purchase <?php echo th(rand(400, 2*$funding)); ?> books for the library in <a href="<?php echo $link; ?>"><?php echo $city; ?></a>, as well as help out with some much-needed renovations to the facilities. The citizens will learn in style and comfort.</p>
				<?php $edu = get_post_meta($ID, 'education', true);
					  $happy = get_post_meta($ID, 'happiness', true);
					  if ($edu < 98) {
						  update_post_meta($ID, 'education', $edu + 3);
					  } elseif ($edu < 99) {
						  update_post_meta($ID, 'education', $edu + 2);
					  } elseif ($edu < 100) {
					  	  update_post_meta($ID, 'education', $edu + 1);
					  }
					  if ($happy < 100) {
					  	  update_post_meta($ID, 'happiness', $happy + 1);
					  }
				} else { ?> 
				<p>You gave <?php echo th($funding); ?> in funding! That's fantastic! It's enough to purchase <?php echo th(rand(2000, 3*$funding)); ?> books for the library in <a href="<?php echo $link; ?>"><?php echo $city; ?></a>. The citizens have also used the funding to build a new wing in the library, and they're naming it after you! The people of <?php echo $city; ?> are looking smarter and happier, and it seems that more are moving to the city to take advantage of these outstanding amenities.</p>
				<?php $edu = get_post_meta($ID, 'education', true);
					  $happy = get_post_meta($ID, 'happiness', true);
					  $target_pop = get_post_meta($ID, 'target-pop', true);
					  if ($edu < 98) {
						  update_post_meta($ID, 'education', $edu + 3);
					  } elseif ($edu < 99) {
						  update_post_meta($ID, 'education', $edu + 2);
					  } elseif ($edu < 100) {
					  	  update_post_meta($ID, 'education', $edu + 1);
					  }
					  if ($happy < 99) {
					  	  update_post_meta($ID, 'happiness', $happy + 2);
					  } elseif ($happy < 100) {
					  	  update_post_meta($ID, 'happiness', $happy + 1);
					  }
					  update_post_meta($ID, 'target-pop', $target_pop + 100);
				} 
				// Update cash
				$cash = get_field('cash', 'user_'.$current_user->ID);
				update_field('cash', $cash - $funding, 'user_'.$current_user->ID);
				?>
				
			<a class="again" href="<?php the_permalink(); ?>">Next order of business</a>		
		</div>
	</div>
</div>

<?php } else {

// Pick a random city
$user_args = array(
	'author' => $current_user->ID,
	'posts_per_page' => 1,
	'orderby' => 'rand',
	);
$user_query = new WP_query($user_args);
while ($user_query->have_posts()) : $user_query->the_post();

	$ID = get_the_ID();
	$city = get_the_title();
	$link = get_permalink();
	$pop = get_post_meta($ID, 'population', true);
	include ( MAIN . 'structures.php');
	include( MAIN .'structures/values.php');

	if (get_post_meta($ID, 'library-y', true) != 0) {
		$library = true;
		break;
	} else {
		$library = false;
	}
endwhile;
wp_reset_postdata();

?>
<div class="container">
	<div class="module">
		<h2 class="header active">Library</h2>
		<div class="content visible clearfix">
			<img src="<?php echo bloginfo('template_url'); ?>/images/library.png" class="alignleft" alt="Library" />
				<?php if ($library == true) { ?>
					<p>The library in <a href="<?php echo $link; ?>"><?php echo $city; ?></a> has lots of books, but it could always use more. The citizens are asking for extra government funding to increase the library's circulation and spread knowledge. Will you grant the library more funding?</p>
					<form method="post" action="<?php the_permalink(); ?>">
						<input type="radio" name="fund" value="yes" id="yes" /><label for="yes">Yes</label>
						<input type="radio" name="fund" value="no" id="no" /><label for="no">No</label>
						<br />

						<input type="number" name="funding" id="funding" min="0" />

						<input type="hidden" name="id" id="id" value="<?php echo $ID; ?>" />
						<input type="hidden" name="link" id="link" value="<?php echo $link; ?>" />
						<input type="hidden" name="city" id="city" value="<?php echo $city; ?>" />
						<input type="hidden" name="adv" id="adv" value="3" />

						<input name="submit" id="submit" type="submit" value="Fund that library!" />
					</form>

					<!-- run some javascript to help with the form -->
					<script>
						jQuery(window).ready(function($) {
							var again = $('.again'),
								funding = $('#funding, #submit');
							
							again.hide();
							funding.hide();

							$('#yes').on('click', function(){
								again.hide();
								funding.show();
							})
							$('#no').on('click', function(){
								again.show();
								funding.hide();
							})
						});
					</script>
				<?php } else { ?>
					<p>There is no library in <a href="<?php echo $link; ?>"><?php echo $city; ?></a>, and that's sad.</p>
					
					<?php 
					$pop = get_post_meta($ID, 'population', true);
					// If under the desired population, it's ok, the people aren't clamoring for it yet
					if ($pop < $structures['library'][6]) { ?>
						<p>But, as the community is still so small, it's not yet a huge priority. The citizens have book clubs and lend each other paperback copies of their favorites. They understand that their town isn't big enough to have a library yet.</p>
					<?php 
					// Otherwise, give a small amount of funding
					// and reduce happiness slightly 
					} else { 
						$funding = rand(5, 30); ?>

						<p>The citizens are arguing that any respectable town of this size ought to have a library. They've managed to put together <?php echo $funding; ?> in funding to help with its construction, but they certainly aren't happy about digging into their own pockets. You'd better build that library soon...</p>
						
						<?php 
						// Update cash and happiness
						$cash = get_field('cash', 'user_'.$current_user->ID);
						$happy = get_post_meta($ID, 'happiness', true);
						update_field('cash', $cash + $funding, 'user_'.$current_user->ID);
						if ($happy > 0) {
							update_post_meta($ID, 'happiness', $happy - 1);
						}
					} 
				} ?>
				
			<a class="again" href="<?php the_permalink(); ?>">Next order of business</a>		
		</div>
	</div>
</div>

<?php 
}
?>