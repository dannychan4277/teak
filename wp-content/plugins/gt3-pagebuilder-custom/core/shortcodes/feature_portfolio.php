<?php

class feature_portfolio
{
    public function register_shortcode($shortcodeName)
    {
        function shortcode_feature_portfolio($atts, $content = null)
        {
            wp_enqueue_script('gt3_cookie_js', get_template_directory_uri() . '/js/jquery.cookie.js', array(), false, true);

            $compile = '';
            extract(shortcode_atts(array(
                'heading_alignment' => 'left',
                'heading_size' => $GLOBALS["pbconfig"]['default_heading_in_module'],
                'heading_color' => '',
                'heading_text' => '',
                'number_of_posts' => $GLOBALS["pbconfig"]['featured_portfolio_default_number_of_posts'],
                'posts_per_line' => '2',
                'selected_categories' => '',
                'sorting_type' => "new",
            ), $atts));

            #heading
            if (strlen($heading_color) > 0) {
                $custom_color = "color:#{$heading_color};";
            }
            if (strlen($heading_text) > 0) {
                $compile = "<div class='bg_title'><" . $heading_size . " style='" . (isset($custom_color) ? $custom_color : '') . ((strlen($heading_alignment) > 0 && $heading_alignment !== 'left') ? 'text-align:' . $heading_alignment . ';' : '') . "' class='headInModule'>{$heading_text}</" . $heading_size . "></div>";
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

            $compile .= '
        <div class="featured_items clearfix">
            <div class="items' . $posts_per_line . ' featured_portfolio" data-count="' . $posts_per_line . '">
                <ul class="item_list">
        ';

            if (strlen($selected_categories) > 0) {
                $post_type_terms = explode(",", $selected_categories);
            } else {
                $post_type_terms = array();
            }
            $wp_query = new WP_Query();
            $args = array(
                'post_type' => 'port',
                'posts_per_page' => $number_of_posts,
                'ignore_sticky_posts' => 1,
                'orderby' => $sort_type,
            );

            if (count($post_type_terms) > 0) {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'portcat',
                        'field' => 'id',
                        'terms' => $post_type_terms
                    )
                );
            }

            $wp_query->query($args);

            while ($wp_query->have_posts()) : $wp_query->the_post();

                $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'single-post-thumbnail');
                $all_likes = gt3pb_get_option("likes");
                $post_views = (get_post_meta(get_the_ID(), "post_views", true) > 0 ? get_post_meta(get_the_ID(), "post_views", true) : "0");

                if (strlen($featured_image[0]) > 0) {
                    $featured_image_url = aq_resize($featured_image[0], "570", "425", true, true, true);
                    $full_image_url = $featured_image[0];
                    $featured_image_full = '
                    <a href="' . get_permalink(get_the_ID()) . '">
                        <div class="featured_item_fadder">
                            <img src="' . $featured_image_url . '" />
                        </div>
                    </a>';
                    $isset_fimage_status = true;
                } else {
                    $featured_image_full = '';
                    $isset_fimage_status = false;
                }

                $post = get_post();
                $post_excerpt = ((strlen($post->post_excerpt) > 0) ? $post->post_excerpt : gt3pb_smarty_modifier_truncate(get_the_content(), 92, ''));

                $new_term_list = get_the_terms(get_the_id(), "portcat");
                $echoallterm = "";
                if (is_array($new_term_list)) {
                    foreach ($new_term_list as $term) {
                        $echoallterm .= "<a href='" . get_term_link($term->slug, "portcat") . "'>" . $term->name . "</a>, ";
                    }
                } else {
                    $tempname = 'Uncategorized';
                }

                $compile .= '
                    <li class="' . ($isset_fimage_status == true ? "isset_fimage" : "without_fimage") . '">
                        <div class="item">
                            <div class="prelative">
                                ' . $featured_image_full . '
                                <div class="item_info">
                                    <h5><a href="' . get_permalink() . '">' . get_the_title() . '</a></h5>
                                    <div class="fp_cat">
                                        ' . trim($echoallterm, ', ') . '
                                    </div>
                                    <div class="views_likes">
                                        <!--div class="post-views">
                                            <i class="stand_icon icon-eye-open"></i>
                                            <span>' . $post_views . '</span>
                                        </div-->
                                        <div class="post_likes post_likes_add ' . (isset($_COOKIE['like' . get_the_ID()]) ? "already_liked" : "") . '" data-postid="' . get_the_ID() . '" data-modify="like_post">
                                            <i class="stand_icon ' . (isset($_COOKIE['like' . get_the_ID()]) ? "icon-heart" : "icon-heart-empty") . '"></i>
                                            <span>' . ((isset($all_likes[get_the_ID()]) && $all_likes[get_the_ID()] > 0) ? $all_likes[get_the_ID()] : 0) . '</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    ';

            endwhile;

            $compile .= '
                </ul>
            </div>
        </div>
        ';

            wp_reset_query();

            return $compile;

        }

        add_shortcode($shortcodeName, 'shortcode_feature_portfolio');
    }
}

#Shortcode name
$shortcodeName = "feature_portfolio";
$shortcode_feature_portfolio = new feature_portfolio();
$shortcode_feature_portfolio->register_shortcode($shortcodeName);
?>