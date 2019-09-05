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
                            data-height ="80%"
                            
                            data-fit="cover">  
                            <?php $slides = carbon_get_post_meta(get_the_ID(), 'motohome_gallery');
                                foreach ($slides as $slide):
                            ?>
                                <a href="<?php echo wp_get_attachment_image_url($slide, "full"); ?>"><img src="<?php echo wp_get_attachment_image_url($slide, "full"); ?>" alt="Image" width="100px" height="64px"></a>
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
                
                <?php
                $data_arr = carbon_get_post_meta( $post->ID, 'rooms');
                for ($i=0; $i<count($data_arr);$i++){
                ?>
                    <div class="mth_front_rooms">
                        <div class="mth_room_unit">
                            <div class="mth_list_cal" id="mth_list_cal_<?php the_ID(); echo ("-".$i); ?>">
                                <form id="mth_room_form_<?php the_ID(); echo ("-".$i); ?>">
                                    <input type="hidden" class="mth_hid_cal_input" id="mth_hid_cal_input_<?php the_ID(); echo ("-".$i); ?>">
                                    <input type="hidden" class="mth_hid_room_id_input" id="mth_hid_room_id_input_<?php the_ID(); echo ("-".$i); ?>" value="<?php the_ID(); echo ("-".$i); ?>">
                                </form> 
                            </div>
                            <div class="mth_list_content articleTxt__box">
                                    <div class="articleTxt__header">
                                        <div class="mth_room_title">
                                            <?php echo ( $data_arr[$i]['room_name']) ?>
                                        </div>
                                        <div class="mth_room_coast"> Стоимость:
                                            <?php echo ( $data_arr[$i]['room_cost']) ?>
                                        </div>
                                    </div>
                                    <div class="articleTxt__text">
                                        <?php echo ( $data_arr[$i]['room_desc']) ?>
                                    </div>
                            </div>
                        </div>
                        <div class="motohomeHeaderWrapper">
                           <div class="motohomeHeader">
                               <div class="settings__rowCenter settings__rowCenter--motohome">
                                   <div class="mth_button_wrapper settings__calculateBtnWrapper">
                                       <button form="mth_room_form_<?php the_ID(); echo ("-".$i); ?>" class="settings__calculateBtn mth_home_submit_button">Забронировать
                                       </button>
                                   </div>
                               </div>
                           </div>
                       </div>
                    </div>
                <?php 
                } 
                ?>
                


                <div class="mth_front_loc">
                        <input type="hidden" class="mth_front_loc_value" value="<?php echo carbon_get_post_meta(get_the_ID(), 'motohome_loc');?>">
                </div>
                
            </div>

<?php
get_footer();
