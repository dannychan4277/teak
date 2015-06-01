<?php

class portfolio_shortcode
{
    public function register_shortcode($shortcodeName)
    {
        function shortcode_portfolio($atts, $content = null)
        {

            wp_enqueue_script('gt3_popup_js', get_template_directory_uri() . '/js/popup.js', array(), false, true);
            wp_enqueue_script('gt3_isotope_js', get_template_directory_uri() . '/js/isotope.min.js', array(), false, false);
			wp_enqueue_script('gt3_cookie_js', get_template_directory_uri() . '/js/jquery.cookie.js', array(), false, true);

            if (!isset($compile)) {
                $compile = '';
            }

            extract(shortcode_atts(array(
                'heading_alignment' => 'left',
                'heading_size' => $GLOBALS["pbconfig"]['default_heading_in_module'],
                'heading_color' => '',
                'heading_text' => '',
                'posts_per_page' => '4',
                'view_type' => '1 column',
                'filter' => 'on',
                'selected_categories' => '',
            ), $atts));

            #heading
            if (strlen($heading_color) > 0) {
                $custom_color = "color:#{$heading_color};";
            }
            if (strlen($heading_text) > 0) {
                $compile .= "<div class='bg_title'><" . $heading_size . " style='" . $custom_color . ((strlen($heading_alignment) > 0 && $heading_alignment !== 'left') ? 'text-align:' . $heading_alignment . ';' : '') . "' class='headInModule'>{$heading_text}</" . $heading_size . "></div>";
            }

            switch ($view_type) {
                case "1 column":
                    $view_type_class = "columns1";
                    BREAK;
                case "2 columns":
                    $view_type_class = "columns2";
                    BREAK;
                case "3 columns":
                    $view_type_class = "columns3";
                    BREAK;
                case "4 columns":
                    $view_type_class = "columns4";
                    BREAK;
                case "Masonry 2 columns":
                    $view_type_class = "masonry_columns2";
                    BREAK;
				case "Masonry 3 columns":
                    $view_type_class = "masonry_columns3";
                    BREAK;
				case "Masonry 4 columns":
                    $view_type_class = "masonry_columns4";
                    BREAK;
            }

            if ($view_type_class == "columns1" || $view_type_class == "columns2" || $view_type_class == "columns3" || $view_type_class == "columns4"){
                $portfolio_isotope_activate = '
                jQuery(".is_masonry").isotope({
                    layoutMode: "fitRows"
                },"reLayout");
                ';
            } elseif ($view_type_class == "masonry_columns2" || $view_type_class == "masonry_columns3" || $view_type_class == "masonry_columns4"){
                $portfolio_isotope_activate = '
                jQuery(".is_masonry").isotope("reLayout");
                ';
            }

            $post_type_terms = array();
            if (strlen($selected_categories) > 0) {
                $post_type_terms = explode(",", $selected_categories);
            }

            #Filter
            if ($filter == "on") {
                $compile .= gt3pb_showPortCatsMasonry($post_type_terms);
            }

            $compile .= '<div class="portfolio_block ' . $view_type_class . '"><div class="portwrap is_masonry">';



                $GLOBALS['showOnlyOneTimeJS']['portfolio'] = '
                <script>
                var posts_already_showed = 0;
                jQuery(window).load(function () {
                    gt3_get_posts("gt3_get_posts", "port", "' . $posts_per_page . '", posts_already_showed, "'.$view_type_class.'", "'.$selected_categories.'");
                    posts_already_showed = posts_already_showed + ' . $posts_per_page . ';
                });

                jQuery(".load_more_posts").click(function(){
                    gt3_get_posts("gt3_get_posts", "port", "' . $posts_per_page . '", posts_already_showed, "'.$view_type_class.'", "'.$selected_categories.'");
                    posts_already_showed = posts_already_showed + ' . $posts_per_page . ';
                    return false;
                });

                function gt3_get_posts(action, post_type, posts_count, posts_already_showed, template, selected_categories) {
                        jQuery.post(gt3_ajaxurl, { action: action, post_type: post_type, posts_count: posts_count, posts_already_showed: posts_already_showed, template: template, selected_categories: selected_categories })
                        .done(function (data) {
                            if (data.length < 1) {
                                jQuery(".load_more_posts").hide("fast");
                            }

                            jQuery(".is_masonry").isotope("insert", jQuery(data), function () {
                                jQuery(".is_masonry").ready(function () {
                                    ' . $portfolio_isotope_activate . '
                                });
                            });
                        });
                    };
                </script>
                ';

            $compile .= '</div></div>';

            $compile .= '<div class="load_more_posts_cont">
                            <a href="' . esc_js("javascript:void(0)") . '" class="load_more_posts">' . __("More Items", "theme_localization") . '</a>
                        </div>';

            $GLOBALS['showOnlyOneTimeJS']['isotope'] = '
            <script>
                function portfolio_is_masonry() {
                    jQuery(".optionset li a").click(function(){
                        jQuery(".optionset li a").removeClass("selected");
                        jQuery(".optionset li").removeClass("selected");
                        jQuery(this).addClass("selected");
                        jQuery(this).parent().addClass("selected");
                        var filterSelector = jQuery(this).attr("data-category");

                        jQuery(".is_masonry").isotope({
                            filter: filterSelector,
                        });
                        return false;
                    });
                }

                jQuery(window).load(function () {
                    portfolio_is_masonry();
                    jQuery(".optionset li").eq(0).find("a").click();
                });

                jQuery(window).resize(function(){
                    portfolio_is_masonry();
                    jQuery(".optionset li").eq(0).find("a").click();
                });

            </script>
            ';

            wp_reset_query();
            return $compile;
        }

        add_shortcode($shortcodeName, 'shortcode_portfolio');
    }
}

#Shortcode name
$shortcodeName = "portfolio";
$portfolio = new portfolio_shortcode();
$portfolio->register_shortcode($shortcodeName);


/* AJAX PART */
add_action('wp_ajax_gt3_get_posts', 'gt3_get_posts');
add_action('wp_ajax_nopriv_gt3_get_posts', 'gt3_get_posts');
function gt3_get_posts()
{

    if ($_REQUEST['post_type'] == "port") {
        $wp_query_get_blog_posts = new WP_Query();
        $args = array(
            'post_type' => esc_attr($_REQUEST['post_type']),
            'offset' => absint($_REQUEST['posts_already_showed']),
            'post_status' => 'publish',
            'posts_per_page' => absint($_REQUEST['posts_count'])
        );

        $post_type_terms = array();
        if (isset($_REQUEST['selected_categories']) && strlen($_REQUEST['selected_categories']) > 0) {
            $post_type_terms = explode(",", esc_attr($_REQUEST['selected_categories']));
        }

        if (count($post_type_terms) > 0) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'portcat',
                    'field' => 'id',
                    'terms' => $post_type_terms
                )
            );
        }

        $all_likes = gt3pb_get_option("likes");

        $wp_query_get_blog_posts->query($args);
        $compile = '';
        while ($wp_query_get_blog_posts->have_posts()) : $wp_query_get_blog_posts->the_post();

            $pf = get_post_format();
            if (empty($pf)) $pf = "text";
            $gt3_theme_pagebuilder = gt3pb_get_plugin_pagebuilder(get_the_ID());

            $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_id()), 'single-post-thumbnail');
            if (strlen($featured_image[0]) < 1) {
                $featured_image[0] = "";
            }

            $post_views = (get_post_meta(get_the_ID(), "post_views", true) > 0 ? get_post_meta(get_the_ID(), "post_views", true) : "0");

            if (isset($gt3_theme_pagebuilder['page_settings']['portfolio']['work_link']) && strlen($gt3_theme_pagebuilder['page_settings']['portfolio']['work_link']) > 0) {
                $linkToTheWork = esc_url($gt3_theme_pagebuilder['page_settings']['portfolio']['work_link']);
                $target = "target='_blank'";
            } else {
                $linkToTheWork = get_permalink();
                $target = "";
            }

            $echoallterm = '';

            $new_term_list = get_the_terms(get_the_id(), "portcat");
            if (is_array($new_term_list)) {
                foreach ($new_term_list as $term) {
                    $tempname = strtr($term->name, array(
                        " " => "-",
                        "'" => "-",
                    ));
                    $echoallterm .= strtolower($tempname) . " ";
                    $echoterm = $term->name;
                }
            } else {
                $tempname = 'Uncategorized';
            }

            /* 1 column */
            if ($_REQUEST['template'] == "columns1") {
                $port_content_show = ((strlen(get_the_excerpt()) > 0) ? get_the_excerpt() : gt3pb_smarty_modifier_truncate(get_the_content(), 300));

                $compile .= '
                    <div data-category="' . $echoallterm . '" class="' . $echoallterm . ' portfolio_item">
                        <div class="row">';
                if (strlen($featured_image[0]) > 0) {
                    $compile .= '
                    <div class="portfolio_item_img featured_item_fadder span6">
                        <a ' . $target . ' href="' . $linkToTheWork . '">
                            <img src="' . aq_resize($featured_image[0], "700", "525", true, true, true) . '" alt="">
                        </a>
                    </div>
                    ';
                }

                $terms = get_the_terms(get_the_ID(), 'portcat');
                if ($terms && !is_wp_error($terms)) {
                    $draught_links = array();
                    foreach ($terms as $term) {
                        $draught_links[] = '<a href="' . get_term_link($term->slug, "portcat") . '">' . $term->name . '</a>';
                    }
                    $on_draught = (is_array($draught_links) ? join(", ", $draught_links) : "");
                }

                $compile .= '
                        <div class="post_preview portfolio_dscr blog_post_preview ' . (strlen($featured_image[0]) > 0 ? "span6" : "span12") . '">
                            <div class="preview_wrapper">
                                <div class="preview_topblock">
                                    <h3 class="entry-title"><a href="' . $linkToTheWork . '">' . get_the_title() . '</a></h3>
                                    <div class="meta">
                                        <span><i class="icon-calendar-empty"></i>' . get_the_time("F d, Y") . '</span>
                                        <span class="category">
                                            <i class="icon-folder-close-alt"></i>
                                            ' . (isset($on_draught) ? $on_draught : "") . '
                                        </span>
                                        <span class="pf_meta_comments"><i class="icon-comment-alt"></i><a href="' . get_comments_link() . '">' . get_comments_number(get_the_ID()) . '</a></span>
                                        <div class="views_likes">
                                            <div class="post_likes post_likes_add ' . (isset($_COOKIE['like' . get_the_ID()]) ? "already_liked" : "") . '" data-postid="' . get_the_ID() . '" data-modify="like_post">
                                                <i class="stand_icon ' . (isset($_COOKIE['like' . get_the_ID()]) ? "icon-heart" : "icon-heart-empty") . '"></i>
                                                <span>' . ((isset($all_likes[get_the_ID()]) && $all_likes[get_the_ID()] > 0) ? $all_likes[get_the_ID()] : 0) . '</span>
                                            </div>
                                        </div>';

                $compile .= '
                                    </div>
                                </div>
                                <article class="contentarea with_read_more_button">
                                    ' . $port_content_show . '<br>' . do_shortcode("[custom_button style='btn_small btn_type1' icon='' target='_self' href='" . get_permalink(get_the_id()) . "']" . __('Read More<i class="icon-caret-right"></i>', 'gt3_builder') . "[/custom_button]") . '
                                </article>
                            </div>
                        </div>
                    </div>
                    <div class="horisontal_divider t2"></div>
                </div>';
            }

            /* 2 columns */
            if ($_REQUEST['template'] == "columns2") {
                $port_content_show = ((strlen(get_the_excerpt()) > 0) ? get_the_excerpt() : gt3pb_smarty_modifier_truncate(get_the_content(), 300));

                $terms = get_the_terms(get_the_ID(), 'portcat');
                if ($terms && !is_wp_error($terms)) {
                    $draught_links = array();
                    foreach ($terms as $term) {
                        $draught_links[] = '<a href="' . get_term_link($term->slug, "portcat") . '">' . $term->name . '</a>';
                    }
                    $on_draught = (is_array($draught_links) ? join(", ", $draught_links) : "");
                }

                $compile .= '
                <div data-category="' . $echoallterm . '" class="isset_fimage ' . $echoallterm . ' portfolio_item">
                    <div class="item">
                        <div class="prelative">
                            <a href="' . $linkToTheWork . '">
                                <div class="featured_item_fadder">
                                    <img src="' . aq_resize($featured_image[0], "670", "520", true, true, true) . '" />
                                </div>
                            </a>
                            <div class="item_info">
                                <h5><a href="' . $linkToTheWork . '">' . get_the_title() . '</a></h5>
                                <div class="fp_cat">
                                    ' . (isset($on_draught) ? $on_draught : "") . '
                                </div>
                                <div class="views_likes">
                                    <div class="post_likes post_likes_add ' . (isset($_COOKIE['like' . get_the_ID()]) ? "already_liked" : "") . '" data-postid="' . get_the_ID() . '" data-modify="like_post">
                                        <i class="stand_icon ' . (isset($_COOKIE['like' . get_the_ID()]) ? "icon-heart" : "icon-heart-empty") . '"></i>
                                        <span>' . ((isset($all_likes[get_the_ID()]) && $all_likes[get_the_ID()] > 0) ? $all_likes[get_the_ID()] : 0) . '</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
            }

            /* 3 columns */
            if ($_REQUEST['template'] == "columns3") {
                $port_content_show = ((strlen(get_the_excerpt()) > 0) ? get_the_excerpt() : gt3pb_smarty_modifier_truncate(get_the_content(), 300));

                $terms = get_the_terms(get_the_ID(), 'portcat');
                if ($terms && !is_wp_error($terms)) {
                    $draught_links = array();
                    foreach ($terms as $term) {
                        $draught_links[] = '<a href="' . get_term_link($term->slug, "portcat") . '">' . $term->name . '</a>';
                    }
                    $on_draught = (is_array($draught_links) ? join(", ", $draught_links) : "");
                }

                $compile .= '
                <div data-category="' . $echoallterm . '" class="isset_fimage ' . $echoallterm . ' portfolio_item">
                    <div class="item">
                        <div class="prelative">
                            <a href="' . $linkToTheWork . '">
                                <div class="featured_item_fadder">
                                    <img src="' . aq_resize($featured_image[0], "670", "520", true, true, true) . '" />
                                </div>
                            </a>
                            <div class="item_info">
                                <h5><a href="' . $linkToTheWork . '">' . get_the_title() . '</a></h5>
                                <div class="fp_cat">
                                    ' . (isset($on_draught) ? $on_draught : "") . '
                                </div>
                                <div class="views_likes">
                                    <div class="post_likes post_likes_add ' . (isset($_COOKIE['like' . get_the_ID()]) ? "already_liked" : "") . '" data-postid="' . get_the_ID() . '" data-modify="like_post">
                                        <i class="stand_icon ' . (isset($_COOKIE['like' . get_the_ID()]) ? "icon-heart" : "icon-heart-empty") . '"></i>
                                        <span>' . ((isset($all_likes[get_the_ID()]) && $all_likes[get_the_ID()] > 0) ? $all_likes[get_the_ID()] : 0) . '</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
            }

            /* 4 columns */
            if ($_REQUEST['template'] == "columns4") {
                $port_content_show = ((strlen(get_the_excerpt()) > 0) ? get_the_excerpt() : gt3pb_smarty_modifier_truncate(get_the_content(), 300));

                $terms = get_the_terms(get_the_ID(), 'portcat');
                if ($terms && !is_wp_error($terms)) {
                    $draught_links = array();
                    foreach ($terms as $term) {
                        $draught_links[] = '<a href="' . get_term_link($term->slug, "portcat") . '">' . $term->name . '</a>';
                    }
                    $on_draught = (is_array($draught_links) ? join(", ", $draught_links) : "");
                }

                $compile .= '
                <div data-category="' . $echoallterm . '" class="isset_fimage ' . $echoallterm . ' portfolio_item">
                    <div class="item">
                        <div class="prelative">
                            <a href="' . $linkToTheWork . '">
                                <div class="featured_item_fadder">
                                    <img src="' . aq_resize($featured_image[0], "670", "520", true, true, true) . '" />
                                </div>
                            </a>
                            <div class="item_info">
                                <h5><a href="' . $linkToTheWork . '">' . get_the_title() . '</a></h5>
                                <div class="fp_cat">
                                    ' . (isset($on_draught) ? $on_draught : "") . '
                                </div>
                                <div class="views_likes">
                                    <div class="post_likes post_likes_add ' . (isset($_COOKIE['like' . get_the_ID()]) ? "already_liked" : "") . '" data-postid="' . get_the_ID() . '" data-modify="like_post">
                                        <i class="stand_icon ' . (isset($_COOKIE['like' . get_the_ID()]) ? "icon-heart" : "icon-heart-empty") . '"></i>
                                        <span>' . ((isset($all_likes[get_the_ID()]) && $all_likes[get_the_ID()] > 0) ? $all_likes[get_the_ID()] : 0) . '</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                ';
            }


            /* Masonry 2 columns */
            if ($_REQUEST['template'] == "masonry_columns2") {
                $port_content_show = ((strlen(get_the_excerpt()) > 0) ? get_the_excerpt() : gt3pb_smarty_modifier_truncate(get_the_content(), 300));

                $terms = get_the_terms(get_the_ID(), 'portcat');
                if ($terms && !is_wp_error($terms)) {
                    $draught_links = array();
                    foreach ($terms as $term) {
                        $draught_links[] = '<a href="' . get_term_link($term->slug, "portcat") . '">' . $term->name . '</a>';
                    }
                    $on_draught = (is_array($draught_links) ? join(", ", $draught_links) : "");
                }

                $compile .= '
                <div data-category="' . $echoallterm . '" class="isset_fimage ' . $echoallterm . ' portfolio_item">
                    <div class="item">
                        <div class="prelative">
                            <a href="' . $linkToTheWork . '">
                                <div class="featured_item_fadder">
                                    <img src="' . $featured_image[0] . '" />
                                </div>
                            </a>
                            <div class="item_info">
                                <h5><a href="' . $linkToTheWork . '">' . get_the_title() . '</a></h5>
                                <div class="fp_cat">
                                    ' . (isset($on_draught) ? $on_draught : "") . '
                                </div>
                                <div class="views_likes">
                                    <div class="post_likes post_likes_add ' . (isset($_COOKIE['like' . get_the_ID()]) ? "already_liked" : "") . '" data-postid="' . get_the_ID() . '" data-modify="like_post">
                                        <i class="stand_icon ' . (isset($_COOKIE['like' . get_the_ID()]) ? "icon-heart" : "icon-heart-empty") . '"></i>
                                        <span>' . ((isset($all_likes[get_the_ID()]) && $all_likes[get_the_ID()] > 0) ? $all_likes[get_the_ID()] : 0) . '</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';


            }
			
			
			/* Masonry 3 columns */
            if ($_REQUEST['template'] == "masonry_columns3") {
                $port_content_show = ((strlen(get_the_excerpt()) > 0) ? get_the_excerpt() : gt3pb_smarty_modifier_truncate(get_the_content(), 300));

                $terms = get_the_terms(get_the_ID(), 'portcat');
                if ($terms && !is_wp_error($terms)) {
                    $draught_links = array();
                    foreach ($terms as $term) {
                        $draught_links[] = '<a href="' . get_term_link($term->slug, "portcat") . '">' . $term->name . '</a>';
                    }
                    $on_draught = (is_array($draught_links) ? join(", ", $draught_links) : "");
                }

                $compile .= '
                <div data-category="' . $echoallterm . '" class="isset_fimage ' . $echoallterm . ' portfolio_item">
                    <div class="item">
                        <div class="prelative">
                            <a href="' . $linkToTheWork . '">
                                <div class="featured_item_fadder">
                                    <img src="' . $featured_image[0] . '" />
                                </div>
                            </a>
                            <div class="item_info">
                                <h5><a href="' . $linkToTheWork . '">' . get_the_title() . '</a></h5>
                                <div class="fp_cat">
                                    ' . (isset($on_draught) ? $on_draught : "") . '
                                </div>
                                <div class="views_likes">
                                    <div class="post_likes post_likes_add ' . (isset($_COOKIE['like' . get_the_ID()]) ? "already_liked" : "") . '" data-postid="' . get_the_ID() . '" data-modify="like_post">
                                        <i class="stand_icon ' . (isset($_COOKIE['like' . get_the_ID()]) ? "icon-heart" : "icon-heart-empty") . '"></i>
                                        <span>' . ((isset($all_likes[get_the_ID()]) && $all_likes[get_the_ID()] > 0) ? $all_likes[get_the_ID()] : 0) . '</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';

            }
			
			/* Masonry 4 columns */
            if ($_REQUEST['template'] == "masonry_columns4") {
                $port_content_show = ((strlen(get_the_excerpt()) > 0) ? get_the_excerpt() : gt3pb_smarty_modifier_truncate(get_the_content(), 300));

                $terms = get_the_terms(get_the_ID(), 'portcat');
                if ($terms && !is_wp_error($terms)) {
                    $draught_links = array();
                    foreach ($terms as $term) {
                        $draught_links[] = '<a href="' . get_term_link($term->slug, "portcat") . '">' . $term->name . '</a>';
                    }
                    $on_draught = (is_array($draught_links) ? join(", ", $draught_links) : "");
                }


//

                $compile .= '
                <div data-category="' . $echoallterm . '" class="isset_fimage ' . $echoallterm . ' portfolio_item">
                    <div class="item">
                        <div class="prelative">
                            <a href="' . $linkToTheWork . '">
                                <div class="featured_item_fadder">
                                    <img src="' . $featured_image[0] . '" />
                                </div>
                            </a>
                            <div class="item_info">
                                <h5><a href="' . $linkToTheWork . '">' . get_the_title() . '</a></h5>
                                <div class="fp_cat">
                                    ' . (isset($on_draught) ? $on_draught : "") . '
                                </div>
                                <div class="views_likes">
                                    <div class="post_likes post_likes_add ' . (isset($_COOKIE['like' . get_the_ID()]) ? "already_liked" : "") . '" data-postid="' . get_the_ID() . '" data-modify="like_post">
                                        <i class="stand_icon ' . (isset($_COOKIE['like' . get_the_ID()]) ? "icon-heart" : "icon-heart-empty") . '"></i>
                                        <span>' . ((isset($all_likes[get_the_ID()]) && $all_likes[get_the_ID()] > 0) ? $all_likes[get_the_ID()] : 0) . '</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
            }

            /* Portfolio Grid Ajax */
            if ($_REQUEST['template'] == "portfolio_grid_ajax"){
                $terms = get_the_terms(get_the_ID(), 'portcat');
                $draught_links = array();
                if ($terms && !is_wp_error($terms)) {
                    foreach ($terms as $term) {
                        $draught_links[] = '<a href="' . get_term_link($term->slug, "portcat") . '">' . $term->name . '</a>';
                    }
                    $on_draught = (is_array($draught_links) ? join(", ", $draught_links) : "");
                }

                $compile .= '
                    <div class="blogpost_preview_fw port_type ' . $echoallterm . '" data-category="' . $echoallterm . '">
                        <div class="fw_preview_wrapper">
                            <img class="featured_image_standalone" src="' . aq_resize($featured_image[0], 1170, 950, true, true, true) . '" alt="" />
                            <div class="inner">
                                <h6 class="blogpost_title"><a ' . $target . ' href="' . $linkToTheWork . '">' . get_the_title() . '</a></h6>
                                <div class="inmeta">
                                    <div>
                                         '. implode(", ", $draught_links ) .'
                                    </div>
                                    <div class="post_likes post_likes_add ' . (isset($_COOKIE['like' . get_the_ID()]) ? "already_liked" : "") . '" data-postid="' . get_the_ID() . '" data-modify="like_post">
                                        <i class="stand_icon ' . (isset($_COOKIE['like' . get_the_ID()]) ? "icon-heart" : "icon-heart-empty") . '"></i>
                                        <span>' . ((isset($all_likes[get_the_ID()]) && $all_likes[get_the_ID()] > 0) ? $all_likes[get_the_ID()] : 0) . '</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
            }

            /* Portfolio Grid Margin Ajax */
            if ($_REQUEST['template'] == "portfolio_grid_margin_ajax"){
                $port_content_show = ((strlen(get_the_excerpt()) > 0) ? get_the_excerpt() : gt3pb_smarty_modifier_truncate(get_the_content(), 300));
                $terms = get_the_terms(get_the_ID(), 'portcat');
                if ($terms && !is_wp_error($terms)) {
                    $draught_links = array();
                    foreach ($terms as $term) {
                        $draught_links[] = '<a href="' . get_term_link($term->slug, "portcat") . '">' . $term->name . '</a>';
                    }
                    $on_draught = (is_array($draught_links) ? join(", ", $draught_links) : "");
                }

                $compile .= '
                <div class="blogpost_preview_fw port_type ' . $echoallterm . '" data-category="' . $echoallterm . '">
                    <div class="fw_preview_wrapper">
                        <div class="image_wrapper">
                            <img class="featured_image_standalone" src="' . aq_resize($featured_image[0], 1170, 950, true, true, true) . '" alt="" />
                        </div>
                        <div class="inner">
                            <h6 class="blogpost_title"><a ' . $target . ' href="' . $linkToTheWork . '">' . get_the_title() . '</a></h6>
                            <div class="inmeta">
                                <div>
                                     '. implode(", ", $draught_links ) .'
                                </div>
                                <div class="post_likes post_likes_add ' . (isset($_COOKIE['like' . get_the_ID()]) ? "already_liked" : "") . '" data-postid="' . get_the_ID() . '" data-modify="like_post">
                                    <i class="stand_icon ' . (isset($_COOKIE['like' . get_the_ID()]) ? "icon-heart" : "icon-heart-empty") . '"></i>
                                    <span>' . ((isset($all_likes[get_the_ID()]) && $all_likes[get_the_ID()] > 0) ? $all_likes[get_the_ID()] : 0) . '</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
            }

            /* Portfolio Grid Title Ajax */
            if ($_REQUEST['template'] == "portfolio_grid_title_ajax"){
                $terms = get_the_terms(get_the_ID(), 'portcat');
                if ($terms && !is_wp_error($terms)) {
                    $draught_links = array();
                    foreach ($terms as $term) {
                        $draught_links[] = '<a href="' . get_term_link($term->slug, "portcat") . '">' . $term->name . '</a>';
                    }
                    $on_draught = (is_array($draught_links) ? join(", ", $draught_links) : "");
                }

                $compile .= '
                <div class="blogpost_preview_fw port_type ' . $echoallterm . '" data-category="' . $echoallterm . '">
                    <div class="fw_preview_wrapper">
                        <div class="image_wrapper">
                            <img class="featured_image_standalone" src="' . aq_resize($featured_image[0], 1170, 950, true, true, true) . '" alt="" />
                        </div>
                        <div class="inner">
                            <h6 class="blogpost_title"><a ' . $target . ' href="' . $linkToTheWork . '">' . get_the_title() . '</a></h6>
                            <div class="inmeta">
                                <div>
                                     '. implode(", ", $draught_links ) .'
                                </div>
                                <div class="post_likes post_likes_add ' . (isset($_COOKIE['like' . get_the_ID()]) ? "already_liked" : "") . '" data-postid="' . get_the_ID() . '" data-modify="like_post">
                                    <i class="stand_icon ' . (isset($_COOKIE['like' . get_the_ID()]) ? "icon-heart" : "icon-heart-empty") . '"></i>
                                    <span>' . ((isset($all_likes[get_the_ID()]) && $all_likes[get_the_ID()] > 0) ? $all_likes[get_the_ID()] : 0) . '</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
            }

        endwhile;

        echo $compile;
    }

    die();
}
?>