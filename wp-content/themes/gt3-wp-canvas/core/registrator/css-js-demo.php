<?php
#Only demo stuff
#gt3_update_theme_option("demo_server", "true");
#gt3_delete_theme_option("demo_server");

if (gt3_get_theme_option("demo_server") == "true") {
    if (!function_exists('gt3_css_js_demo')) {
        function gt3_css_js_demo()
        {
        }
    }
    add_action('wp_enqueue_scripts', 'gt3_css_js_demo');
}

?>