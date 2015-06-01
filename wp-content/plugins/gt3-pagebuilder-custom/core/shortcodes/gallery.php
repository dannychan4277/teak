<?php

class show_gallery {

	public function register_shortcode($shortcodeName) {
		function shortcode_show_gallery($atts, $content = null) {

            wp_enqueue_script('gt3_popup_js', get_template_directory_uri() . '/js/popup.js', array(), false, true);
            wp_enqueue_script('gt3_cookie_js', get_template_directory_uri() . '/js/jquery.cookie.js', array(), false, true);
			
            $compile = "";

			extract( shortcode_atts( array(
                'heading_alignment' => 'left',
                'heading_size' => $GLOBALS["pbconfig"]['default_heading_in_module'],
                'heading_color' => '',
                'heading_text' => '',
                'preview_thumbs_format' => 'rectangle',
                'images_in_a_row' => '4',
                'width' => $GLOBALS["pbconfig"]['gallery_module_default_width'],
                'height' => $GLOBALS["pbconfig"]['gallery_module_default_height'],
                'galleryid' => '',
			), $atts ) );

            switch($images_in_a_row) {
                case 1:
                    $width = 1170;
                    break;
                case 2:
                    $width = 600;
                    break;
                case 3:
                    $width = 600;
                    break;
                case 4:
                    $width = 600;
                    break;
            }

            $height = $width;

            if ($preview_thumbs_format == "rectangle") {
                $height = 70*$width/100;

                /* Spike */
                if ($images_in_a_row == 1) {
                    $height = 71.6*$width/100;
                }
            }

            $width = $width."px";
            $height = $height."px";
			
			$all_likes = gt3pb_get_option("likes");
            $all_views = gt3pb_get_option("views");

            #heading
            if (strlen($heading_color)>0) {$custom_color = "color:#{$heading_color};";}
            if (strlen($heading_text)>0) {
                $compile .= "<div class='bg_title'><".$heading_size." style='".(isset($custom_color) ? $custom_color : '') . ((strlen($heading_alignment) > 0 && $heading_alignment !== 'left') ? 'text-align:'.$heading_alignment.';' : '')."' class='headInModule'>{$heading_text}</".$heading_size."></div>";
            }

			$compile .= '
			<div class="featured_items">
			    <div class="items' . $images_in_a_row . ' featured_gallery" data-count="' . $images_in_a_row . '">
                    <ul class="item_list">
			';

            $galleryPageBuilder = gt3pb_get_plugin_pagebuilder($galleryid);

            if (isset($galleryPageBuilder['sliders']['fullscreen']['slides']) && is_array($galleryPageBuilder['sliders']['fullscreen']['slides'])) {
                foreach ($galleryPageBuilder['sliders']['fullscreen']['slides'] as $imageid => $image) {

                    if ($image['slide_type'] == "video") {
                        $thishref = $image['src']."&width=100%&height=100%;";
                    } else {
                        $thishref = wp_get_attachment_url($image['attach_id']);
                    }

                    $all_views[$imageid] = (isset($all_views[$imageid]) ? $all_views[$imageid] : 0)+1;
					
					if (isset($image['title']['value']) && strlen($image['title']['value'])>0) {$photoTitleOutput = $image['title']['value'];} else {$photoTitleOutput = "";}
                    if (isset($image['caption']['value']) && strlen($image['caption']['value'])>0) {$photoCaption  = $image['caption']['value'];} else {$photoCaption = "";}

                        $compile .= '
                        <li class="isset_fimage">
                            <div class="item">
                                <div class="prelative">
                                    <a href="'.$thishref.'" class="prettyPhoto" rel="gallery[]">
                                        <div class="featured_item_fadder">
                                            <img src="'.aq_resize(wp_get_attachment_url($image['attach_id']), $width, $height, true, true, true).'" />
                                        </div>
                                    </a>
                                    <!--div class="item_info">
                                        <div class="views_likes">
                                            <div class="post-views">
                                                <i class="stand_icon icon-eye-open"></i>
                                                <span>'.$all_views[$imageid].'</span>
                                            </div>
                                            <div class="post_likes gallery_likes_add '.(isset($_COOKIE['like'.$imageid]) ? "already_liked" : "") . '" data-attachid="'.$imageid.'">
                                                <i class="stand_icon '.(isset($_COOKIE['like'.$imageid]) ? "icon-heart" : "icon-heart-empty") . '"></i>
                                                <span>'.((isset($all_likes[$imageid]) && $all_likes[$imageid] > 0) ? $all_likes[$imageid] : 0).'</span>
                                            </div>
                                        </div>
                                    </div-->
                                </div>
                            </div>
                        </li>
                        ';

                    unset($photoTitleOutput, $photoCaption);
                }
				gt3pb_update_option("views", $all_views);
            }

			$compile .= "
                    </ul>
                    <div class='clear'></div>
                </div>
            </div>
            ";
            $GLOBALS['showOnlyOneTimeJS']['prettyPhoto'] = "
			<script>
				jQuery(document).ready(function($) {
					jQuery('.prettyPhoto').prettyPhoto({social_tools: ''});
				});
			</script>
			";

            $GLOBALS['showOnlyOneTimeJS']['gallery_likes'] = "
			<script>
				jQuery(document).ready(function($) {
					jQuery('.gallery_likes_add').click(function(){
					var gallery_likes_this = jQuery(this);
					if (!jQuery.cookie('like'+gallery_likes_this.attr('data-attachid'))) {
                        jQuery.post(gt3_ajaxurl, {
                            action:'add_like_attachment',
                            attach_id:jQuery(this).attr('data-attachid')
                        }, function (response) {
                            jQuery.cookie('like'+gallery_likes_this.attr('data-attachid'), 'true', { expires: 7, path: '/' });
                            gallery_likes_this.addClass('already_liked');
							gallery_likes_this.find('span').text(response);
							gallery_likes_this.find('.icon-heart-empty').removeClass('icon-heart-empty').addClass('icon-heart');
                        });
                    }
                    });
				});
			</script>
			";
			
			return $compile;
			
		}
		add_shortcode($shortcodeName, 'shortcode_show_gallery');
	}
}

add_action( 'wp_ajax_add_like_attachment', 'gt3_add_like' );
add_action( 'wp_ajax_nopriv_add_like_attachment', 'gt3_add_like' );
function gt3_add_like() {
    $all_likes = gt3pb_get_option("likes");
    $attach_id = absint($_POST['attach_id']);
    $all_likes[$attach_id] = (isset($all_likes[$attach_id]) ? $all_likes[$attach_id] : 0)+1;
    gt3pb_update_option("likes", $all_likes);
    echo $all_likes[$attach_id];
    die();
}

#Shortcode name
$shortcodeName="show_gallery";
$shortcode_show_gallery = new show_gallery();
$shortcode_show_gallery->register_shortcode($shortcodeName);

?>