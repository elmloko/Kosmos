<?php
$glt_css = get_option('googlelanguagetranslator_css');
$language_switcher_width = get_option('glt_language_switcher_width');
$language_switcher_text_color = get_option('glt_language_switcher_text_color');
$language_switcher_bg_color = get_option('glt_language_switcher_bg_color');
$glt_display = get_option('googlelanguagetranslator_display');
$floating_widget_position = get_option ('glt_floating_widget_position');
$floating_widget_text_color = get_option ('glt_floating_widget_text_color');
$floating_widget_bg_color = get_option('glt_floating_widget_bg_color');

echo '<style>';
echo $glt_css;

if(get_option('googlelanguagetranslator_flags') == 1) {
    if(get_option('googlelanguagetranslator_display') == 'Vertical') {
        echo 'p.hello{font-size:12px;color:darkgray;}';
        echo '#google_language_translator,#flags{text-align:left;}';
    } elseif(get_option('googlelanguagetranslator_display') == 'Horizontal') {
        if(get_option('googlelanguagetranslator_flags_alignment') == 'flags_right') {
            echo '#google_language_translator{text-align:left!important;}';
            echo 'select.goog-te-combo{float:right;}';
            echo '.goog-te-gadget{padding-top:13px;}';
            echo '.goog-te-gadget .goog-te-combo{margin-top:-7px!important;}';
        }

        echo '.goog-te-gadget{margin-top:2px!important;}';
        echo 'p.hello{font-size:12px;color:#666;}';
        echo 'div.skiptranslate.goog-te-gadget{display:inline!important;}';
    } elseif(get_option('googlelanguagetranslator_display') == 'SIMPLE' and get_option('googlelanguagetranslator_flags_alignment') == 'flags_right') {
        echo '.goog-te-gadget{float:right;clear:right;}';
        echo 'div.skiptranslate.goog-te-gadget{display:inline!important;}';
    }

    if(get_option('googlelanguagetranslator_flags_alignment') == 'flags_right') {
        echo '#google_language_translator,#language{clear:both;width:160px;text-align:right;}';
        echo '#language{float:right;}';
        echo '#flags{text-align:right;width:165px;float:right;clear:right;}';
        echo '#flags ul{float:right!important;}';
        echo 'p.hello{text-align:right;float:right;clear:both;}';
        echo '.glt-clear{height:0px;clear:both;margin:0px;padding:0px;}';
    }

    if(get_option('googlelanguagetranslator_flags_alignment') == 'flags_left') {
        echo '#google_language_translator{clear:both;}';
        echo '#flags{width:165px;}';
        echo '#flags a{display:inline-block;margin-right:2px;}';
    } elseif(get_option('googlelanguagetranslator_flags_alignment') == 'flags_right') {
        echo '#flags{width:165px;}';
        echo '#flags a{display:inline-block;margin-left:2px;}';
    }
}

if(get_option('googlelanguagetranslator_active') == 1) {
    if(get_option('googlelanguagetranslator_showbranding') == 'Yes') {
        echo '#google_language_translator{width:auto!important;}';
        echo 'div.skiptranslate.goog-te-gadget{display:inline!important;}';
    } elseif(get_option('googlelanguagetranslator_showbranding') == 'No' and get_option('googlelanguagetranslator_display') != 'SIMPLE') {
        echo '#google_language_translator a{display:none!important;}';
        echo 'div.skiptranslate.goog-te-gadget{display:inline!important;}';
        echo '.goog-te-gadget{color:transparent!important;}';
        echo '.goog-te-gadget{font-size:0px!important;}';
        echo '.goog-branding{display:none;}';
    }

    echo '.goog-tooltip{display: none!important;}';
    echo '.goog-tooltip:hover{display: none!important;}';
    echo '.goog-text-highlight{background-color:transparent!important;border:none!important;box-shadow:none!important;}';

    if(get_option('googlelanguagetranslator_translatebox') == 'no')
        echo '#google_language_translator{display:none;}';
}

if(!empty($language_switcher_text_color))
  echo '#google_language_translator select.goog-te-combo{color:'.$language_switcher_text_color.';}';

if(get_option('googlelanguagetranslator_flags') == 0 and get_option('googlelanguagetranslator_active') == 1)
    echo '#flags{display:none;}';

if(get_option('googlelanguagetranslator_active') == 1) {
    if(get_option('googlelanguagetranslator_toolbar') == 'Yes'){
        echo '#google_language_translator{color:transparent;}';
    } elseif(get_option('googlelanguagetranslator_toolbar') == 'No') {
        echo 'div.skiptranslate{display:none!important;}';
    }
    echo 'body{top:0px!important;}';
    echo '#goog-gt-{display:none!important;}';
}

if($floating_widget_position == 'bottom_left') {
    echo '#glt-translate-trigger{left:20px;right:auto;}';
} elseif($floating_widget_position == 'top_right') {
    echo '#glt-translate-trigger{bottom:auto;top:0;}';
    echo '.tool-container.tool-top{top:50px!important;bottom:auto!important;}';
    echo '.tool-container.tool-top .arrow{border-color:transparent transparent #d0cbcb; top:-14px;}';
} elseif ($floating_widget_position == 'top_left') {
    echo '#glt-translate-trigger{bottom:auto;top:0;left:20px;right:auto;}';
    echo '.tool-container.tool-top{top:50px!important;bottom:auto!important;}';
    echo '.tool-container.tool-top .arrow{border-color:transparent transparent #d0cbcb;top:-14px;}';
} elseif ($floating_widget_position == 'top_center') {
    echo '#glt-translate-trigger{bottom:auto;top:0;left:50%;margin-left:-63px;right:auto;}';
    echo '.tool-container.tool-top{top:50px!important;bottom:auto!important;}';
    echo '.tool-container.tool-top .arrow{border-color:transparent transparent #d0cbcb;top:-14px;}';
} elseif ($floating_widget_position == 'bottom_center') {
    echo '#glt-translate-trigger{left:50%; margin-left:-63px; right:auto;}';
}

if(!empty($floating_widget_text_color))
    echo '#glt-translate-trigger > span{color:'.$floating_widget_text_color.';}';

if(!empty($floating_widget_bg_color))
    echo '#glt-translate-trigger{background:'.$floating_widget_bg_color.';}';

if(!empty($language_switcher_width) and isset($language_switcher_width) and $glt_display != 'Horizontal')
    echo '.goog-te-gadget .goog-te-combo{width:'.$language_switcher_width.';}';

if(!empty($language_switcher_bg_color) and isset($language_switcher_bg_color))
    echo '#google_language_translator .goog-te-gadget .goog-te-combo{background:'.$language_switcher_bg_color.';border:0!important;}';

echo '</style>';