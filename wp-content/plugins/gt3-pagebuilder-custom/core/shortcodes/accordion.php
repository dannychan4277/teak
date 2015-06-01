<?php

#accordion_item
function accordion_item($atts, $content = null)
{
    global $gt3pb_acctemmpi;
    $compile = '';

    extract(shortcode_atts(array(
        'heading_alignment' => 'left',
        'heading_size' => $GLOBALS["pbconfig"]['default_heading_in_module'],
        'heading_color' => '',
        'heading_text' => '',
        'title' => '',
        'expanded_state' => '',
    ), $atts));

    $compile .= "
    <h5 data-count='" . $gt3pb_acctemmpi . "' class='shortcode_accordion_item_title acc_togg_title expanded_" . $expanded_state . "'><span>" . $title . "</span></h5>
    <div class='shortcode_accordion_item_body acc_togg_body'>
        <div class='ip'>" . $content . "</div>
    </div>
    ";

    $gt3pb_acctemmpi++;
    return $compile;
}

add_shortcode('accordion_item', 'accordion_item');


class accordion_shortcode
{
    public function register_shortcode($shortcodeName)
    {
        function shortcode_accordion($atts, $content = null)
        {
            global $gt3pb_acctemmpi;
            $compile = '';

            extract(shortcode_atts(array(
                'heading_alignment' => 'left',
                'heading_size' => $GLOBALS["pbconfig"]['default_heading_in_module'],
                'heading_color' => '',
                'heading_text' => '',
                'title' => '',
                'expanded_state' => '',
            ), $atts));

            $gt3pb_acctemmpi = 1;

            #heading
            if (strlen($heading_color) > 0) {
                $custom_color = "color:#{$heading_color};";
            }
            if (strlen($heading_text) > 0) {
                $compile .= "<div class='bg_title'><" . $heading_size . " style='" . (isset($custom_color) ? $custom_color : '') . ((strlen($heading_alignment) > 0 && $heading_alignment !== 'left') ? 'text-align:' . $heading_alignment . ';' : '') . "' class='headInModule'>{$heading_text}</" . $heading_size . "></div>";
            }

            $compile .= "<div class='shortcode_accordion accordion'>" . do_shortcode($content) . "</div>";

            $GLOBALS['showOnlyOneTimeJS']['toggles_accordion'] = "
            <script>
                jQuery(document).ready(function($) {
                    jQuery('.shortcode_accordion_item_title').click(function(){
                        if (!jQuery(this).hasClass('state-active')) {
                            jQuery(this).parents('.shortcode_accordion').find('.shortcode_accordion_item_body').slideUp('fast');
                            jQuery(this).next().slideToggle('fast');
                            jQuery(this).parents('.shortcode_accordion').find('.state-active').removeClass('state-active');
                            jQuery(this).addClass('state-active');
                        }
                    });
                    jQuery('.shortcode_toggles_item_title').click(function(){
                        jQuery(this).next().slideToggle('fast');
                        jQuery(this).toggleClass('state-active');
                    });

                    jQuery('.shortcode_accordion_item_title.expanded_yes, .shortcode_toggles_item_title.expanded_yes').each(function( index ) {
                        jQuery(this).next().slideDown('fast');
                        jQuery(this).addClass('state-active');
                    });
                });
            </script>
            ";

            return $compile;
        }

        add_shortcode($shortcodeName, 'shortcode_accordion');
    }
}

#Shortcode name
$shortcodeName = "accordion_shortcode";
$accordion_shortcode = new accordion_shortcode();
$accordion_shortcode->register_shortcode($shortcodeName);
?>