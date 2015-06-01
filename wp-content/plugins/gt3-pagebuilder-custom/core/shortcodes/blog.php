<?php

class blog_shortcode
{

    public function register_shortcode($shortcodeName)
    {
        function shortcode_blog($atts, $content = null)
        {
            if (!isset($compile)) {
                $compile = '';
            }
            extract(shortcode_atts(array(
                'heading_alignment' => 'left',
                'heading_size' => $GLOBALS["pbconfig"]['default_heading_in_module'],
                'heading_color' => '',
                'heading_text' => '',
                'blog_type' => '',
                'posts_per_page' => '10',
                'posts_per_line' => '3',
                'masonry' => 'no',
                'cat_ids' => 'all',
				'pager_type' => 'type1',
            ), $atts));

            #heading
            if (strlen($heading_color) > 0) {
                $custom_color = "color:#{$heading_color};";
            } else {
                $custom_color = '';
            }
            if (strlen($heading_text) > 0) {
                $compile .= "<div class='bg_title'><" . $heading_size . " style='" . $custom_color . ((strlen($heading_alignment) > 0 && $heading_alignment !== 'left') ? 'text-align:' . $heading_alignment . ';' : '') . "' class='headInModule'>{$heading_text}</" . $heading_size . "></div>";
            }

            global $gt3pb_wp_query_in_shortcodes, $paged, $gt3_current_page_sidebar;

            if (empty($paged)) {
                $paged = (get_query_var('page')) ? get_query_var('page') : 1;
            }

            $gt3pb_wp_query_in_shortcodes = new WP_Query();
            $args = array(
                'post_type' => 'post',
                'paged' => $paged,
                'posts_per_page' => $posts_per_page,
            );

            if ($cat_ids !== "all" && $cat_ids !== "") {
                $args['cat'] = $cat_ids;
            }

            $gt3pb_wp_query_in_shortcodes->query($args);

            while ($gt3pb_wp_query_in_shortcodes->have_posts()) : $gt3pb_wp_query_in_shortcodes->the_post();

                $gt3_theme_pagebuilder = get_post_meta(get_the_ID(), "pagebuilder", true);
                $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'single-post-thumbnail');


                if (get_the_category()) $categories = get_the_category();
                $post_categ = '';
                $separator = ', ';
                if ($categories) {
                    foreach ($categories as $category) {
                        $post_categ = $post_categ . '<a href="' . get_category_link($category->term_id) . '">' . $category->cat_name . '</a>' . $separator;
                    }
                }

                if (get_the_tags() !== '') {
                    $posttags = get_the_tags();
                }
                if ($posttags) {
                    $post_tags = '';
                    $post_tags_compile = '<span class="tags"><i class="icon-tag"></i>';
                    foreach ($posttags as $tag) {
                        $post_tags = $post_tags . '<a href="?tag=' . $tag->slug . '">' . $tag->name . '</a>' . ', ';
                    }
                    $post_tags_compile .= ' ' . trim($post_tags, ', ') . '</span>';
                } else {
                    $post_tags_compile = '';
                }
				
				$post = get_post();
				
				$comments_num = '' . get_comments_number(get_the_ID()) . '';

				if ($comments_num == 1) {
					$comments_text = '';
				} else {
					$comments_text = '';
				}

                if ($blog_type == "type2") {
					$post_excerpt = ((strlen($post->post_excerpt) > 0) ? gt3pb_smarty_modifier_truncate($post->post_excerpt, 890, "...") : gt3pb_smarty_modifier_truncate(get_the_content(), 890, "..."));
                    $compile .= '
                    <div class="bloglisting_post row type2">
                    <div class="span12">
                        <div class="date">
                            <div class="month">
                                ' . get_the_time("M") . '
                            </div>
                            <div class="day">
                                ' . get_the_time("d") . '
                            </div>
                        </div>
                    ';


                    if (strlen($featured_image[0]) > 0) {
                        $compile .= '
                        <div class="featured_image">
                            <img src="' . aq_resize($featured_image[0], 352, 397, true, true, true) . '" alt="">
                        </div>
                    ';
                    }

                    $compile .= '
                            <div class="' . (strlen($featured_image[0]) > 0 ? "with_image " : "without_image ") . (($gt3_current_page_sidebar) == "no-sidebar" ? "without_sidebar" : "with_sidebar") . ' post_preview">
                                <div class="post_preview_wrapper">
                                    <h3 class="entry-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>
                                    <div class="meta">
                                        <span><i class="icon-pencil"></i><a href="' . get_author_posts_url(get_the_author_meta('ID')) . '">' . get_the_author_meta('display_name') . '</a></span>
                                        <span><i class="icon-folder-close-alt"></i>' . trim($post_categ, ', ') . '</span>
                                        <span class="comments"><i class="icon-comment-alt"></i><a href="' . get_comments_link() . '">' . get_comments_number(get_the_ID()) . ' ' . $comments_text . '</a></span>
                                    </div>
                                    <p>' . $post_excerpt . '</p>
                                    <div class="readmore_cont">
                                    ' . do_shortcode("[custom_button style='btn_normal btn_type1' icon='' target='_self' href='" . get_permalink() . "']" . __('Read More<i class="icon-caret-right"></i>', 'gt3_builder') . "[/custom_button]") . '
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    ';

                    $compile .= '
                    </div>
                    <div class="horisontal_divider t2"></div>
                    ';
                } elseif ($blog_type == "type3") {
                    $post_excerpt = ((strlen($post->post_excerpt) > 0) ? gt3pb_smarty_modifier_truncate($post->post_excerpt, 810, "...") : gt3pb_smarty_modifier_truncate(get_the_content(), 810, "..."));
					$compile .= '
                    <div class="bloglisting_post row type3">
                    ';

                    if (strlen($featured_image[0]) > 0) {
                        $compile .= '
                        <div class="span6 featured_image">
                            <img src="' . aq_resize($featured_image[0], "560", "", true, true, true) . '" alt="">
                        </div>
                    ';
                    }

                    $compile .= '
                        <div class="' . (strlen($featured_image[0]) > 0 ? "with_image" : "without_image") . ' post_preview">
                            <h3 class="entry-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>
                            <div class="meta">
                                <span><i class="icon-pencil"></i><a href="' . get_author_posts_url(get_the_author_meta('ID')) . '">' . get_the_author_meta('display_name') . '</a></span>
                                <span><i class="icon-calendar-empty"></i>' . get_the_time("M d, Y") . '</span>
                                <span><i class="icon-comment-alt"></i><a href="' . get_comments_link() . '">' . get_comments_number(get_the_ID()) . ' ' . $comments_text . '</a></span>
                            </div>
                            ' . $post_excerpt . '
                            <div class="readmore_cont">
                            ' . do_shortcode("[custom_button style='btn_normal btn_type1' icon='icon-mail-forward' target='_self' href='" . get_permalink() . "']" . __('READ MORE', 'gt3_builder') . "[/custom_button]") . '
                            </div>
                        </div>
                    ';

                    $compile .= '
                    </div>
                    ';
                } else {
                    $compile .= '
                    <div class="bloglisting_post row">
                    ';

                    $compile .= '
                        <div class="span12 post_preview">
                            <h3 class="entry-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>
                            ' . get_pf_type_output(array("pf" => get_post_format(), "gt3_theme_pagebuilder" => $gt3_theme_pagebuilder)) . '
                            <div class="post_otput_container">
                                <div class="date">
                                    <div class="month">
                                        ' . get_the_time("M") . '
                                    </div>
                                    <div class="day">
                                        ' . get_the_time("d") . '
                                    </div>
                                </div>
                                    <p>' . ((strlen(get_the_excerpt()) > 0) ? get_the_excerpt() : get_the_content())   . '</p>
                                <div class="clear"></div>
                                <div class="readmore_cont">
                                ' . do_shortcode("[custom_button style='btn_normal btn_type1' icon='' target='_self' href='" . get_permalink() . "']" . __('Read More<i class="icon-caret-right"></i>', 'gt3_builder') . "[/custom_button]") . '
                                </div>
                                <div class="meta">
                                    <span><i class="icon-pencil"></i><a href="' . get_author_posts_url(get_the_author_meta('ID')) . '">' . get_the_author_meta('display_name') . '</a></span>
                                    <span><i class="icon-folder-close-alt"></i>' . trim($post_categ, ', ') . '</span>
                                    <span class="comments"><i class="icon-comment-alt"></i><a href="' . get_comments_link() . '">' . get_comments_number(get_the_ID()) . ' ' . $comments_text . '</a></span>
                            </div>
                            </div>
                        </div>
                    ';
                    $compile .= '
                    </div>
                    <div class="horisontal_divider"></div>
                    ';

                }

            endwhile;
			
			if ($pager_type == "type1") {
            	$compile .= gt3pb_get_plugin_pagination("10", "show_in_shortcodes");
			}

            wp_reset_query();

            return $compile;
        }

        add_shortcode($shortcodeName, 'shortcode_blog');
    }
}

#Shortcode name
$shortcodeName = "blog";
$blog = new blog_shortcode();
$blog->register_shortcode($shortcodeName);

?>