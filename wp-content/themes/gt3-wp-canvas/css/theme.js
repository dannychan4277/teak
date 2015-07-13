"use strict";

////////////////////
// Document Ready //
////////////////////
jQuery(document).ready(function () {
    var window_w = jQuery(window).width(),
        window_h = jQuery(window).height(),
        wrapper_h = jQuery('.wrapper').height(),
        header_h = jQuery('header').height(),
        footer_h = jQuery('footer').height(),
        cont_fw_h = jQuery('.container.fw').height();

    setTimeout("jQuery('body').css('opacity', '1')", 500);

    jQuery('.gt3_menu .menu .sub-menu .current-menu-item').parent().css('display', 'block').parent().addClass("gt3_menu_active_menu_item").find("span > i").removeClass("icon-chevron-down").addClass("icon-chevron-up");

    jQuery('.current-menu-ancestor .current-menu-parent').addClass("current-menu-parent_open").parent().css('display', 'block').parent().addClass("gt3_menu_active_menu_item").find("span > i").removeClass("icon-chevron-down").addClass("icon-chevron-up");

    jQuery('.horizontal_menu ul li > .sub-menu li.menu-item-has-children a span i').removeClass('icon-chevron-down').addClass('icon-angle-right');

    var active_menu = jQuery('.gt3_menu .menu-item-has-children .sub-menu .menu-item-has-children').hasClass('current-menu-parent');
    if (active_menu == false) {
        jQuery('.gt3_menu .menu-item-has-children .sub-menu .menu-item-has-children').find("span > i").removeClass("icon-chevron-up").addClass("icon-chevron-down");
    }

    if (window_w < 767) {
        jQuery('.gt3_menu').wrap("<div class='menu_mobile'></div>");
        jQuery('.menu_scroll').removeClass('menu_scroll');
        jQuery('.menu_toggler').click(function(){
            jQuery('.menu_mobile').slideToggle(300);
        });
    }

    jQuery('.menu_scroll').jScrollPane({
        autoReinitialise: true
    });

    jQuery('.share_text').click(function () {
        jQuery(this).parent().find('.sh_fo_detail').toggle('fast');
    });

    jQuery('.menu_toggler').click(function () {
        jQuery(this).toggleClass('active');
        jQuery('body').toggleClass('gt3_menu_active');
    });

    jQuery(document).on("click", "footer, .wrapper", function () {
        jQuery('.menu_toggler').removeClass('active');
        jQuery('body').removeClass('gt3_menu_active');
    });

    jQuery(".search_form i").click(function () {
        jQuery(this).parents(".search_form").find(".s_btn_search").click();
    });

    jQuery(".gt3_menu .menu-item-has-children > a").click(function () {
        jQuery(this).next().slideToggle("fast").parent().toggleClass("gt3_menu_active_menu_item");
        jQuery(this).find("span > i").toggleClass("icon-chevron-down");
        jQuery(this).find("span > i").toggleClass("icon-chevron-up");
    });

    jQuery(document).on('click', '.post_likes_add', function () {
        var post_likes_this = jQuery(this);
        if (!jQuery.cookie('like' + post_likes_this.attr('data-postid'))) {
            jQuery.post(gt3_ajaxurl, {
                action: 'add_like_post',
                post_id: jQuery(this).attr('data-postid')
            }, function (response) {
                jQuery.cookie('like' + post_likes_this.attr('data-postid'), 'true', {expires: 7, path: '/'});
                post_likes_this.addClass('already_liked');
                post_likes_this.find("span").text(response);
                post_likes_this.find("i").removeClass("icon-heart-empty").addClass("icon-heart");
            });
        }
    });
    jQuery('.layer_block').hover(function () {
        var block_width = jQuery(this).attr('data-width'),
            content_number = jQuery(this).find('.layer_block_content').size(),
            content_height = jQuery('.layer_block_content').height(),
            content_margin = (block_width - (content_height * content_number)) / 2;
        jQuery(this).css({
            'width': block_width,
            'height': block_width,
            'margin-left': -(block_width / 2 + 18),
            'margin-top': -(block_width / 2 + 18),
            'padding': (block_width / 2)
        }).addClass('hovered').find('.layer_block_content:first-child').css({'margin-top': content_margin});
        setTimeout("jQuery('.hovered>.layer_block_content').css('display', 'block').css('transform', 'scale(1)');", 300);
    }, function () {
        jQuery('.layer_block_content').css('display', 'none');
        jQuery(this).css({
            'width': 8,
            'height': 8,
            'margin-left': 0,
            'margin-top': 0,
            'padding': 0
        }).removeClass('hovered');
    });
});

/////////////////
// Window Load //
/////////////////
jQuery(window).load(function () {
    setTimeout("jQuery('body').css('opacity', '1')", 500);
});

///////////////////
// Window Resize //
///////////////////
jQuery(window).resize(function () {
    jQuery('.menu_scroll').jScrollPane({
        autoReinitialise: true
    });
});

///////////////////
// Menu Folding //
///////////////////
jQuery(window).load(function () {
    //if (location.pathname === "/furniture/") {
        //jQuery(".archive .product-categories .children").addClass("hidden");
        jQuery(".archive .product-categories>li.cat-parent>a").on("mouseover", function (e) {
            jQuery(jQuery(e.toElement)[0].parentElement).find(".children").addClass("show");
        });
        jQuery(".archive .product-categories>li.cat-parent>a").on("mouseout", function (e) {
            jQuery(jQuery(e.toElement)[0].parentElement).find(".children").removeClass("show");
        });

        jQuery(".archive .product-categories>li.cat-parent>ul.children").on("mouseover", function (e) {
            jQuery(this).addClass("show");
        });
        jQuery(".archive .product-categories>li.cat-parent>ul.children").on("mouseout", function (e) {
            jQuery(this).removeClass("show");
        });
        jQuery("body.archive").append("<style>\
                ul.product-categories>li{\
                    position: relative;\
                }\
                ul.product-categories>li.cat-parent>ul.children{\
                    position: absolute;\
                    z-index: 1;\
                }\
                ul.product-categories>li>a{\
                    margin: 0px 10px;\
                }\
            </style>");
        jQuery("body.single-product").prepend('<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">');
        jQuery("body.single-product .woocommerce_container>div.product").prepend('<i class="prev"/><i class="next"/>');


        var currentProduct = jQuery("body.single-product .woocommerce_container>div.product")[0];
        var currentProductNumber = parseInt(currentProduct.id.split("-")[1]);
        var prevProductNumber = 0;
        var prevProductLink = "";
        var nextProductNumber = 10000000;
        var nextProductLink = "";

        var relatedProducts = jQuery("body.single-product .related ul.products li");
        relatedProducts.each(function (index) {
            var product = jQuery(relatedProducts[index])[0];
            var classNames = product.className;
            var classNameList = classNames.split(" ");
            var postNumber = classNameList.reduce(function (postNumber, className) {
                if (className.indexOf("post") === 0) {
                    var postNumberString = className.split("-")[1];
                    postNumber = parseInt(postNumberString);
                    return postNumber;
                }
                return postNumber;
            }, 0);
            if (postNumber < currentProductNumber && postNumber > prevProductNumber) {
                prevProductNumber = postNumber;
                prevProductLink = jQuery(product).find("a")[0].href;
            }
            if (postNumber < nextProductNumber && postNumber > currentProductNumber) {
                nextProductNumber = postNumber;
                nextProductLink = jQuery(product).find("a")[0].href;
            }
        });

        if (prevProductLink === "") {
            jQuery("i.prev").addClass("hidden");
        }
        if (nextProductLink === "") {
            jQuery("i.next").addClass("hidden");
        }
        jQuery("i.prev").click(function () {
            if (prevProductLink !== "") {
                location.href = prevProductLink;
            }
        });
        jQuery("i.next").click(function () {
            if (nextProductLink !== "") {
                location.href = nextProductLink;
            }
        });
});
