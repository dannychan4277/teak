<?php

class portfolio_masonry_shortcode
{
    public function register_shortcode($shortcodeName)
    {
        function shortcode_portfolio_masonry($atts, $content = null)
        {
            if (!isset($compile)) {$compile='';}

            extract(shortcode_atts(array(
                'heading_alignment' => 'left',
                'heading_size' => $GLOBALS["pbconfig"]['default_heading_in_module'],
                'heading_color' => '',
                'heading_text' => '',
                'posts_per_page' => '4',
                'filter' => 'off',
                'selected_categories' => '',
            ), $atts));

            #heading
            if (strlen($heading_color) > 0) {
                $custom_color = "color:#{$heading_color};";
            }
            if (strlen($heading_text) > 0) {
                $compile .= "<div class='bg_title'><" . $heading_size . " style='" . $custom_color . ((strlen($heading_alignment) > 0 && $heading_alignment !== 'left') ? 'text-align:' . $heading_alignment . ';' : '') . "' class='headInModule'>{$heading_text}</" . $heading_size . "></div>";
            }

            $post_type_terms = array();
            if (strlen($selected_categories) > 0) {
                $post_type_terms = explode(",", $selected_categories);
            }

            wp_enqueue_script('js_isotope', get_template_directory_uri() . '/js/jquery.isotope.min.js', array(), false, false);
            wp_enqueue_script('js_sorting', get_template_directory_uri() . '/js/sorting.js');

            #Filter
            if ($filter == "on") {
                $compile .= gt3pb_showPortCatsMasonry($post_type_terms);
            }
			
			$GLOBALS['showOnlyOneTimeJS']['fw_portfolio_block'] = "
            <script>
                function fw_portf_block() {
                    if (jQuery('div').hasClass('right-sidebar') || jQuery('div').hasClass('left-sidebar')) {} else {
                        var fw_portf_block = jQuery('.masonry_portfolio_block');
                        var fw_portf_block_parent = fw_portf_block.parent().width();
                        var fw_site_width_port = jQuery(window).width();
                        var fw_portfolio_site_width_diff = fw_site_width_port - fw_portf_block_parent;
                        fw_portf_block.css('margin-left', '-'+fw_portfolio_site_width_diff/2+'px').css('width', fw_site_width_port+'px');
                    }	
                }
                jQuery(document).ready(function() {
                    fw_portf_block();		
					
					jQuery('.portfolio_preview_zoom').click(function(){
						var set_img = jQuery(this).parents('.featured_item_fadder').find('img').attr('src');
						var set_title = jQuery(this).parents('.masonry_pf_item').find('.masonry_pf_title').text();
						var set_preview_meta = jQuery(this).parents('.masonry_pf_item').find('.preview_meta').html();
						var set_text = jQuery(this).parents('.masonry_pf_item').find('.masonry_pf_excerpt').html();
						var set_url = jQuery(this).attr('data-url');
						jQuery('html, body').stop().animate({scrollTop: jQuery('.portfolio_preview_wrapper').offset().top-jQuery('.menu_fixed').height()-50}, 600);
						jQuery('.portfolio_preview_wrapper').animate({'opacity' : '0'}, 500, function(){
							jQuery(this).empty();
							jQuery(this).parents('.masonry_portfolio_preview').height(0);
							jQuery(this).append('<div class=\"portfolio_item\"><div class=\"portfolio_item_img\"><img src=\"'+set_img+'\"></div><div class=\"portfolio_dscr blog_post_preview\"><div class=\"preview_wrapper\"><div class=\"preview_topblock\"><h3 class=\"blogpost_title\"><a href=\"'+set_url+'\">'+set_title+'</a></h3><div class=\"preview_meta\">'+set_preview_meta+'</div><article class=\"contentarea\">'+set_text+' <a href=\"'+set_url+'\" class=\"read_more\">Read more</a></article></div></div></div><div class=\"clear\"></div></div>');
							jQuery(this).parents('.masonry_portfolio_preview').height(jQuery(this).height()+10);
							jQuery(this).animate({'opacity' : '1'}, 500);
						});
					});
					jQuery('.pf_preview_close').click(function(){
						jQuery(this).parents('.masonry_portfolio_preview').height(0);
						jQuery('.portfolio_preview_wrapper').animate({'opacity' : '0'}, 500, function(){
							jQuery(this).empty();
						});	
					});
								
                });
                jQuery(window).resize(function(){
                    fw_portf_block();
                });
            </script>
            ";
			
			$compile .= '<div class="masonry_portfolio_preview"><a href="'.esc_js("javascript:void(0)").'" class="pf_preview_close"></a><div class="portfolio_preview_wrapper">fff</div></div>';

            $compile .= '<div class="masonry_portfolio_block image-grid isotope" id="list">';
            global $gt3pb_wp_query_in_shortcodes;
            $gt3pb_wp_query_in_shortcodes = new WP_Query();
            global $paged;
            $args = array(
                'post_type' => 'port',
                'order' => 'DESC',
                'paged' => $paged,
                'posts_per_page' => $posts_per_page,
            );

            if (isset($_GET['slug']) && strlen($_GET['slug']) > 0) {
                $post_type_terms = $_GET['slug'];
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

            $gt3pb_wp_query_in_shortcodes->query($args);

            $i = 1;

            while ($gt3pb_wp_query_in_shortcodes->have_posts()) : $gt3pb_wp_query_in_shortcodes->the_post();

                $pf = get_post_format();
                if (empty($pf)) $pf = "text";
                $gt3_theme_pagebuilder = gt3pb_get_plugin_pagebuilder(get_the_ID());

                $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_id()), 'single-post-thumbnail');
                if (strlen($featured_image[0]) < 1) {
                    $featured_image[0] = "";
                }

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

                #Portfolio 1
                    $port_content_show = ((strlen(get_the_excerpt()) > 0) ? get_the_excerpt() : gt3pb_smarty_modifier_truncate(get_the_content(), 470));

                    $compile .= '
						<div data-category="' . $echoallterm . '" class="' . $echoallterm . ' element masonry_pf_item">
							<div class="portfolio_item_wrapper">
								<div class="img_block wrapped_img">
								    <div class="featured_item_fadder">
								        <img src="' . aq_resize($featured_image[0], "700", "525", true, true, true) . '" alt="" width="570">
								        <div class="item_info">
								            <a class="featured_ico_link portfolio_preview_zoom" href="'.esc_js("javascript:void(0)").'" data-url="' . get_permalink() . '"></a>
                                        </div>
                                        <h5 class="masonry_pf_title">
                                            <a href="' . get_permalink() . '">' . get_the_title() . '</a>
                                        </h5>
                                    </div>
                                </div>
                            </div>
								
								<div class="preview_meta dn">
									<span class="preview_meta_author">
										by <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '">' . get_the_author_meta('display_name') . '</a>
									</span>
									<span class="preview_categ">in ' . trim($tempname, ', ') . '</span>';
									if (isset($gt3_theme_pagebuilder['page_settings']['portfolio']['skills']) && is_array($gt3_theme_pagebuilder['page_settings']['portfolio']['skills'])) {
										foreach ($gt3_theme_pagebuilder['page_settings']['portfolio']['skills'] as $skillkey => $skillvalue) {
											$compile .= '<span class="preview_skills">' . esc_attr($skillvalue['name']) . ': ';
											$compile .= esc_attr($skillvalue['value']) . '</span>';
										}
									}
								
							$compile .= '
								</div>	
								<div class="portfolio_content dn">
									<span class="masonry_pf_excerpt">'. ((strlen(get_the_excerpt()) > 0) ? get_the_excerpt() : gt3pb_smarty_modifier_truncate(get_the_content(), 300)) .'</span>
								</div>
								
						</div>
						';
                $i++;
                unset($echoallterm, $pf);
            endwhile;

            $compile .= '<div class="clear"></div></div>';

            wp_reset_query();
            return $compile;
        }

        add_shortcode($shortcodeName, 'shortcode_portfolio_masonry');
    }
}

#Shortcode name
$shortcodeName = "portfolio_masonry";
$portfolio = new portfolio_masonry_shortcode();
$portfolio->register_shortcode($shortcodeName);
?>