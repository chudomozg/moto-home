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


            <div class="mainContent mth_main_content">
                <div class="mth_flex">
                    <div class="mth_front_gallery_wrapper">
                        <div class="mth_front_gallery fotorama"  
                            data-nav="thumbs" 
                            data-width="100%"  
                            data-ratio="1/0.9"
                            data-fit="cover">  
                            <?php $slides = carbon_get_post_meta(get_the_ID(), 'motohome_gallery');
                                foreach ($slides as $slide):
                            ?>
                                <a href="<?php echo wp_get_attachment_image_url($slide, "full"); ?>"><img src="<?php echo wp_get_attachment_image_url($slide, "full"); ?>" alt="Image"></a>
                            <?php endforeach; 
                            ?>
                        </div>
                    </div>
                    <div class="mth_front_map">
                        <div id="map" style="width: 100%; height: 100%"></div>
                    </div>
                </div>
                <div class="mth_desc">
                    <?php the_content(); ?>
                </div>
                

                <div class="mth_front_rooms">
                    <?php
                    $data_arr = carbon_get_post_meta( $post->ID, 'rooms');
                    print_r( $data_arr );
                    ?>
                </div>
        
                <div class="mth_front_loc">
                        <input type="hidden" class="mth_front_loc_value" value="<?php echo carbon_get_post_meta(get_the_ID(), 'motohome_loc');?>">
                </div>
                
            </div>

<?php
get_footer();
