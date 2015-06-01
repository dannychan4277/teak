<?php

class gt3_sidebar {
	public function register_shortcode($shortcodeName) {
		function shortcode_gt3_sidebar($atts, $content = null) {

			extract( shortcode_atts( array(
              'heading_alignment' => 'left',
			  'heading_size' => $GLOBALS["pbconfig"]['default_heading_in_module'],
			  'heading_color' => '',
			  'heading_text' => '',
			  'widget_sidebar' => '',
			), $atts ) );

            #heading
            $compile = '';
            if (strlen($heading_color)>0) {$custom_color = "color:#{$heading_color};";}
            if (strlen($heading_text)>0) {
			    $compile .= "<div class='bg_title'><".$heading_size." style='".(isset($custom_color) ? $custom_color : '') . ((strlen($heading_alignment) > 0 && $heading_alignment !== 'left') ? 'text-align:'.$heading_alignment.';' : '')."' class='headInModule'>{$heading_text}</".$heading_size."></div>";
            }

			$compile .= get_dynamic_sidebar($widget_sidebar);

            return $compile;
		}
		add_shortcode($shortcodeName, 'shortcode_gt3_sidebar');
	}
}

#Shortcode name
$shortcodeName="gt3_sidebar";
$shortcode_gt3_sidebar = new gt3_sidebar();
$shortcode_gt3_sidebar->register_shortcode($shortcodeName);

?>