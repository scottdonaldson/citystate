<?php 

/* ----------------- *\
   POPULATION GROWTH
\* ----------------- */

/* In this order of business, we:

   1. Pick a random city from the current user's cities
   2. Get current population, target population, and happiness.
   3. If population is less than 2000, it's just a fledgling town. Give some cash to help on its way.
   4. If population is 2000 or greater, 
*/

// Get user info
global $current_user;
get_currentuserinfo();

// Get a random city
$pop_query = new WP_query(array(
    'author' => $current_user->ID,
    'posts_per_page' => 1,
    'orderby' => 'rand',
    )
);
while ($pop_query->have_posts()) : $pop_query->the_post();

    $ID = get_the_ID();
    $city = get_the_title();
    $link = get_permalink();
    $pop = get_post_meta($ID, 'population', true);
    $target = get_post_meta($ID, 'target-pop', true);
    $happiness = get_post_meta($ID, 'happiness', true);
   
endwhile;
wp_reset_postdata();

?>

<div class="container docket-<?php echo $adv; ?>">
    <div class="module">
        <?php 
        // If we're under 2000...
        if ($pop < 2000) { 
            // Find a city with a university
            $uni_query = new WP_query(array(
                'posts_per_page' => 1,
                'orderby' => 'rand',
                'meta_query' => array(
                        array(
                            'key' => 'university-y',
                            'value' => 0,
                            'compare' => '!=',
                        )
                    )
                )
            );
            if ($uni_query->have_posts()) {
                while ($uni_query->have_posts()) : $uni_query->the_post();
                    $uni_city = get_the_title();
                    $uni_link = get_permalink();
                    $no_uni = false;
                endwhile;
            } else {
                $no_uni = true;
            }
            wp_reset_postdata();
            ?>
        <h2 class="header">Subject of Scrutiny</h2>
        <div class="content clearfix">
            <img src="<?php echo bloginfo('template_url'); ?>/images/university.png" class="alignleft" alt="University" />
            
            <p>A group of researchers from 
                <?php if (!$no_uni) { ?>
                    the university in <a class="snapshot" href="<?php echo $uni_link; ?>"><?php echo $uni_city; ?></a>
                <?php } else { ?>
                    a university in a distant city
                <?php } ?> has been visiting your city of <a class="snapshot" href="<?php echo $link; ?>"><?php echo $city; ?></a>. They're wrapping up their fieldwork and hope to publish in the near future. For allowing them to perform their work, and for the accommodations, they've paid you <strong><?php $income = rand(10, 25); echo $income; ?></strong>.</p>

            <p>At your insistence, they promise that large, full-color photographs of <?php echo $city; ?> will accompany their written material.</p>

            <?php update_field('cash', get_field('cash', 'user_'.$current_user->ID) + $income, 'user_'.$current_user->ID); ?>
        </div>
        <?php 
        // 2000 people or greater
        } else { ?>
            <h2 class="header">Population</h2>
            <div class="content clearfix">
                <?php
                // Under 60% of target population
                if ($pop/$target < 0.6) {
                    // Happiness under 40%
                    if ($happy <= 40) { ?>
                        <p>Analysts have determined that the infrastructure and economy of <a class="snapshot" href="<?php echo $link; ?>"><?php echo $city; ?></a> allows for ample population growth, but predictions show it to be growing at a very slow rate.</p>
                        <p>This is likely caused by a mostly unhappy populace. Your advisors suggest building more parks or <a href="<?php echo home_url(); ?>/budget">increasing funding to city structures</a> to begin to alleviate this problem.</p>
                    <?php
                    // Happiness between 40 and 60
                    } elseif ($happy > 40 && $happy <= 60) { ?>
                        <p>Analysts have determined that the infrastructure and economy of <a class="snapshot" href="<?php echo $link; ?>"><?php echo $city; ?></a> allows for ample population growth. Most predictions indicate steady growth, eventually leveling out.</p>
                        <p>Your public relations team releases the results to a number of media outlets, and as a result of the positive outlook, happiness in the city appears to be increasing.</p>
                        <?php update_post_meta($ID, 'happiness', get_post_meta($ID, 'happiness', true) + 1); ?>
                    <?php
                    // Happiness above 60    
                    } elseif ($happy > 60) { ?>
                        <p>Analysts have determined that the infrastructure and economy of <a class="snapshot" href="<?php echo $link; ?>"><?php echo $city; ?></a> allows for ample population growth. Based on the city's overall desirability, most predictions indicate rapid growth, eventually leveling out.</p>
                        <p>The vast majority of citizens in <?php echo $city; ?> seem very happy about this news. But a few hipsters decide that they aren't looking forward to this influx, and decide to move elsewhere. City population declines by <strong><?php $decline = rand(50, 250); echo $decline; ?>.</strong></p>
                        <?php update_post_meta($ID, 'population', get_post_meta($ID, 'population', true) - $decline); 
                    }

                // Population between 60% and 95% of target
                } elseif ($pop/$target >= 0.6 && $pop/$target < 0.95) { ?>
                    <p>Analysts have determined that the infrastructure and economy of <a class="snapshot" href="<?php echo $link; ?>"><?php echo $city; ?></a> allows for ample population growth. Based on the city's overall desirability, most predictions indicate rapid growth, eventually leveling out.</p>
                <?php 
                // Population between 95% of target and 5% over target
                } elseif ($pop/$target >= 0.95 && $pop/$target < 1.05) { ?>
                    <p>Analysts have determined that the infrastructure and economy of <a class="snapshot" href="<?php echo $link; ?>"><?php echo $city; ?></a> is very near its equilibrium.</p>
                    <p>To prevent economic stagnation, your advisors suggest improving existing infrastructure or pursuing trade options.</p>
                <?php
                // Population greater than 105% of target 
                } elseif ($pop/$target >= 1.05) { ?>
                    <p>Analysts have determined the current population of <?php echo $city; ?> is unsustainable! Unless infrastructure is upgraded or the economy improves, the population will shrink until it reaches a level of equilibrium.</p>
                    <?php if ($happy > 60) { ?>
                        <p>Owing to the general contentment in <?php echo $city; ?>, population will decline slower than it otherwise would. People just want to stay here, even if it's crowded.</p>
                    <?php } ?>
                <?php } ?>
            </div>
        <?php } 
        include ( MAIN .'docket/next.php'); ?>
    </div>
</div>