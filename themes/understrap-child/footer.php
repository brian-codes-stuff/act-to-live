<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package understrap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$container = get_theme_mod( 'understrap_container_type' );
?>

<?php get_template_part( 'sidebar-templates/sidebar', 'footerfull' ); ?>

<div class="wrapper" id="wrapper-footer">

<div class="container">
      <p class="m-0 text-center text-white">Copyright © Act To Live <?php echo date("Y");?></p>
      <div class="text-center center-block">
          <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
        <br>
            <a href="#"><i class="fa fa-facebook-square fa-2x social"></i></a>
          <a href="#"><i class="fa fa-twitter-square fa-2x social"></i></a>
          <a href="#"><i class="fa fa-google-plus-square fa-2x social"></i></a>
          <a href="#" "=""><i class="fa fa-envelope-square fa-2x social"></i></a>
</div>
    </div>

</div><!-- wrapper end -->

</div><!-- #page we need this extra closing tag here -->

<?php wp_footer(); ?>

</body>

</html>
