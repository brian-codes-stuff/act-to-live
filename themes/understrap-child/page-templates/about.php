<?php
/**
 * Template Name: About Page
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

	<div class="<?php echo esc_attr( $container ); ?>" id="content">
	
	<hr>
		<div class="row">

			<div class="col-md-6 content-area" id="primary">

				<main class="site-main" id="main" role="main">

					<img src="<?php the_field('featured_image'); ?>" />
				
				</main><!-- #main -->

			</div><!-- #primary -->
			<div class="col-md-6">
				<?php the_field('main_content'); ?>
			</div>
			<div class="col-md-12">
			<hr>
            </div>
            <div class="col-md-12">
            <h3>Our Team</h3>
            <hr>
            </div>
            <?php if( have_rows('team_members') ): ?>

            <?php 
while( have_rows('team_members') ): the_row(); 

	// vars
	$image = get_sub_field('team_member_photo');
	$name = get_sub_field('team_member_name');
    $position = get_sub_field('team_member_position');
    $bio = get_sub_field('team_member_bio');
    $email = get_sub_field('team_member_email');

    ?>
    
    <div class="col-lg-6 mb-6">
          <div class="card h-100">
            <img class="card-img-top" src="<?php echo $image; ?>" alt="">
            <div class="card-body">
              <h4 class="card-title text-center"><strong><?php echo $name; ?></strong></h4>
              <hr>
              <h6 class="card-subtitle mb-2 text-muted"><?php echo $position; ?></h6>
              <p class="card-text"><?php echo $bio; ?></p>
            </div>
            <div class="card-footer">
              <a href="#"><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a>
            </div>
          </div>
        </div>

   <?php endwhile; ?>

   <?php endif; ?>


			<div class="col-md-4">
				<?php the_field('media_call_out_section'); ?>
			</div>
            
            


			<div class="col-md-12">
			<hr>
			</div>
		</div><!-- .row end -->

	</div><!-- Container end -->

</div><!-- Wrapper end -->

<?php get_footer(); ?>
