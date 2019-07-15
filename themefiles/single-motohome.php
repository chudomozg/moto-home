<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

get_header(); ?>

<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				get_template_part( 'template-parts/post/content', get_post_format() );

				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;

				// the_post_navigation(
				// 	array(
				// 		'prev_text' => '<span class="screen-reader-text">' . __( 'Previous Post', 'twentyseventeen' ) . '</span><span aria-hidden="true" class="nav-subtitle">' . __( 'Previous', 'twentyseventeen' ) . '</span> <span class="nav-title"><span class="nav-title-icon-wrapper">' . twentyseventeen_get_svg( array( 'icon' => 'arrow-left' ) ) . '</span>%title</span>',
				// 		'next_text' => '<span class="screen-reader-text">' . __( 'Next Post', 'twentyseventeen' ) . '</span><span aria-hidden="true" class="nav-subtitle">' . __( 'Next', 'twentyseventeen' ) . '</span> <span class="nav-title">%title<span class="nav-title-icon-wrapper">' . twentyseventeen_get_svg( array( 'icon' => 'arrow-right' ) ) . '</span></span>',
				// 	)
				//);

			endwhile; // End of the loop.
            ?>
            <div class="mth_front_rooms">
                Комнаты:<br/>>
                <?php
                $data_arr = carbon_get_post_meta( $post->ID, 'rooms');
                print_r( $data_arr );
                ?>
            </div>
            <div class="mth_front_gallery"> 
                <h4 class="mth_front_gallery_title mth_title">Фотографии</h4>
                <?php $slides = carbon_get_post_meta(get_the_ID(), 'motohome_gallery');
                    foreach ($slides as $slide):
                ?>
                    <img src="<?php echo wp_get_attachment_image_url($slide); ?>" alt="Image">
                <?php endforeach; 
                ?>
            </div>
           <div class="mth_front_city">
               <div class="mth_front_city_title mth_title">
                   Город:
               </div>
               <div class="mth_front_city_value">
                    <?php
                    $region=get_term(carbon_get_post_meta(get_the_ID(), 'motohome_region'));
                    $city=get_term(carbon_get_post_meta(get_the_ID(), 'motohome_city'));
                    echo ($region->name." г.".$city->name);
                   ?>
               </div>
           </div>
           <div class="mth_front_loc">
               <div class="mth_front_loc_title mth_title">
                    Местоположение
               </div>
                <input type="hidden" class="mth_front_loc_value" value="<?php echo carbon_get_post_meta(get_the_ID(), 'motohome_loc');?>">
           </div>
           <div class="mth_front_map">
               <div id="map" style="width: 100%; height: 400px"></div>
           </div>


		</main><!-- #main -->
	</div><!-- #primary -->
	<?php get_sidebar(); ?>
</div><!-- .wrap -->

<?php
get_footer();
