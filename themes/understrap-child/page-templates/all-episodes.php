<?php
/**
 * Template Name: All Episodes Page
 *
 * Template for displaying a page without sidebar even if a sidebar widget is published.
 *
 * @package understrap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
$container = get_theme_mod( 'understrap_container_type' );
?>

<div class="wrapper sub-page-header" id="full-width-page-wrapper" style="background: url('<?php the_field('main_image'); ?> ') no-repeat center center fixed;  -webkit-background-size: cover;  -moz-background-size: cover;  -o-background-size: cover;  background-size: cover; height: 150px; display: block; margin-top: -32px; width: 100%;">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="sub-title"><?php the_title(); ?></h1>
            </div>
        </div>
    </div>
</div>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<hr>
			<?php the_field('main_content'); ?>
			<hr>
		</div>
	</div>
</div>
<div class="wrapper" id="archive-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>"  tabindex="-1">

		<div class="row">

			<!-- Do the left sidebar check -->
			

		<div class="col-md-12">
        <h3>All Episodes</h3>
        <hr>
        </div>

            <?php 
// the query
$wpb_all_query = new WP_Query(array('post_type'=>'post', 'order'   => 'ASC', 'cat'=>'3', 'post_status'=>'publish', 'posts_per_page'=>-1)); ?>
 
<?php if ( $wpb_all_query->have_posts() ) : ?>
 

 
    <!-- the loop -->
    <?php while ( $wpb_all_query->have_posts() ) : $wpb_all_query->the_post(); ?>
	<div class="col-md-4">
	<div class="card mb-4">
            <img class="card-img-top" src="<?php echo get_the_post_thumbnail_url(); ?>">
            <div class="card-body">
              <h2 class="card-title"><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h2>
              <p class="card-text"><?php the_excerpt(); ?></p>
             
            </div>
            <div class="card-footer text-muted">
              Posted on <?php echo get_the_date(); ?> by
			  <?php the_author(); ?>
            </div>
          </div>
</div>

    <?php endwhile; ?>
    <!-- end of the loop -->
 

 
    <?php wp_reset_postdata(); ?>
 
<?php else : ?>
    <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
<?php endif; ?>

	

			<!-- The pagination component -->
			<?php understrap_pagination(); ?>

		<!-- Do the right sidebar check -->
	
	</div> <!-- .row -->

</div><!-- Container end -->

</div><!-- Wrapper end -->

<?php get_footer(); ?>
