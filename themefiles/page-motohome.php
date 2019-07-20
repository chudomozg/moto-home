<?php get_header(); ?>
<div class="mainContent">
    <div class="mainContentHeader">
        <div class="container">
        <div class="row">
            <div class="col">
            <div class="mainContent__headerBox d-flex justify-content-center">
                <div class="mainContent__header">Мотодома</div>
            </div>
            </div>
        </div>
        </div>
    </div>
    <div class="motohomeContent mth_page_all_motohome">
        <div class="container">
            <div class="row">
                <div class="col">
                    <select name="wp_cn_front_region_select" id="wp_cn_front_region_select">
                        <?php
                        $region_options = mth_reion_select_fill();
                        foreach ($region_options as $key=>$value){
                            echo ('<option value="'.$key.'">'.$value.'</option>');
                        }
                        ?>
                    </select>
                    <select name="wp_cn_front_city_select" id="wp_cn_front_city_select">
                        
                    </select>
                    <div class="motohomeContentMap">
                        <div id="map" style="width:100%; height:462px"></div>
                    </div>
                    <div class="motohomeContentWrapper d-flex flex-wrap justify-content-between align-items-stretch">
                  
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
?>