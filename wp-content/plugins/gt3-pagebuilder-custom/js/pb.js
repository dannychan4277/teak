jQuery.fn.tinymce_textareas = function (selector) {
};

function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

/* FIX FOR ALL OTHER ADMIN PAGES */
if (post_id < 1) {
    var post_id = 0;
}

function waiting_state_start() {
    jQuery(".waiting-bg").show();
}

function waiting_state_end() {
    jQuery(".waiting-bg").hide();
}

/* AUTO HEIGHT FOR POPUP */
function popupAutoH() {
    nowWinH = jQuery(window).height();
    popupH = nowWinH - 150;
    jQuery(".pop_scrollable_area").height(popupH);
}

function reactivate_color_picker() {
    /* REACTIVATE COLOR PICKER */
    jQuery('.cpicker').ColorPicker({
        onSubmit: function (hsb, hex, rgb, el) {
            jQuery(el).val(hex);
            jQuery(el).ColorPickerHide();
            jQuery(".cpicker.focused").next().css("background-color", "#" + hex);
        },
        onBeforeShow: function () {
            jQuery(this).ColorPickerSetColor(this.value);
        },
        onHide: function () {
            jQuery("input").removeClass("focused");
        },
        onChange: function (hsb, hex, rgb) {
            jQuery(".cpicker.focused").val(hex);
            jQuery(".cpicker.focused").next().css("background-color", "#" + hex);
        }
    })
        .bind('keyup', function () {
            jQuery(this).ColorPickerSetColor(this.value);
        });

    jQuery('.cpicker').focus(function () {
        jQuery(this).addClass("focused");
    });
}


function reactivate_sortable() {
    jQuery('.sections').sortable({placeholder: 'ui-state-highlight-sections', handle: '.some-element.move'});
    jQuery('.feature-list').sortable({placeholder: 'ui-state-highlight-sections', handle: '.some-element2.move'});
    jQuery('.sortable_icons_list').sortable();
}

function reactivate_selectbox() {
    jQuery(".mix-container select, .newselect, .shortcodebox_content select").selectbox();
}

/* SHOW / HIDE content */
jQuery(document).ready(function () {
    jQuery(document).on('click', '.show-hide-container', function () {
        jQuery(this).parents('.pb-cont').find('.hideable-content').toggle('fast');
    });
});


/* COLORBOX */
jQuery(document).ready(function () {
    /* ADD IMAGE TO AVAILABLE MEDIA */
    jQuery('.add_image_to_sliders_available_media').colorbox({
        href: 'media-upload.php?post_id=' + post_id + '&type=image&pg=gallery',
        iframe: true,
        innerWidth: 660,
        innerHeight: 500,
        onClosed: function () {
            jQuery.post(ajaxurl, {
                action: 'gt3pb_get_media_for_postid',
                post_id: post_id,
                page: 1
            }, function (data) {
                jQuery('.available_media .ajax_cont').html(data);
            }, 'text');
        }
    });
    jQuery('body').append('<div class="shortcodebox_fadder shortcodebox_hided"></div><div class="shortcodebox shortcodebox_hided" style="height:' + (jQuery(window).height() - 200) + 'px"><div class="shortcodebox_title"><h2>Shortcodes</h2><span class="shortcodebox_close"></span></div><div class="shortcodebox_content" style="height:' + (jQuery(window).height() - 290) + 'px"></div></div>');
    jQuery('.shortcodebox_fadder').fadeOut(1, function () {
        jQuery(this).removeClass('shortcodebox_hided');
    });
    jQuery('.shortcodebox').fadeOut(1, function () {
        jQuery(this).removeClass('shortcodebox_hided');
    });
    jQuery('.shortcodebox_close').click(function () {
        jQuery('.shortcodebox_fadder').fadeOut(300);
        jQuery('.shortcodebox').fadeOut(300);
        shortcodes_costyl = true;
    });
    jQuery('.shortcodebox_fadder').click(function () {
        jQuery('.shortcodebox_fadder').fadeOut(300);
        jQuery('.shortcodebox').fadeOut(300);
        shortcodes_costyl = true;
    });
});

jQuery(document).ready(function () {
    jQuery(".mix-container select, .newselect").selectbox();

    jQuery(document).on('click', '.available_media_arrow', function () {
        if (jQuery(this).hasClass("left_arrow")) {
            show_img_media_library_page = show_img_media_library_page - 1;
        }
        if (jQuery(this).hasClass("right_arrow")) {
            show_img_media_library_page = show_img_media_library_page + 1;
        }

        if (show_img_media_library_page < 1) {
            show_img_media_library_page = 1;
        }

        jQuery.post(ajaxurl, {
            action: 'gt3pb_get_media_for_postid',
            post_id: post_id,
            page: show_img_media_library_page
        }, function (data) {
            if (data !== "no_items") {
                jQuery('.available_media .ajax_cont').html(data);
            } else {
                show_img_media_library_page = show_img_media_library_page - 1;
            }
        }, 'text');
    });

    jQuery(window).resize(function () {
        jQuery('.shortcodebox').height(jQuery(window).height() - 200);
        jQuery('.shortcodebox_content').height(jQuery(window).height() - 290);
    });
    /*TipTip*/
    jQuery(".tiptip").tipTip({maxWidth: "190px", edgeOffset: 0, defaultPosition: "top", delay: "0"});
    tiny_present = false;
    if (jQuery('.tinyCont').size() > 0 || jQuery('.tinytextarea').size() > 0) {
        tiny_present = true;
    }

    if (typeof(tinyMCE) == "undefined" || tiny_present == false) {
        our_tiny_firstrun = true;
    } else {
        run_Tiny();
    }
});

function run_Tiny() {
    if (typeof(tinyMCE) == "undefined") {
    } else {
        tinymce.PluginManager.add('gt3_code', function (editor) {
            function gt3_showDialog() {
                editor.windowManager.open({
                    title: "Source code",
                    body: {
                        type: 'textbox',
                        name: 'code',
                        multiline: true,
                        minWidth: editor.getParam("code_dialog_width", 600),
                        minHeight: editor.getParam("code_dialog_height", Math.min(tinymce.DOM.getViewPort().h - 200, 500)),
                        value: editor.getContent({source_view: true}),
                        spellcheck: false,
                        style: 'direction: ltr; text-align: left'
                    },
                    onSubmit: function (e) {
                        editor.focus();

                        editor.undoManager.transact(function () {
                            editor.setContent(e.data.code);
                        });

                        editor.selection.setCursorLocation();
                        editor.nodeChanged();
                    }
                });
            }

            editor.addCommand("mceCodeEditor", gt3_showDialog);

            editor.addButton('gt3_code', {
                icon: 'code',
                tooltip: 'Source code',
                onclick: gt3_showDialog
            });

            editor.addMenuItem('gt3_code', {
                icon: 'code',
                text: 'Source code',
                context: 'tools',
                onclick: gt3_showDialog
            });
        });

        our_tiny_firstrun = false;
        tinyMCE.init({
            menubar: false,
            plugins: "textcolor image wplink gt3_code",
            toolbar: "bold italic underline alignleft aligncenter alignright alignjustify blockquote bullist numlist undo redo link unlink image hr removeformat forecolor backcolor pbshortcodes gt3_code",
            setup: function (ed, shortcodes_costyl) {
                ed.addButton('pbshortcodes', {
                    title: 'Shortcodes',
                    image: GT3PBPLUGINROOTURL + 'js/shortcode.png',
                    onclick: function () {
                        jQuery(function ($) {
                            console.log('sgh');
                            jQuery('.shortcodebox_fadder').fadeIn(500);
                            jQuery('.shortcodebox').fadeIn(500);
                            shortcodes_costyl = false;
                        });

                        jQuery.post(ajaxurl, {action: 'getshortcodesUI'}, function (response) {
                            jQuery('.shortcodebox .shortcodebox_content').html(response);
                            reactivate_selectbox();
                            reactivate_color_picker();
                        });

                        if (shortcodes_costyl == false) {
                            jQuery(document).on("click", ".insertshortcode", function () {
                                var thisShortCodeName = jQuery(this).parents(".shortcodeitem").attr("shortcodename");

                                /* exec shortcode compiller */
                                handlerName = thisShortCodeName + "_handler";
                                var thisHandler = window[handlerName];
                                thisHandler();

                                jQuery('.shortcodebox').fadeOut(500);
                                jQuery('.shortcodebox_fadder').fadeOut(500);

                                var whatInsert = jQuery(this).parents(".shortcodeitem").find(".whatInsert").html();
                                if (shortcodes_costyl == false) {
                                    ed.execCommand('mceInsertContent', false, whatInsert);
                                    shortcodes_costyl = true;
                                }
                            });

                            jQuery(document).on("click", ".shortcodebox_close", function () {
                                var whatInsert = "";
                                if (shortcodes_costyl == false) {
                                    ed.execCommand('mceInsertContent', false, whatInsert);
                                    shortcodes_costyl = true;
                                }
                            });

                            jQuery(document).on("click", ".shortcodebox_fadder", function () {
                                var whatInsert = "";
                                if (shortcodes_costyl == false) {
                                    ed.execCommand('mceInsertContent', false, whatInsert);
                                    shortcodes_costyl = true;
                                }
                            });

                            jQuery(document).on("change", ".select_shortcode", function () {
                                var nowSelect = jQuery(this).val();
                                jQuery(".shortcodeitem").hide();
                                jQuery("." + nowSelect).show();
                            });
                        }
                    }
                });
            },
            skin: "lightgray",
            selector: ".tinyCont textarea, .tinytextarea",
            relative_urls: false,
            width: "100%",
            language: "",
            height: 500
        });
    }
}

function check_visual_part_for_toggles() {
    jQuery(".radio_toggle_cont").each(function (index) {
        var yes_state = jQuery(this).find('.yes_state').attr('checked');
        var no_state = jQuery(this).find('.no_state').attr('checked');

        if (yes_state == 'checked') {
            //alert("yes");
            jQuery(this).find(".no_state").removeAttr("checked");
            jQuery(this).find(".radio_toggle_mirage").removeClass("not_checked").addClass("checked");
            jQuery(this).find(".radio_toggle_mirage").stop().animate({backgroundPosition: '0% 0%'}, {duration: 'fast'});
        } else {
            //alert("no");
            jQuery(this).find(".yes_state").removeAttr("checked");
            jQuery(this).find(".radio_toggle_mirage").removeClass("checked").addClass("not_checked");
            jQuery(this).find(".radio_toggle_mirage").stop().animate({backgroundPosition: '100% 0%'}, {duration: 'fast'});
        }

    });
}

/* Document ready for all elements */
jQuery(document).ready(function () {

    /* SLIDER */
    function resetSliderAtts(li_for_this_slider) {

        jQuery(li_for_this_slider).each(function (index) {
            jQuery(this).find(".itemorder").val(index);
        });
    }

    jQuery(".sortable").sortable({
        out: function (event, ui) {
            var li_for_this_slider = jQuery(this).find("li");
            resetSliderAtts(li_for_this_slider);
        },
        create: function (event, ui) {
            var li_for_this_slider = jQuery(this).find("li");
            resetSliderAtts(li_for_this_slider);
        },
        delay: 200
    });

    jQuery(document).on('click', '.img-item .inter_x', function () {
        jQuery(this).parents(".img-item").hide('fast', function () {
            jQuery(this).remove();
        });
    });

    jQuery(document).on('click', '.add_new_port_skills', function () {
        var skill_rand = getRandomInt(0, 99999);
        jQuery(this).parents(".port_skills_cont").find(".all_added_skills").append("<li class='stand_iconsweet ui-state-default new_added' style='display:none'> <input type='text' class='itt_type1 ww10 select_icon_and_insert_here' name='pagebuilder[page_settings][portfolio][skills][" + skill_rand + "][icon]' placeholder='Icon' value=''> <input type='text' class='itt_type1 ww43' name='pagebuilder[page_settings][portfolio][skills][" + skill_rand + "][name]' placeholder='Field name' value=''> <input type='text' class='itt_type1 ww43' name='pagebuilder[page_settings][portfolio][skills][" + skill_rand + "][value]' placeholder='Field value' value=''> <span class='remove_skill'><i class='stand_icon icon-remove'></i></span></li>");
        jQuery('.new_added').slideDown(300).removeClass('.new_added');
    });

    jQuery(document).on('click', '.remove_skill', function () {
        jQuery(this).parents("li").slideUp(300, function () {
            jQuery(this).remove();
        });
    });


    /* add image in slider */
    jQuery(document).on('click', '.slider_type .available_media_item', function () {
        var available_media_item_this_attach_id = jQuery(this).find('.previmg').attr('data-attach-id');
        var available_media_item_this_thumburl = jQuery(this).find('.previmg').attr('data-thumb-url');
        var parent_root = jQuery(this).parents('.bg_or_slider_option');
        var settings_type = jQuery(this).parents('.bg_or_slider_option').find('.settings_type').val();

        var data = {
            action: 'get_unused_id_ajax'
        };

        waiting_state_start();

        jQuery.post(ajaxurl, data, function (response) {
            parent_root.find(".selected_media .append_block .sortable-img-items").append('<li><div class="img-item item-with-settings append_animation"><input type="hidden" name="pagebuilder[sliders][' + settings_type + '][slides][' + response + '][attach_id]" value="' + available_media_item_this_attach_id + '"><input type="hidden" name="pagebuilder[sliders][' + settings_type + '][slides][' + response + '][slide_type]" value="image"><div class="img-preview"><img src="' + available_media_item_this_thumburl + '" alt=""><div class="hover-container"><div class="inter_x"></div><div class="inter_drag"></div><div class="inter_edit"></div></div></div><div class="edit_popup"><h2>Image Settings</h2><span class="edit_popup_close"></span><div class="this-option img-in-slider"><div class="padding-cont"><div class="fl w9"><h4>Title</h4><input name="pagebuilder[sliders][' + settings_type + '][slides][' + response + '][title][value]" type="text" value="" class="textoption type1"></div><div class="right_block fl w1"><h4>color</h4><div class="color_picker_block"><span class="sharp">#</span><input type="text" value="" name="pagebuilder[sliders][' + settings_type + '][slides][' + response + '][title][color]" maxlength="25" class="medium cpicker textoption type1"><input type="text" value="" class="textoption type1 cpicker_preview" disabled="disabled"></div></div><div class="clear"></div></div><div class="hr_double"></div><div class="padding-cont"><div class="fl w9"><h4>Caption</h4><textarea name="pagebuilder[sliders][' + settings_type + '][slides][' + response + '][caption][value]" type="text" class="textoption type1 big"></textarea></div><div class="right_block fl w1"><h4>color</h4><div class="color_picker_block"><span class="sharp">#</span><input type="text" value="" name="pagebuilder[sliders][' + settings_type + '][slides][' + response + '][caption][color]" maxlength="25" class="medium cpicker textoption type1"><input type="text" value="" class="textoption type1 cpicker_preview" disabled="disabled"></div></div><div class="clear"></div></div></div><div class="padding-cont"><input type="button" value="Done" class="done-btn green-btn" name="ignore_this_button"><div class="clear"></div></div></div></div></li>');
            jQuery('.img-item.append_animation').fadeIn('fast');
            setTimeout("jQuery('.img-item.append_animation').removeClass('append_animation')", 200);
            reactivate_selectbox();
            /* REACTIVATE COLOR PICKER */
            jQuery('.cpicker').ColorPicker({
                onSubmit: function (hsb, hex, rgb, el) {
                    jQuery(el).val(hex);
                    jQuery(el).ColorPickerHide();
                    jQuery(".cpicker.focused").next().css("background-color", "#" + hex);
                },
                onBeforeShow: function () {
                    jQuery(this).ColorPickerSetColor(this.value);
                },
                onHide: function () {
                    jQuery("input").removeClass("focused");
                },
                onChange: function (hsb, hex, rgb) {
                    jQuery(".cpicker.focused").val(hex);
                    jQuery(".cpicker.focused").next().css("background-color", "#" + hex);
                }
            })
                .bind('keyup', function () {
                    jQuery(this).ColorPickerSetColor(this.value);
                });

            jQuery('.cpicker').focus(function () {
                jQuery(this).addClass("focused");
            });

            waiting_state_end();
        });
    });

    /* add video in slider */
    jQuery(document).on('click', '.slider_type .add_video_slider', function () {
        var available_media_item_this_url = jQuery(this).find('.previmg').attr('data-full-url');
        var parent_root = jQuery(this).parents('.bg_or_slider_option');
        var settings_type = jQuery(this).parents('.bg_or_slider_option').find('.settings_type').val();

        var data = {
            action: 'get_unused_id_ajax'
        };

        waiting_state_start();

        jQuery.post(ajaxurl, data, function (response) {
            parent_root.find(".selected_media .append_block .sortable-img-items").append('<li><div class="img-item item-with-settings append_animation"><input type="hidden" name="pagebuilder[sliders][' + settings_type + '][slides][' + response + '][src]" value=""><input type="hidden" name="pagebuilder[sliders][' + settings_type + '][slides][' + response + '][slide_type]" value="video"><div class="img-preview"><img src="' + available_media_item_this_url + '" alt=""><div class="hover-container"><div class="inter_x"></div><div class="inter_drag"></div><div class="inter_edit"></div></div></div><div class="edit_popup"><h2>Video settings</h2><span class="edit_popup_close"></span><div class="this-option"><div class="padding-cont"><h4>Video URL (Vimeo or YouTube)</h4><input name="pagebuilder[sliders][' + settings_type + '][slides][' + response + '][src]" type="text" value="" class="textoption type1"><div class="example">Examples:<br>Youtube - http://www.youtube.com/watch?v=YW8p8JO2hQw<br>Vimeo - http://vimeo.com/47989207</div></div><div class="padding-cont" style="padding-top:0;"><div class="fl w9" style="width:601px;"><h4>Title and thumbnail</h4><input name="pagebuilder[sliders][' + settings_type + '][slides][' + response + '][title][value]" type="text" value="" class="textoption type1"></div><div class="right_block fl w1" style="width:115px;"><h4>color</h4><div class="color_picker_block"><span class="sharp">#</span><input type="text" value="" name="pagebuilder[sliders][' + settings_type + '][slides][' + response + '][title][color]" maxlength="25" class="medium cpicker textoption type1"><input type="text" value="" class="textoption type1 cpicker_preview" disabled="disabled"></div></div><div class="preview_img_video_cont"><div class="select_image_root"><input type="hidden" name="pagebuilder[sliders][' + settings_type + '][slides][' + response + '][attach_id]" value="" class="select_img_attachid"><div class="select_img_preview"></div><input type="button" class="button button-secondary button-large select_attach_id_from_media_library" value="Select"></div></div><div class="clear"></div></div><div class="hr_double"></div><div class="padding-cont"><div class="fl w9" style="width:601px;"><h4>Caption</h4><textarea name="pagebuilder[sliders][' + settings_type + '][slides][' + response + '][caption][value]" type="text" class="textoption type1 big" style="height:70px;"></textarea></div><div class="right_block fl w1" style="width:115px;"><h4>color</h4><div class="color_picker_block"><span class="sharp">#</span><input type="text" value="" name="pagebuilder[sliders][' + settings_type + '][slides][' + response + '][caption][color]" maxlength="25" class="medium cpicker textoption type1"><input type="text" value="" class="textoption type1 cpicker_preview" disabled="disabled"></div></div><div class="clear"></div></div></div><div class="hr_double"></div><div class="padding-cont"><input type="button" value="Done" class="done-btn green-btn" name="ignore_this_button"><div class="clear"></div></div></div></div></li>');
            reactivate_color_picker();
            reactivate_selectbox();
            reactivate_ajax_image_upload();
            jQuery('.img-item.append_animation').fadeIn('fast');
            setTimeout("jQuery('.img-item.append_animation').removeClass('append_animation')", 200);
            waiting_state_end();
        });
    });


    /* SHOW/HIDE CONTAINER (SLIDER TYPE) */
    jQuery(document).on('click', '.pb-cont .line_option .toggler .radio_toggle_mirage', function () {
        jQuery(this).parents('.bg_or_slider_option').find('.hideable-area').toggle('fast');
    });
    jQuery(document).on('click', '.edit_popup_close', function () {
        close_settings_popup();
    });


    /* OPEN POPUP EDIT */
    function show_settings_popup(thisTrigger, popupContainerClass) {
        popupAutoH();
        if (typeof popupContainerClass != 'undefined') {
            var edit_popup_area = jQuery("." + popupContainerClass);
            var gt3_popup_initialed = thisTrigger;
        } else {
            var edit_popup_area = thisTrigger.parents('.item-with-settings').find('.edit_popup');
        }
        edit_popup_area.fadeToggle('fast').addClass('nowOpen');
        jQuery('.popup-bg').fadeIn('fast');
        var pop_width = jQuery('.edit_popup.nowOpen').width();
        var pop_height = jQuery('.edit_popup.nowOpen').height();
        var offset_width = pop_width / 2;
        var offset_height = pop_height / 2;
        jQuery('.edit_popup.nowOpen').css('marginLeft', '-' + offset_width + 'px');
        jQuery('.edit_popup.nowOpen').css('marginTop', '-' + offset_height + 'px');

        if (jQuery('.edit_popup.nowOpen').find('.tinyCont').size() > 0) {
            jQuery('.edit_popup.nowOpen').find('.tinyCont').each(function () {
                set_id = jQuery(this).find('textarea').attr('id')
                tinymce.execCommand('mceAddEditor', false, set_id);
            });
        }
        thisTrigger.addClass("popup_open_from_here");
    }

    jQuery(document).on('click', '.all_available_port_skills_icons i', function () {
        jQuery('.popup_open_from_here').val(jQuery(this).attr('class'));
        close_settings_popup();
    });

    /* CLOSE POPUP EDIT */
    function close_settings_popup() {
        if (jQuery('.edit_popup.nowOpen').find('.tinyCont').size() > 0) {
            jQuery('.edit_popup.nowOpen').find('.tinyCont').each(function () {
                set_id = jQuery(this).find('textarea').attr('id')
                tinymce.execCommand('mceRemoveControl', true, set_id);
            });
        }
        jQuery('.edit_popup.nowOpen').fadeOut('fast');
        jQuery('.popup-bg').fadeOut('fast');
        setTimeout("jQuery('.edit_popup.nowOpen').css('marginLeft', '0px').css('marginTop', '0px').removeClass('nowOpen')", 300);
        jQuery('.popup_open_from_here').removeClass('popup_open_from_here');
    }

    jQuery(document).on('click', '.inter_edit, .module-cont .edit.box-with-icon .control-element', function () {
        show_settings_popup(jQuery(this));
    });

    jQuery(document).on('click', '.popup-bg, .done-btn', function () {
        close_settings_popup();
    });

    jQuery('.sortable-img-items').sortable({placeholder: 'ui-state-highlight', handle: '.inter_drag'});
    jQuery('.sortable-modules').sortable({placeholder: 'ui-state-highlight', handle: '.dragger'});
    jQuery('.sections').sortable({placeholder: 'ui-state-highlight-sections', handle: '.some-element.move'});
    jQuery('.feature-list').sortable({placeholder: 'ui-state-highlight-sections', handle: '.some-element2.move'});

    /* Click & add img to background container */
    jQuery(document).on('click', '.bg_or_slider_option.bg_type .available_media_item', function () {
        var for_bg_data_full_url = jQuery(this).find('.previmg').attr('data-full-url');
        var for_bg_data_preview_bg_image = jQuery(this).find('.previmg').attr('data-thumb-url');
        jQuery('.bg_or_slider_option.bg_type .preview_bg_image').fadeOut('fast', function () {
            jQuery('.bg_or_slider_option.bg_type .preview_bg_image').delay(200).attr('src', for_bg_data_preview_bg_image).fadeIn('fast');
        });
        jQuery('.bg_or_slider_option.bg_type .bg_image_src').val(for_bg_data_full_url);
    });

    jQuery(document).on("click", ".select_icon_and_insert_here", function () {
        show_settings_popup(jQuery(this), "all_available_port_skills_icons");
    });

    /* VISIBLE BLOCKS LOGIC FOR BG & SLIDER SETTINGS */
    function closeToggles(toggleClass) {
        jQuery.each(toggleClass, function () {
            jQuery('.radio_toggle_cont.' + this + '').find('.yes_state').removeAttr('checked');
            jQuery('.radio_toggle_cont.' + this + '').find('.no_state').attr('checked', 'checked');
            jQuery('.radio_toggle_cont.' + this + '').find('.radio_toggle_mirage').removeClass('checked').addClass('not_checked');
            jQuery('.radio_toggle_cont.' + this + '').parents('.bg_or_slider_option').find('.hideable-area').hide('fast');
            jQuery('.radio_toggle_cont.' + this + '').find('.radio_toggle_mirage').removeClass("checked").addClass("not_checked");
            jQuery('.radio_toggle_cont.' + this + '').find('.radio_toggle_mirage').stop().animate({backgroundPosition: '100% 0%'}, {duration: 'fast'});
        });
    }

    jQuery(document).on('click', '.bg_slide_sett', function () {
        /* WORK ONLY IF WE OPEN SOME TOGGLER */
        if (jQuery(this).find('.yes_state').attr('checked')) {
            /* fullscreen_toggler */
            if (jQuery(this).hasClass('fullscreen_toggler')) {
                var click_on = 'fullscreen_toggler';
                hide_items = new Array("fullwidth_toggler", "bgimage_toggler", "bgcolor_toggler");
                closeToggles(hide_items);
            }
            /* fullwidth_toggler */
            if (jQuery(this).hasClass('fullwidth_toggler')) {
                var click_on = 'fullwidth_toggler';
                hide_items = new Array("fullscreen_toggler");
                closeToggles(hide_items);
            }
            /* bgimage_toggler */
            if (jQuery(this).hasClass('bgimage_toggler')) {
                var click_on = 'bgimage_toggler';
                hide_items = new Array("fullscreen_toggler");
                closeToggles(hide_items);
            }
            /* bgcolor_toggler */
            if (jQuery(this).hasClass('bgcolor_toggler')) {
                var click_on = 'bgcolor_toggler';
                hide_items = new Array("fullscreen_toggler");
                closeToggles(hide_items);
            }
        }
    });


    /* SLIDE CHECK BOX */


    /* START STATE */
    check_visual_part_for_toggles();
    /* END START STATE */


    jQuery(document).on('click', '.radio_toggle_cont .radio_toggle_mirage', function () {

        var this_click_btn = jQuery(this);
        var radio_toggle_cont = this_click_btn.parents(".radio_toggle_cont");

        if (this_click_btn.hasClass("checked")) {
            this_click_btn.stop().animate({backgroundPosition: '100% 0%'}, {duration: 'fast'});
            this_click_btn.removeClass("checked").addClass("not_checked");
            radio_toggle_cont.find('.yes_state').removeAttr("checked");
            radio_toggle_cont.find('.no_state').attr("checked", "checked");
        } else {

            /* only one accordion can be expanded */
            this_click_btn.parents('.edit_popup').find('.accordion_expanded_toggle').find('.radio_toggle_mirage').each(function (index) {
                if (jQuery(this).not(".checked")) {
                    var radio_toggle_cont2 = jQuery(this).parents(".radio_toggle_cont");
                    jQuery(this).stop().animate({backgroundPosition: '100% 0%'}, {duration: 'fast'});
                    jQuery(this).removeClass("checked").addClass("not_checked");
                    radio_toggle_cont2.find('.yes_state').removeAttr("checked");
                    radio_toggle_cont2.find('.no_state').attr("checked", "checked");
                }
            });

            this_click_btn.stop().animate({backgroundPosition: '0% 0%'}, {duration: 'fast'});
            this_click_btn.removeClass("not_checked").addClass("checked");
            radio_toggle_cont.find('.no_state').removeAttr("checked");
            radio_toggle_cont.find('.yes_state').attr("checked", "checked");
        }
    });
    /* END SLIDE CHECK BOX */

    /* PAGE BUILDER MODULE SIZER */
    function change_size_pb(parent_module_cont, new_size) {
        parent_module_cont.removeClass('block_1_4');
        parent_module_cont.removeClass('block_1_3');
        parent_module_cont.removeClass('block_1_2');
        parent_module_cont.removeClass('block_2_3');
        parent_module_cont.removeClass('block_3_4');
        parent_module_cont.removeClass('block_1_1');
        parent_module_cont.find('.current_size').val(new_size);
    }

    /* MORE */
    jQuery(document).on('click', '.right.box-with-icon .control-element', function () {
        parent_module_cont = jQuery(this).parents('.module-cont');
        var now_size = parent_module_cont.find('.current_size').val();

        if (now_size == "block_1_4") {
            change_size_pb(parent_module_cont, "block_1_3");
            parent_module_cont.addClass("block_1_3");
            parent_module_cont.find(".control-element span").html("1/3");
        }
        if (now_size == "block_1_3") {
            change_size_pb(parent_module_cont, "block_1_2");
            parent_module_cont.addClass("block_1_2");
            parent_module_cont.find(".control-element span").html("1/2");
        }
        if (now_size == "block_1_2") {
            change_size_pb(parent_module_cont, "block_2_3");
            parent_module_cont.addClass("block_2_3");
            parent_module_cont.find(".control-element span").html("2/3");
        }
        if (now_size == "block_2_3") {
            change_size_pb(parent_module_cont, "block_3_4");
            parent_module_cont.addClass("block_3_4");
            parent_module_cont.find(".control-element span").html("3/4");
        }
        if (now_size == "block_3_4") {
            change_size_pb(parent_module_cont, "block_1_1");
            parent_module_cont.addClass("block_1_1");
            parent_module_cont.find(".control-element span").html("1/1");
        }
    });
    /* LESS */
    jQuery(document).on('click', '.left.box-with-icon .control-element', function () {
        parent_module_cont = jQuery(this).parents('.module-cont');
        var now_size = parent_module_cont.find('.current_size').val();

        if (now_size == "block_1_1") {
            change_size_pb(parent_module_cont, "block_3_4");
            parent_module_cont.addClass("block_3_4");
            parent_module_cont.find(".control-element span").html("3/4");
        }
        if (now_size == "block_3_4") {
            change_size_pb(parent_module_cont, "block_2_3");
            parent_module_cont.addClass("block_2_3");
            parent_module_cont.find(".control-element span").html("2/3");
        }
        if (now_size == "block_2_3") {
            change_size_pb(parent_module_cont, "block_1_2");
            parent_module_cont.addClass("block_1_2");
            parent_module_cont.find(".control-element span").html("1/2");
        }
        if (now_size == "block_1_2") {
            change_size_pb(parent_module_cont, "block_1_3");
            parent_module_cont.addClass("block_1_3");
            parent_module_cont.find(".control-element span").html("1/3");
        }
        if (now_size == "block_1_3") {
            change_size_pb(parent_module_cont, "block_1_4");
            parent_module_cont.addClass("block_1_4");
            parent_module_cont.find(".control-element span").html("1/4");
        }
    });
    /* END PAGE BUILDER MODULE SIZER */


    /* ADD MODULE */
    jQuery(document).on('click', '.pb-module', function () {
        var add_module_name = jQuery(this).attr('data-module-name');
        var add_module_caption = jQuery(this).find('span.module-name').text();
        waiting_state_start();
        var tinyrand = "tiny" + getRandomInt(0, 9999);

        var data = {
            action: 'gt3pb_get_module_html',
            module_name: add_module_name,
            module_caption: add_module_caption,
            tinymce_activation_class: tinyrand,
            postid_for_module: post_id
        };

        jQuery.post(ajaxurl, data, function (response) {
            jQuery('.pb-list-active-modules .sortable-modules').append(response).tinymce_textareas("." + tinyrand);
            reactivate_color_picker();
            reactivate_sortable();
            waiting_state_end();
            reactivate_selectbox();
            check_visual_part_for_toggles();
            if (our_tiny_firstrun == true && jQuery('body').find('.tinyCont').size() == 1) {
                run_Tiny();
            }

            //tinymce.execCommand('mceAddEditor', false, tinyrand);
        });
    });
    /* END ADD MODULE */

    jQuery(document).on('click', '.module-cont .delete .control-element', function () {
        jQuery(this).parents(".module-cont").remove();
    });

    jQuery(document).on('click', '.delete.some-element2', function () {
        jQuery(this).parents(".price_feature").remove();
    });

    jQuery(document).on('click', '.section .some-element.delete', function () {
        jQuery(this).parents(".section").remove();
    });

    jQuery(document).on('click', '.section .some-element.edit', function () {
        jQuery(this).parents(".section").find(".hide_area").slideToggle("fast");
    });

    jQuery(document).on('click', '.some-element2.edit2', function () {
        jQuery(this).parents(".price_feature").find(".hide_area2").slideToggle("fast");
    });

    /* add new section accordion */
    jQuery(document).on('click', '.add_new_accordion_section', function () {
        var target1 = jQuery(this).parents(".edit_popup").find(".sections");
        var this_key1 = jQuery(this).parents(".module-cont").find(".module_key").val();
        var tinyrand = "tiny" + getRandomInt(0, 9999);
        var data = {
            tinymce_activation_class: tinyrand,
            action: 'get_unused_id_ajax'
        };

        var already_added_elemts_count = target1.find("li").length;

        waiting_state_start();

        jQuery.post(ajaxurl, data, function (response) {
            if (already_added_elemts_count > 0) {
                var this_append = "<li class='section'><div class='heading line_option visual_style1 big_type'><div class='option_title text-shadow1'>Section</div><div class='some-element clickable edit hovered'></div><div class='pre_toggler'></div><div class='some-element movable move hovered'></div><div class='pre_toggler'></div><div class='some-element clickable delete hovered'></div><div class='pre_toggler'></div></div><div class='clear'></div><div class='hide_area'><div class='some-padding'><input type='text' class='expanded_text1 type1 section_name' name='pagebuilder[modules][module_id][module_items][section_id][title]' value='Section'><textarea class='expanded_text1 type2 mt tinytextarea' id='ajax" + response + "' name='pagebuilder[modules][module_id][module_items][section_id][description]'></textarea></div><div class='expanded_state_cont'><span class='text-shadow1'>Expanded</span><div class='radio_toggle_cont accordion_expanded_toggle'><input type='radio' class='checkbox_slide yes_state' value='yes' name='pagebuilder[modules][module_id][module_items][section_id][expanded_state]'><input type='radio' class='checkbox_slide no_state' value='no' checked='checked' name='pagebuilder[modules][module_id][module_items][section_id][expanded_state]'><div class='radio_toggle_mirage' style='background-position: 100% 0%;'></div></div></div></div></li>";
            } else {
                var this_append = "<li class='section'><div class='heading line_option visual_style1 big_type'><div class='option_title text-shadow1'>Section</div><div class='some-element clickable edit hovered'></div><div class='pre_toggler'></div><div class='some-element movable move hovered'></div><div class='pre_toggler'></div><div class='some-element clickable delete hovered'></div><div class='pre_toggler'></div></div><div class='clear'></div><div class='hide_area'><div class='some-padding'><input type='text' class='expanded_text1 type1 section_name' name='pagebuilder[modules][module_id][module_items][section_id][title]' value='Section'><textarea class='expanded_text1 type2 mt tinytextarea' id='ajax" + response + "' name='pagebuilder[modules][module_id][module_items][section_id][description]'></textarea></div><div class='expanded_state_cont'><span class='text-shadow1'>Expanded</span><div class='radio_toggle_cont accordion_expanded_toggle'><input type='radio' class='checkbox_slide yes_state' value='yes' checked='checked' name='pagebuilder[modules][module_id][module_items][section_id][expanded_state]'><input type='radio' class='checkbox_slide no_state' value='no' name='pagebuilder[modules][module_id][module_items][section_id][expanded_state]'><div class='radio_toggle_mirage' style='background-position: 0% 0%;'></div></div></div></div></li>";
            }

            this_append = this_append.replace(new RegExp("section_id", 'g'), response);
            this_append = this_append.replace(new RegExp("module_id", 'g'), this_key1);
            set_acc_id = 'ajax' + response;

            target1.append(this_append).tinymce_textareas("." + tinyrand);
            if (our_tiny_firstrun == true && jQuery('body').find('.tinytextarea').size() == 1) {
                run_Tiny();
            }
            tinymce.execCommand('mceAddEditor', false, set_acc_id);

            reactivate_sortable();
            waiting_state_end();
        });
    });

    /* add new section diagramm */
    jQuery(document).on('click', '.add_new_diagramm_section', function () {
        var target1 = jQuery(this).parents(".edit_popup").find(".sections");
        var this_key1 = jQuery(this).parents(".module-cont").find(".module_key").val();
        var data = {
            action: 'get_unused_id_ajax'
        };

        waiting_state_start();

        jQuery.post(ajaxurl, data, function (response) {

            var this_append = "<li class='section'><div class='heading line_option visual_style1 big_type'><div class='option_title text-shadow1'>Section</div><div class='some-element clickable edit hovered'></div><div class='pre_toggler'></div><div class='some-element movable move hovered'></div><div class='pre_toggler'></div><div class='some-element clickable delete hovered'></div><div class='pre_toggler'></div></div><div class='clear'></div><div class='hide_area'><div class='some-padding'><input type='text' class='expanded_text1 type1 section_name' name='pagebuilder[modules][module_id][module_items][section_id][title]' value='Section'> Percent: <input type='text' value='' style='width:88px; text-align: center; margin-right: 2px; float: right;' name='pagebuilder[modules][module_id][module_items][section_id][percent]' class='expanded_text1 type1 section_name skill_percent'><!--input type='text' value='' name='pagebuilder[modules][module_id][module_items][section_id][description]' class='expanded_text1 type1 section_name skill_description'--></div></div></li>";

            this_append = this_append.replace(new RegExp("section_id", 'g'), response);
            this_append = this_append.replace(new RegExp("module_id", 'g'), this_key1);

            target1.append(this_append);

            reactivate_sortable();
            waiting_state_end();

        });
    });

    /* add new toggle section */
    jQuery(document).on('click', '.add_new_toggle_section', function () {
        var target1 = jQuery(this).parents(".edit_popup").find(".sections");
        var tinyrand = "tiny" + getRandomInt(0, 9999);
        var this_key1 = jQuery(this).parents(".module-cont").find(".module_key").val();
        var data = {
            tinymce_activation_class: tinyrand,
            action: 'get_unused_id_ajax'
        };

        waiting_state_start();

        jQuery.post(ajaxurl, data, function (response) {

            var this_append = "<li class='section'><div class='heading line_option visual_style1 big_type'><div class='option_title text-shadow1'>Section</div><div class='some-element clickable edit hovered'></div><div class='pre_toggler'></div><div class='some-element movable move hovered'></div><div class='pre_toggler'></div><div class='some-element clickable delete hovered'></div><div class='pre_toggler'></div></div><div class='clear'></div><div class='hide_area'><div class='some-padding'><input type='text' class='expanded_text1 type1 section_name' name='pagebuilder[modules][module_id][module_items][section_id][title]' value='Section'><textarea class='expanded_text1 type2 mt tinytextarea' id='ajax" + response + "' name='pagebuilder[modules][module_id][module_items][section_id][description]'></textarea></div><div class='expanded_state_cont'><span class='text-shadow1'>Expanded</span><div class='radio_toggle_cont toggles_expanded_toggle'><input type='radio' class='checkbox_slide yes_state' value='yes' name='pagebuilder[modules][module_id][module_items][section_id][expanded_state]'><input type='radio' class='checkbox_slide no_state' value='no' checked='checked' name='pagebuilder[modules][module_id][module_items][section_id][expanded_state]'><div class='radio_toggle_mirage' style='background-position: 100% 0%;'></div></div></div></div></li>";

            this_append = this_append.replace(new RegExp("section_id", 'g'), response);
            this_append = this_append.replace(new RegExp("module_id", 'g'), this_key1);

            target1.append(this_append).tinymce_textareas("." + tinyrand);

            set_acc_id = 'ajax' + response;
            if (our_tiny_firstrun == true && jQuery('body').find('.tinytextarea').size() == 1) {
                run_Tiny();
            }
            tinymce.execCommand('mceAddEditor', false, set_acc_id);

            reactivate_sortable();
            waiting_state_end();

        });
    });

    /* add new row */
    jQuery(document).on('click', '.add_new_row_section', function () {
        var target3 = jQuery(this).parents(".rows_must_be_here").find(".row-list");
        var this_key3 = jQuery(this).parents(".rows_must_be_here").find(".moduleid").val();
        var data = {
            action: 'get_unused_id_ajax'
        };

        waiting_state_start();

        jQuery.post(ajaxurl, data, function (response) {

            var this_append = "<li class='section'><div class='heading line_option visual_style1 big_type'><div class='option_title text-shadow1'>&nbsp;</div><div class='some-element clickable edit hovered'></div><div class='pre_toggler'></div><div class='some-element movable move hovered'></div><div class='pre_toggler'></div><div class='some-element clickable delete hovered'></div><div class='pre_toggler'></div></div><div class='clear'></div><div class='hide_area'><div class='some-padding'><textarea class='expanded_text1 type2 mt' id='ajax" + response + "' name='pagebuilder[modules][module_id][module_items][section_id][text]'></textarea></div></div></li>";

            this_append = this_append.replace(new RegExp("section_id", 'g'), response);
            this_append = this_append.replace(new RegExp("module_id", 'g'), this_key3);

            target3.append(this_append);
            set_row_id = 'ajax' + response;
            if (our_tiny_firstrun == true && jQuery('body').find('.tinytextarea').size() == 1) {
                run_Tiny();
            }
            tinymce.execCommand('mceAddEditor', false, set_row_id);

            reactivate_sortable();
            waiting_state_end();

        });
    });

    /* add new price feature */
    jQuery(document).on('click', '.add_new_price_feature', function () {
        var target3 = jQuery(this).parent().find(".feature-list");
        var this_key3 = jQuery(this).parent().find(".moduleid").val();
        var this_sectionid = jQuery(this).parent().find(".sectionid").val();
        var data = {
            action: 'get_unused_id_ajax'
        };

        waiting_state_start();

        jQuery.post(ajaxurl, data, function (response) {

            var this_append3 = "<li class='price_feature'><div class='heading line_option visual_style1 big_type'><div class='option_title text-shadow1'>&nbsp;</div><div class='some-element2 clickable edit2 hovered'></div><div class='pre_toggler'></div><div class='some-element2 movable move hovered'></div><div class='pre_toggler'></div><div class='some-element2 clickable delete hovered'></div><div class='pre_toggler'></div></div><div class='clear'></div><div class='hide_area2'><div class='some-padding'><textarea class='expanded_text1 type2 mt' id='ajax" + response + "' name='pagebuilder[modules][module_id][module_items][" + this_sectionid + "][price_features][" + response + "]'></textarea></div></div></li>";

            this_append3 = this_append3.replace(new RegExp("module_id", 'g'), this_key3);

            target3.append(this_append3);
            set_row_id = 'ajax' + response;

            reactivate_sortable();
            waiting_state_end();

        });
    });


    /* add new price block */
    jQuery(document).on('click', '.add_new_price_block', function () {
        var target3 = jQuery(this).parents(".rows_must_be_here").find(".row-list");
        var this_key3 = jQuery(this).parents(".rows_must_be_here").find(".moduleid").val();
        var data = {
            action: 'get_unused_id_ajax'
        };

        waiting_state_start();

        jQuery.post(ajaxurl, data, function (response) {

            var this_append = "<li class='section'><div class='heading line_option visual_style1 big_type'><div class='option_title text-shadow1'>&nbsp;</div><div class='some-element clickable edit hovered'></div><div class='pre_toggler'></div><div class='some-element movable move hovered'></div><div class='pre_toggler'></div><div class='some-element clickable delete hovered'></div><div class='pre_toggler'></div></div><div class='clear'></div><div class='hide_area'><div class='some-padding'><div class='caption'>Name</div><input class='expanded_text type3' name='pagebuilder[modules][module_id][module_items][section_id][block_name]'><div class='caption'>Price</div><input class='expanded_text type3' name='pagebuilder[modules][module_id][module_items][section_id][block_price]'><div class='caption'>Period</div><input class='expanded_text type3' name='pagebuilder[modules][module_id][module_items][section_id][block_period]'><div class='rows_must_be_here dark_lined'><input type='hidden' name='moduleid' class='moduleid' value='module_id'><input type='hidden' name='sectionid' class='sectionid' value='" + response + "'><div class='heading line_option visual_style1 small_type hovered clickable add_new_price_feature'><div class='option_title text-shadow1'>Add feature</div><div class='some-element cross'></div><div class='pre_toggler'></div></div><ul class='feature-list'></ul></div><div class='caption'>\"Get it now\" Link</div><input class='expanded_text type3' name='pagebuilder[modules][module_id][module_items][section_id][block_link]'><div class='caption'>\"Get it now\" caption</div><input class='expanded_text type3' name='pagebuilder[modules][module_id][module_items][section_id][get_it_now_caption]'><div class='caption' style='float:left; margin-top: 13px; margin-right: 15px;'>Most popular</div><div class='radio_toggle_cont toggles_expanded_toggle most_popular'><input type='radio' class='checkbox_slide yes_state' value='yes' name='pagebuilder[modules][module_id][module_items][section_id][most_popular]'><input type='radio' class='checkbox_slide no_state' value='no' checked='checked' name='pagebuilder[modules][module_id][module_items][section_id][most_popular]'><div class='radio_toggle_mirage' style='background-position: 100% 0%;'></div></div><div class='clear'></div></div></div></li>";

            this_append = this_append.replace(new RegExp("section_id", 'g'), response);
            this_append = this_append.replace(new RegExp("module_id", 'g'), this_key3);

            target3.append(this_append);

            reactivate_sortable();
            waiting_state_end();

        });
    });

    jQuery(document).on('click', '.all_part', function () {
        var cat_part_cont = jQuery(this).parents(".checkbox_wrapper").find(".category_part").toggleClass("cat_hided");
        if (jQuery(this).attr("checked")) {
            jQuery(this).parents(".checkbox_wrapper").find(".category_part").removeAttr("checked");
        }
    });

    jQuery(document).on('click', '.category_part', function () {
        jQuery(this).parents(".checkbox_wrapper").find(".all_part").removeAttr("checked");
    });

    jQuery(document).on('keyup', '.section_name', function () {
        var thistitle = jQuery(this).val();
        jQuery(this).parents(".section").find(".option_title").text(thistitle);
    });

    /* ADD IMAGE FOR POST FORMAT */
    jQuery(document).on('click', '.available-images-for-pf .ajax_cont .img-item', function () {
        //jQuery(this).removeClass("available_media_item").clone().appendTo(".selected-images-for-pf");
        var pffullurl = jQuery(this).find(".previmg").attr("data-full-url");
        var pfattach_id = jQuery(this).find(".previmg").attr("data-attach-id");
        var previewurl = jQuery(this).find(".previmg").attr("src");

        var data = {
            action: 'get_unused_id_ajax'
        };

        waiting_state_start();

        jQuery.post(ajaxurl, data, function (response) {
            jQuery(".selected-images-for-pf").append("<div class='img-item append_animation style_small'><div class='img-preview'><img src='" + previewurl + "' data-full-url='" + pffullurl + "' data-thumb-url='" + previewurl + "' alt='' class='previmg'><div class='hover-container'></div></div><input type='hidden' name='pagebuilder[post-formats][images][" + response + "][attach_id]' value='" + pfattach_id + "'></div>");

            jQuery('.img-item.append_animation').fadeIn('fast');
            setTimeout("jQuery('.img-item.append_animation').removeClass('append_animation')", 200);
            waiting_state_end();
        });

    });

    /* DELETE IMAGE FOR POST FORMAT */
    jQuery(document).on('click', '.selected-images-for-pf .img-item', function () {
        jQuery(this).fadeOut('fast');
        var tmpthis = jQuery(this);
        setTimeout(function () {
            tmpthis.remove();
        }, 1000);
    });

    popupAutoH();

    jQuery(document).on("click", ".upload_and_insert", function () {
        tb_show('', 'media-upload.php?type=audio&amp;TB_iframe=true');
        window.thisUploadButton = jQuery(this);

        window.send_to_editor = function (html) {
            audiourl = jQuery(html).attr('href');
            thisUploadButton.parent().next().val(audiourl);
            tb_remove();
        }

        return false;
    });

    /*Select icons*/
    jQuery(document).on("click", ".all_available_font_icons .stand_icon", function () {
        jQuery(this).parents(".enter_option_row").find(".icon_type").val(jQuery(this).attr("data-icon-code"));
        jQuery(this).parents(".enter_option_row").find("li").removeClass("active");
        jQuery(this).parents("li").addClass("active");
    });

    jQuery(document).on("click", ".remove_me", function () {
        jQuery(this).parents(".added_icons .stand_iconsweet").slideUp(300, function () {
            jQuery(this).remove();
        });
    });

    jQuery(document).on("click", ".remove_any_icons", function () {
        jQuery(this).parents(".already_added_icons .stand_iconsweet").slideUp(300, function () {
            jQuery(this).remove();
        });
    });


    /*Create any icons for page settings*/
    jQuery(document).ready(function () {
        jQuery('.sortable_icons_list').sortable();
    });
    jQuery(document).on("click", ".social_list_for_select i", function () {
        var clicked_icon = jQuery(this).attr("data-icon-code");
        var rnd_team = getRandomInt(1000, 9999);
        jQuery(this).parents(".hright").find(".added_icons").append("<div class='stand_iconsweet new_added ui-state-default' style='display:none'><span class='stand_icon-container'><i class='stand_icon " + clicked_icon + "'></i></span><input type='hidden' name='pagebuilder[page_settings][icons][" + clicked_icon + rnd_team + "][data-icon-code]' value='" + clicked_icon + "'><input type='text' class='icon_name' name='pagebuilder[page_settings][icons][" + clicked_icon + rnd_team + "][name]' value='' placeholder='Give some name'><input type='text' class='icon_link' name='pagebuilder[page_settings][icons][" + clicked_icon + rnd_team + "][link]' value='' placeholder='Give some link'><input class='cpicker' type='text' name='pagebuilder[page_settings][icons][" + clicked_icon + rnd_team + "][fcolor]' value='262525' placeholder='Foreground color'><input type='text' value='' class='cpicker_preview' disabled='disabled' style='background:#ffffff'><input class='cpicker' type='text' name='pagebuilder[page_settings][icons][" + clicked_icon + rnd_team + "][bcolor]' value='dddddd' placeholder='Background color'><input type='text' value='' class='cpicker_preview' disabled='disabled' style='background-color:#ffffff;'><span class='remove_me'><i class='stand_icon icon-remove'></i></span></div>");
        jQuery('.new_added').slideDown(300).removeClass('new_added');
        reactivate_color_picker();
        reactivate_sortable();
    });

    jQuery(document).on("click", ".social_list_for_select2 i", function () {
        var clicked_icon = jQuery(this).attr("data-icon-code");
        var rnd_team = getRandomInt(1000, 9999);
        jQuery(this).parents(".hright").find(".added_icons").append("<div class='stand_iconsweet new_added ui-state-default' style='display:none'><span class='stand_icon-container'><i class='stand_icon " + clicked_icon + "'></i></span><input type='hidden' name='pagebuilder[page_settings][icons][" + clicked_icon + rnd_team + "][data-icon-code]' value='" + clicked_icon + "'><input type='text' class='icon_name' name='pagebuilder[page_settings][icons][" + clicked_icon + rnd_team + "][name]' value='' placeholder='Give some name'><input type='text' class='icon_link' name='pagebuilder[page_settings][icons][" + clicked_icon + rnd_team + "][link]' value='' placeholder='Give some link'><span class='remove_me'><i class='stand_icon icon-remove'></i></span></div>");
        jQuery('.new_added').slideDown(300).removeClass('new_added');
        reactivate_color_picker();
        reactivate_sortable();
    });

    /*Create any icons for modules*/
    jQuery(document).on("click", ".all_available_font_icons_for_any_icons i.stand_icon", function () {
        var clicked_icon = jQuery(this).attr("data-icon-code");
        var any_icons_settingname = jQuery(this).parents(".all_available_font_icons_for_any_icons_cont").find(".any_icons_settingname").val();
        var any_icons_moduleid = jQuery(this).parents(".all_available_font_icons_for_any_icons_cont").find(".any_icons_moduleid").val();
        var rnd_any_icon = getRandomInt(1000, 9999);

        jQuery(this).parents(".all_available_font_icons_for_any_icons_cont").find(".already_added_icons").append("<div class='stand_iconsweet new_added ui-state-default' style='display:none'><span class='stand_icon-container'><i class='stand_icon " + clicked_icon + "'></i></span><input type='hidden' name='pagebuilder[modules][" + any_icons_moduleid + "][" + any_icons_settingname + "][" + clicked_icon + rnd_any_icon + "][data-icon-code]' value='" + clicked_icon + "'><input type='text' class='icon_name textoption type1' name='pagebuilder[modules][" + any_icons_moduleid + "][" + any_icons_settingname + "][" + clicked_icon + rnd_any_icon + "][name]' value='' placeholder='Give some name'><input type='text' class='icon_link textoption type1' name='pagebuilder[modules][" + any_icons_moduleid + "][" + any_icons_settingname + "][" + clicked_icon + rnd_any_icon + "][link]' value='' placeholder='Give some link'><input class='cpicker textoption type1' type='text' name='pagebuilder[modules][" + any_icons_moduleid + "][" + any_icons_settingname + "][" + clicked_icon + rnd_any_icon + "][fcolor]' value='262525' placeholder='Foreground color'><input type='text' value='' class='cpicker_preview textoption type1' disabled='disabled' style='background:#ffffff'><input class='cpicker textoption type1' type='text' name='pagebuilder[modules][" + any_icons_moduleid + "][" + any_icons_settingname + "][" + clicked_icon + rnd_any_icon + "][bcolor]' value='ffffff' placeholder='Background color'><input style='background:#c5c5c5' type='text' value='' class='cpicker_preview textoption type1' disabled='disabled'><span class='remove_me'><i class='remove_any_icons icon-remove'></i></span></div>");

        jQuery('.new_added').slideDown(300).removeClass('new_added');
        reactivate_color_picker();
        reactivate_sortable();
    });
});


/* WORK ON LOAD */
jQuery(document).ready(function () {
    /* OPEN ALL PARENT CONTAINERS IF TOGGLER ON */
    jQuery(".pb-cont .line_option .toggler .yes_state").each(function (index) {
        var yes_state = jQuery(this).attr('checked');
        if (yes_state == 'checked') {
            jQuery(this).parents('.bg_or_slider_option').find('.hideable-area').show('fast');
        }
    });

    /* OPEN ALL PARENT CONTAINERS IF TOGGLER ON */
    jQuery(".pb-cont .line_option .toggler .yes_state").each(function (index) {
        var yes_state = jQuery(this).attr('checked');
        if (yes_state == 'checked') {
            jQuery(this).parents('.bg_or_slider_option').find('.hideable-area').show('fast');
        }
    });

    /*SET HOVER TO ACTIVE ICONS*/
    jQuery(".all_available_font_icons .icon_type").each(function (index) {
        jQuery(this).parents(".enter_option_row").find("i[data-icon-code='" + jQuery(this).val() + "']").parent().addClass("active");
    });
});