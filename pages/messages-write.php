<?php 
/*
Template Name: Write
*/
if (isset($_POST['write-send'])) {
	$to = $_POST['write-to'];
	$from = $_POST['write-from'];
	$subject = $_POST['write-subject'];
	$content = $_POST['write-content'];
	
	$ID = wp_insert_post(array(
		'post_type' => 'message',
		'post_title' => $subject,
		'post_content' => $content,
		'post_status' => 'publish'
		)
	);
	add_post_meta($ID, 'to', $to);
	add_post_meta($ID, 'from', $from);
	add_post_meta($ID, 'read', 'unread');
}
get_header(); the_post();

// Only logged in users can view
if (is_user_logged_in()) { 
	global $current_user;
	get_currentuserinfo();
	?>

<div class="container">
	<div class="module">
		<h2 class="header active">Write a Message</h2>
		<div class="content visible clearfix">
			<?php 
			// Messages menu
			wp_nav_menu(array('theme_location' => 'messages')); ?>

			<form id="write-form" name="write-form" method="post" action="<?php the_permalink(); ?>">
				<div class="write-to">
				To: <select id="write-to" name="write-to">
					<?php 
					$users = get_users();
					foreach ($users as $user) { 
						// Only show users that are not the current user
						if ($current_user->ID != $user->ID) {
							echo '<option value="'.$user->ID.'">'.$user->display_name.'</option>';
						}
					} ?>
				</select>
				<input type="hidden" id="write-from" name="write-from" value="<?php echo $current_user->ID; ?>" />
				</div>

				<label for="write-subject">Subject:</label>
				<input type="text" id="write-subject" name="write-subject" />
				<?php wp_editor( '', 'write', $settings = array(
						'media_buttons' => false,
						'textarea_rows' => 5,
						'tinymce' => array(
					        'theme_advanced_buttons1' => 'formatselect,|,bold,italic,underline,|,' .
				            'bullist'
					    )
					) 
				); ?>

				<input type="hidden" id="write-content" name="write-content" />
				<input class="button" type="submit" id="write-send" name="write-send" value="Send" />
			</form>
		</div><!-- .content -->
	</div><!-- .module -->
</div><!-- .container -->

<script>
	jQuery(document).ready(function($){

		var form = $('#write-form'),
			contentInput = $('#write-content'),
			button = $('button');

		form.submit(function(){
			contentInput.val($('iframe').contents().find('#tinymce').html());
			form.find('input[type="submit"]').hide();
		});

	});
</script>

<?php } else { ?>

<div class="container">
	<div class="header"></div>
</div>

<?php }
get_footer(); ?>