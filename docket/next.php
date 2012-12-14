<?php if ($turns > 1) { ?>
<a class="again button" href="<?php the_permalink(); ?>">Next order of business</a>
<?php } else { ?>
<strong>No orders of business left today!</strong>
<?php } ?>