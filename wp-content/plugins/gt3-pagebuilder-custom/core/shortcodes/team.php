<?php

class ourteam
{
    public function register_shortcode($shortcodeName)
    {
        function shortcode_ourteam($atts, $content = null)
        {
            $compile = '';
            extract(shortcode_atts(array(
                'heading_alignment' => 'left',
                'heading_size' => $GLOBALS["pbconfig"]['default_heading_in_module'],
                'heading_color' => '',
                'heading_text' => '',
                'order' => 'ASC',
                'cpt_ids' => '0',
                'items_per_line' => '1',
            ), $atts));

            if ($items_per_line < 1) {
                $items_per_line = 1;
            }
            $item_width = (100 / $items_per_line) - 0.5;

            #heading
            if (strlen($heading_color) > 0) {
                $custom_color = "color:#{$heading_color};";
            }
            if (strlen($heading_text) > 0) {
                $compile .= "<div class='bg_title'><" . $heading_size . " style='" . (isset($custom_color) ? $custom_color : '') . ((strlen($heading_alignment) > 0 && $heading_alignment !== 'left') ? 'text-align:' . $heading_alignment . ';' : '') . "' class='headInModule'>{$heading_text}</" . $heading_size . "></div>";
            }
            $compile .= '
            <ul class="grid_elements">';

            $wp_query = new WP_Query();

            if (strlen($cpt_ids) > 0 && $cpt_ids !== "0") {
                $cpt_ids = explode(",", $cpt_ids);
            }

            if (is_array($cpt_ids) && count($cpt_ids) > 0) {
                $args = array(
                    'post_type' => 'team',
                    'post__in' => $cpt_ids,
                    'post_count' => -1,
                    'max_num_pages' => -1,
                    'posts_per_page' => -1,
                    'order' => $order
                );
            } else {
                $args = array(
                    'post_type' => 'team',
                    'order' => $order
                );
            }

            $wp_query->query($args);
            while ($wp_query->have_posts()) : $wp_query->the_post();
                $gt3_theme_pagebuilder = get_post_meta(get_the_ID(), "pagebuilder", true);
                $position = $gt3_theme_pagebuilder['page_settings']['team']['position'];
                if (strlen($position)>0) {$position = ", ".$position;}

                $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'single-post-thumbnail');

                $compile .= '
					<li class="item" style="width:' . $item_width . '%">
						<div class="ip">
                            <div class="img_block">';
            if (has_post_thumbnail()) {
                $compile .= '<img src="' . aq_resize($featured_image[0], "520", "740", true, true, true) . '" alt="' . get_the_title() . '" />';
            }

            $compile .= '
                            </div>
                            <div class="meta_body">
                                <h6>' . get_the_title() . $position .'</h6>
                                <div class="team_desc">' . get_the_content() . '</div>
                                <div class="team_icons_wrapper">';
            if (isset($gt3_theme_pagebuilder['page_settings']['icons']) ? $socicons = $gt3_theme_pagebuilder['page_settings']['icons'] : $socicons = false) ;
            if (is_array($socicons)) {
                foreach ($socicons as $key => $value) {
                    if ($value['link'] == '') $value['link'] = '#';
                    $compile .= '<a href="' . $value['link'] . '" class="teamlink" title="' . $value['name'] . '" data-color="#' . $value['fcolor'] . '" data-hover-color="#' . $value['bcolor'] . '" style="color:#' . $value['fcolor'] . ';border-color:#' . $value['bcolor'] . ';"><i class="' . $value['data-icon-code'] . '"></i></a>';
                }
            }
            $compile .= '   		</div>
                            </div>
                        </div>
                    </li>
				';

            endwhile;
            wp_reset_query();

            $compile .= '</ul>
            <div class="clear"></div>';
			$GLOBALS['showOnlyOneTimeJS']['team_js'] = "
			<script>
				/*jQuery(document).ready(function($) {
					jQuery('.teamlink').each(function () {
						var team_link_color = jQuery(this).attr('data-color');
						var team_link_hover_color = jQuery(this).attr('data-hover-color');

						jQuery(this).hover(function () {
							jQuery(this).css('background-color', team_link_hover_color);
						},
						function () {
							jQuery(this).css('background-color', team_link_color);
						});
					});
				});*/
			</script>
			";


            return $compile;
        }

        add_shortcode($shortcodeName, 'shortcode_ourteam');
    }
}

#Shortcode name
$shortcodeName = "ourteam";
$shortcode_ourteam = new ourteam();
$shortcode_ourteam->register_shortcode($shortcodeName);

?>