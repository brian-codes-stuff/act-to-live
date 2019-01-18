<?php
/**
 * Template Name: Home Page
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
<div class="wrapper sub-page-header" id="full-width-page-wrapper">
<div class="home-slider">
	<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
  <ol class="carousel-indicators">
    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
  </ol>
 

  <?php if( have_rows('home_slider') ): ?>

  <div class="carousel-inner">

<?php 
	$x = 0;
while( have_rows('home_slider') ): the_row(); 

	// vars
	$image = get_sub_field('slider_image');
	$title = get_sub_field('slide_title');
    $content = get_sub_field('slide_content');
    $url = get_sub_field('slide_url');

	?>

<div class="carousel-item <?php if ($z==0) { echo 'active';} ?>" style="background-image: url('<?php echo $image; ?>');">
<a href="<?php echo $url ?>">
  		<div class="carousel-caption d-none d-md-block">
    		<h1><?php echo $title ?></h1>
    		<p><?php echo $content ?></p>
  		</div>
          </a>
	</div> 
	<?php 
$z++;
endwhile; ?>
	
  
	</div>

<?php endif; ?>


  <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>
    
</div>
<div class="wrapper" id="full-width-page-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" >
	<hr>
	
		<div class="row">

			
			
			<?php 
// the query
$wpb_all_query = new WP_Query(array('post_type'=>'post', 'order'   => 'ASC', 'cat'=>'3', 'post_status'=>'publish', 'posts_per_page'=>3)); ?>
 
<?php if ( $wpb_all_query->have_posts() ) : ?>
 
<div class="col-md-12 text-center">
<h2>Recent Episodes</h2>
<hr>
</div>
 
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
			</div>
		</div><!-- .row end -->

	</div><!-- Container end -->

</div><!-- Wrapper end -->

<?php get_footer(); ?>
