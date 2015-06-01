<?php

class testimonials_shortcode
{
    public function register_shortcode($shortcodeName)
    {
        function shortcode_testimonials($atts, $content = null)
        {
            if (!isset($compile)) {
                $compile = '';
            }

            extract(shortcode_atts(array(
                'heading_alignment' => 'left',
                'heading_size' => $GLOBALS["pbconfig"]['default_heading_in_module'],
                'heading_color' => '',
                'heading_text' => '',
                'cpt_ids' => '0',
                'sorting_type' => "new",
                'testimonials_in_line' => "1"
            ), $atts));

            if ($testimonials_in_line < 1) {
                $testimonials_in_line = 1;
            }
            $testimonial_width = (100 / $testimonials_in_line); // - 0.5;

            if ($testimonials_in_line == 2) {
                $classes_for_left_right = "";
            }

            #heading
            if (strlen($heading_color) > 0) {
                $custom_color = "color:#{$heading_color};";
            }
            if (strlen($heading_text) > 0) {
                $compile .= "<div class='bg_title'><a href='".esc_js("javascript:void(0)")."' class='btn_carousel_left'></a><" . $heading_size . " style='" . (isset($custom_color) ? $custom_color : '') . ((strlen($heading_alignment) > 0 && $heading_alignment !== 'left') ? 'text-align:' . $heading_alignment . ';' : '') . "' class='headInModule'>{$heading_text}</" . $heading_size . "><a href='".esc_js("javascript:void(0)")."' class='btn_carousel_right'></a></div>";
            }

            #sort converter
            switch ($sorting_type) {
                case "new":
                    $sort_type = "post_date";
                    break;
                case "random":
                    $sort_type = "rand";
                    break;
            }

            $compile .= "
			<div class='module_content testimonials_list perline" . $testimonials_in_line . "'>
			    <ul>";
            $args = array(
                'post_type' => "testimonials",
                'orderby' => $sort_type,
                'include' => (string)$cpt_ids,
                'post_status' => 'publish');

            $posts = get_posts($args);
            $postsi = 1;

            if (is_array($posts)) {
                foreach ($posts as $post) {
                    $gt3_theme_pagebuilder = get_post_meta($post->ID, "pagebuilder", true);

                    $testimonials_author = $gt3_theme_pagebuilder['page_settings']['testimonials']['testimonials_author'];
                    $testimonials_company = $gt3_theme_pagebuilder['page_settings']['testimonials']['company'];
                    $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'single-post-thumbnail');

                    if (strlen($testimonials_company) > 0 && strlen($testimonials_author) > 0) {
                        $coma = ", ";
                    } else {
                        $coma = "";
                    }
					if (($testimonials_in_line == 2 && $postsi % 2 == 0)) {
                        $testim_separator = "<li class=\"testimonial_separator\"><div></div></li>";
                    } else {
                        $testim_separator = "";
                    }
                    /*" . (strlen($featured_image[0]) > 0 ? "<img src='" . aq_resize($featured_image[0], "73", "73", true, true, true) . "' class='testimonials_ava'>" : "") . "*/
                    $compile .= "
<li style='width:calc(" . $testimonial_width . "% - 4px)' class='testimonial".$postsi." " . (($testimonials_in_line == 2 && $postsi % 2 !== 0) ? "right_image" : "left_image") . "'>
    <div class='item " . (strlen($featured_image[0]) > 0 ? "with_image" : "without_image") . "'>
        <div class='text_block'>
            <div class='testimonials_text'>
                <p>" . $post->post_content . "</p>
            </div>
        </div>
        <div class='name_and_position'><span>{$testimonials_author}{$coma} <i>{$testimonials_company}</i></span></div>
    </div>
</li>" . $testim_separator . "
";
                    $postsi++;
                }
            }

            $compile .= "</ul>
			</div>";
			
			return $compile;
        }

        add_shortcode($shortcodeName, 'shortcode_testimonials');
    }
}

#Shortcode name
$shortcodeName = "testimonials";
$testimonials = new testimonials_shortcode();
$testimonials->register_shortcode($shortcodeName);

?>