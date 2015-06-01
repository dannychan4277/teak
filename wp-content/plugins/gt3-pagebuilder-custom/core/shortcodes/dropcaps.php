<?php

class dropcaps {

	public function register_shortcode($shortcodeName) {
		function shortcode_dropcaps($atts, $content = null) {
			extract( shortcode_atts( array(
			  'class' => '',
			  'type' => '',
			), $atts ) );

			$strlen = strlen($content) - 1;
			$firstLetter = substr($content, 0, 1);

			return "<span class='dropcap ".$type."'>".$firstLetter."</span>".substr($content, 1, $strlen);
		}
		add_shortcode($shortcodeName, 'shortcode_dropcaps');  
	}
}

#Shortcode name
$shortcodeName="dropcaps";

#Compile UI for admin panel
#Don't change this line
$gt3pb_compileShortcodeUI = "<div class='whatInsert whatInsert_".$shortcodeName."'>".$gt3pb_defaultUI."</div>";

#This function is executed each time when you click "Insert" shortcode button.
$gt3pb_compileShortcodeUI .= "
<table>
	<tr>
		<td>Type:</td>
		<td>
		    <select style='' name='".$shortcodeName."_type' class='".$shortcodeName."_type'>";

            if (is_array($GLOBALS["pbconfig"]['all_available_dropcaps'])) {
                foreach ($GLOBALS["pbconfig"]['all_available_dropcaps'] as $value => $caption) {
                    $gt3pb_compileShortcodeUI .= "<option value='".$value."'>".$caption."</option>";
                }
            }

            $gt3pb_compileShortcodeUI .= "</select>
        </td>
	</tr>
</table>

<div style='float:left;clear:both;line-height:27px;'></div>


<script>
	function ".$shortcodeName."_handler() {
	
		/* YOUR CODE HERE */
		
		var type = jQuery('.".$shortcodeName."_type').val();
		
		/* END YOUR CODE */
	
		/* COMPILE SHORTCODE LINE */
		var compileline = '[".$shortcodeName." type=\"'+type+'\"][/".$shortcodeName."]';
				
		/* DO NOT CHANGE THIS LINE */
		jQuery('.whatInsert_".$shortcodeName."').html(compileline);
	}
</script>

";

#Register shortcode & set parameters
$dropcaps = new dropcaps();
$dropcaps->register_shortcode($shortcodeName);
shortcodesUI::getInstance()->add('dropcaps', array("name" => $shortcodeName, "caption" => "Dropcaps", "handler" => $gt3pb_compileShortcodeUI));

unset($gt3pb_compileShortcodeUI);


?>