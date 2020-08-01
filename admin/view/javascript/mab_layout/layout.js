


var ajaxaction = 'index.php?route=extension/module/webservice/saveComponent&user_token='+user_token;

var save_element_ajaxaction = 'index.php?route=extension/module/webservice/saveElement&user_token='+user_token;

var save_heading_ajaxaction = 'index.php?route=extension/module/webservice/saveHeading&user_token='+user_token;

var get_component_ajaxaction = 'index.php?route=extension/module/webservice/getComponent&user_token='+user_token;
var get_componenttype_ajaxaction = 'index.php?route=extension/module/webservice/getComponentType&user_token='+user_token;
var delete_ajaxaction = 'index.php?route=extension/module/webservice/deleteComponent&user_token='+user_token;

var deleteImage_ajaxaction = 'index.php?route=extension/module/webservice/deleteImage&user_token='+user_token;
var home_pic_gif = 'view/image/Layout_Components/home_pic.gif';




$(document).ready(function () {

    $('#top_category').click(function () {

        addTopCategory(0);

    });

    $('#banner_square').click(function () {

        addBannerSquare(0);

    });

    $('#banner_HS').click(function () {

        addBannerHorizontalslide(0);

    });

    $('#banner_grid').click(function () {

        addBannergrid(0);

    });

    $('#banner_countdown').click(function () {

        addBannerCountdown(0);

    });

    $('#product_square').click(function () {

        addProductSquare(0);

    });

    $('#product_HS').click(function () {

        addProductHorizontalslide(0);

    });

    $('#product_grid').click(function () {

        addProductGrid(0);

    });

    $('#product_LA').click(function () {

        addLastAccessed(0);

    });
    
    preview_content();
});



function addTopCategory(id) {

    num_of_component = $('#number_of_component').val();

    if (id) {

        num_of_component = parseInt($('#number_of_component').val());

        num_of_component = num_of_component + 1;

        $('#number_of_component').val(num_of_component);

        var top_category_html = $('.top_category').html();

        var id_layout = $('#id_layout').val();

        top_category_html = top_category_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);

        top_category_html = top_category_html.replace(/top_category_edit_component/g, 'edit_' + id);

        top_category_html = top_category_html.replace(/top_category_delete_component/g, 'delete_' + id);
        /*start:changes made by Aayushi Agarwal on 27th March 2020 to make preview changes*/
        /*
         * To assign the unique ID to top 4 categories div for different component in different layouts
         */
        var id_component = id;
        for (var index=1; index<=4; index++) {
           var to_replace1 =  'top_category_'+ index;
           var to_replace2 =  'top_category_text_'+ index;
           var with_replace1 = 'top_category_'+ index +'_' + id_layout + '_' + id;
           var with_replace2 = 'top_category_text_'+ index +'_' + id_layout + '_' + id;
           top_category_html = top_category_html.replace(to_replace1, with_replace1);
           top_category_html = top_category_html.replace(to_replace2, with_replace2);
        }
        /*end:changes made by Aayushi Agarwal on 27th March 2020 to make preview changes*/
        $('.slides').append(top_category_html);

        preview_content();

        scrollToBottom();
        /*start:changes made by Aayushi Agarwal on 27th March 2020 to make preview changes*/
        /*
         * To append preview in the device for already added categories when any layout is edited.
         */
        $.ajax({
        url: get_component_ajaxaction + "&getCategoryForm=true",
        data: 'id_layout=' + id_layout + '&id_component=' + id_component,
        type: "post",
        beforeSend: function () {
           
        },
        success: function (data)
        {
            var c = JSON.parse(data);
            if (1) {
                if (b.length > 0) {
                    for (i = 0; i < b.length; i++) {
                        var j = i+1;

                        if(b[i]['id_category'] > 0) {
                            if (j<4) {
                                $('img[id="top_category_'+j+'_'+ id_layout + '_' + id_component + '"]').attr('src', b[i]['image_url']);
                            }
                        }    
                    }
                }
            }
            uploadtopCategoryfile();
        }
    });
    /*end:changes made by Aayushi Agarwal on 27th March 2020 to make preview changes*/
    } else {

        var a = "top_category";

        var id_layout = $('#id_layout').val();

        if (num_of_component <= 20) {

            num_of_component = parseInt(num_of_component) + 1;

            $('#number_of_component').val(num_of_component);

            $.ajax({

                url: ajaxaction + "&action=add_component&assign_component_id=true",

                data: 'component_type=' + a + '&id_layout=' + id_layout,

                type: "post",

                success: function (data)

                {   

                    var id = data;

                    if (id) {

                        var top_category_html = $('.top_category').html();

                        var id_layout = $('#id_layout').val();

                        top_category_html = top_category_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);

                        top_category_html = top_category_html.replace(/top_category_edit_component/g, 'edit_' + id);

                        top_category_html = top_category_html.replace(/top_category_delete_component/g, 'delete_' + id);
                        /*start:changes made by Aayushi Agarwal on 27th March 2020 to make preview changes*/
                        /*
                        * To assign the unique ID to top 4 categories div for different component in different layouts for a new category component
                        */
                        for (var index=1; index<=4; index++) {
                           var to_replace1 =  'top_category_'+ index;
                           var to_replace2 =  'top_category_text_'+ index;
                           var with_replace1 = 'top_category_'+ index +'_' + id_layout + '_' + id;
                           var with_replace2 = 'top_category_text_'+ index +'_' + id_layout + '_' + id;
                           top_category_html = top_category_html.replace(to_replace1, with_replace1);
                           top_category_html = top_category_html.replace(to_replace2, with_replace2);
                        }
                        /*end:changes made by Aayushi Agarwal on 27th March 2020 to make preview changes*/
                        $('.slides').append(top_category_html);

                        preview_content();

                        scrollToBottom();

                        showSuccessMessage(component_add);

                    }

                }

            });

        } else {

            showErrorMessage(limit_reached);

        }

    }

}



function addBannerSquare(id) {

    if (id) {

        num_of_component = parseInt($('#number_of_component').val());

        num_of_component = num_of_component + 1;

        $('#number_of_component').val(num_of_component);

        var banner_square_html = $('.banner-slide').html();

        var id_layout = $('#id_layout').val();

        banner_square_html = banner_square_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);

        banner_square_html = banner_square_html.replace(/banner_square_edit_component/g, 'edit_' + id);

        banner_square_html = banner_square_html.replace(/banner_square_delete_component/g, 'delete_' + id);
        /*start:changes made by Aayushi Agarwal on 27th March 2020 to make preview changes*/
        /*
         * To assign the unique ID to banner square slides for different component in different layouts
         */
        var id_component = id;
        banner_square_html = banner_square_html.replace(/bannerSquareBanner_elem_heading/g, 'bannerSquareBanner_elem_heading_' + id_layout + '_' + id);
        banner_square_html = banner_square_html.replace(/bannerSquare_comp_heading/g, 'bannerSquare_comp_heading_' + id_layout + '_' + id);
        banner_square_html = banner_square_html.replace(/bannerSquareBannerimg/g, 'bannerSquareBannerimg_' + id_layout + '_' + id);
        banner_square_html = banner_square_html.replace(/bannerSList/g, 'bannerSList_' + id_layout + '_' + id);
        /*end:changes made by Aayushi Agarwal on 27th March 2020 to make preview changes*/
        $('.slides').append(banner_square_html);

        preview_content();

        scrollToBottom();

    } else {

        num_of_component = parseInt($('#number_of_component').val());

        num_of_component = num_of_component + 1;

        var id_layout = $('#id_layout').val();

        $('#number_of_component').val(num_of_component);

        var a = "banner_square";

        if (num_of_component <= 20) {

            $.ajax({

                url: ajaxaction + "&configure=kbmobileapp&assign_component_id=true",

                data: 'component_type=' + a + '&id_layout=' + id_layout,

                type: "post",

                success: function (data)

                {

                    var id = data;

                    if (id) {

                        var banner_square_html = $('.banner-slide').html();

                        var id_layout = $('#id_layout').val();

                        banner_square_html = banner_square_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);

                        banner_square_html = banner_square_html.replace(/banner_square_edit_component/g, 'edit_' + id);

                        banner_square_html = banner_square_html.replace(/banner_square_delete_component/g, 'delete_' + id);
                        /*start:changes made by Aayushi Agarwal on 27th March 2020 to make preview changes*/
                        banner_square_html = banner_square_html.replace(/bannerSquareBanner_elem_heading/g, 'bannerSquareBanner_elem_heading_' + id_layout + '_' + id);
                        banner_square_html = banner_square_html.replace(/bannerSquare_comp_heading/g, 'bannerSquare_comp_heading_' + id_layout + '_' + id);
                        banner_square_html = banner_square_html.replace(/bannerSquareBannerimg/g, 'bannerSquareBannerimg_' + id_layout + '_' + id);
                        banner_square_html = banner_square_html.replace(/bannerSList/g, 'bannerSList_' + id_layout + '_' + id);
                        
                        /*end:changes made by Aayushi Agarwal on 27th March 2020 to make preview changes*/
                        $('.slides').append(banner_square_html);

                        preview_content();

                        scrollToBottom();

                        showSuccessMessage(component_add);

                    }

                }

            });



        } else {

            showErrorMessage(limit_reached);

        }

    }

}

function addBannerHorizontalslide(id) {

    if (id) {

        num_of_component = parseInt($('#number_of_component').val());

        num_of_component = num_of_component + 1;

        $('#number_of_component').val(num_of_component);

        var Hbanner_square_html = $('.Hbanner-slide').html();

        var id_layout = $('#id_layout').val();

        Hbanner_square_html = Hbanner_square_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);

        Hbanner_square_html = Hbanner_square_html.replace(/banner_horizontal_edit_component/g, 'edit_' + id);

        Hbanner_square_html = Hbanner_square_html.replace(/banner_horizontal_delete_component/g, 'delete_' + id);
        Hbanner_square_html = Hbanner_square_html.replace(/slidingBList/g, 'slidingBList_'  + id_layout + '_' + id);
        
        $('.slides').append(Hbanner_square_html);

        preview_content();

        scrollToBottom();

    } else {

        num_of_component = parseInt($('#number_of_component').val());

        num_of_component = num_of_component + 1;

        var id_layout = $('#id_layout').val();

        $('#number_of_component').val(num_of_component);

        var a = "banner_horizontal_slider";

        if (num_of_component <= 20) {

            $.ajax({

                url: ajaxaction + "&configure=kbmobileapp&assign_component_id=true",

                data: 'component_type=' + a + '&id_layout=' + id_layout,

                type: "post",

                success: function (data)

                {

                    var id = data;

                    if (id) {

                        var Hbanner_square_html = $('.Hbanner-slide').html();

                        var id_layout = $('#id_layout').val();

                        Hbanner_square_html = Hbanner_square_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);

                        Hbanner_square_html = Hbanner_square_html.replace(/banner_horizontal_edit_component/g, 'edit_' + id);

                        Hbanner_square_html = Hbanner_square_html.replace(/banner_horizontal_delete_component/g, 'delete_' + id);
                        Hbanner_square_html = Hbanner_square_html.replace(/bannerHorizontalSlide_comp_heading/g, 'bannerHorizontalSlide_comp_heading_' + id_layout + '_' + id);
                        Hbanner_square_html = Hbanner_square_html.replace(/bannerHorizontalSlideimg/g, 'bannerHorizontalSlideimg_' + id_layout + '_' + id);
                        Hbanner_square_html = Hbanner_square_html.replace(/slidingBList/g, 'slidingBList_' + id_layout + '_' + id);
                        $('.slides').append(Hbanner_square_html);

                        preview_content();

                        scrollToBottom();

                        showSuccessMessage(component_add);

                    }

                }

            });



        } else {

            showErrorMessage(limit_reached);

        }

    }

}

function addBannergrid(id) {

    if (id) {

        num_of_component = parseInt($('#number_of_component').val());

        num_of_component = num_of_component + 1;

        $('#number_of_component').val(num_of_component);

        var banner_Grid_html = $('.banner-grid').html();

        var id_layout = $('#id_layout').val();

        banner_Grid_html = banner_Grid_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);

        banner_Grid_html = banner_Grid_html.replace(/banner_grid_edit_component/g, 'edit_' + id);

        banner_Grid_html = banner_Grid_html.replace(/banner_grid_delete_component/g, 'delete_' + id);

        $('.slides').append(banner_Grid_html);

        preview_content();

        scrollToBottom();

    } else {

        num_of_component = parseInt($('#number_of_component').val());

        num_of_component = num_of_component + 1;

        var id_layout = $('#id_layout').val();

        $('#number_of_component').val(num_of_component);

        var a = "banners_grid";

        if (num_of_component <= 20) {

            $.ajax({

                url: ajaxaction + "&configure=kbmobileapp&assign_component_id=true",

                data: 'component_type=' + a + '&id_layout=' + id_layout,

                type: "post",

                success: function (data)

                {

                    var id = data;

                    if (id) {

                        var banner_Grid_html = $('.banner-grid').html();

                        var id_layout = $('#id_layout').val();

                        banner_Grid_html = banner_Grid_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);

                        banner_Grid_html = banner_Grid_html.replace(/banner_grid_edit_component/g, 'edit_' + id);

                        banner_Grid_html = banner_Grid_html.replace(/banner_grid_delete_component/g, 'delete_' + id);
                        banner_Grid_html = banner_Grid_html.replace(/bannerGrid_comp_heading/g, 'bannerGrid_comp_heading_' + id_layout + '_' + id);
                        $('.slides').append(banner_Grid_html);

                        preview_content();

                        scrollToBottom();

                        showSuccessMessage(component_add);



                    }

                }

            });



        } else {

            showErrorMessage(limit_reached);

        }

    }



}

function addBannerCountdown(id) {

    if (id) {

        num_of_component = parseInt($('#number_of_component').val());

        num_of_component = num_of_component + 1;

        $('#number_of_component').val(num_of_component);

        var banner_countdown_html = $('.banner-countdown').html();

        var id_layout = $('#id_layout').val();

        banner_countdown_html = banner_countdown_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);

        banner_countdown_html = banner_countdown_html.replace(/banner_countdown_edit_component/g, 'edit_' + id);

        banner_countdown_html = banner_countdown_html.replace(/banner_countdown_delete_component/g, 'delete_' + id);

        $('.slides').append(banner_countdown_html);

        preview_content();

        scrollToBottom();

    } else {

        num_of_component = parseInt($('#number_of_component').val());

        num_of_component = num_of_component + 1;

        var id_layout = $('#id_layout').val();

        $('#number_of_component').val(num_of_component);

        var a = "banners_countdown";

        if (num_of_component <= 20) {

            $.ajax({

                url: ajaxaction + "&configure=kbmobileapp&assign_component_id=true",

                data: 'component_type=' + a + '&id_layout=' + id_layout,

                type: "post",

                success: function (data)

                {

                    var id = data;

                    if (id) {

                        var banner_countdown_html = $('.banner-countdown').html();

                        var id_layout = $('#id_layout').val();

                        banner_countdown_html = banner_countdown_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);

                        banner_countdown_html = banner_countdown_html.replace(/banner_countdown_edit_component/g, 'edit_' + id);

                        banner_countdown_html = banner_countdown_html.replace(/banner_countdown_delete_component/g, 'delete_' + id);
                        banner_countdown_html = banner_countdown_html.replace(/bannerCountdown_comp_heading/g, 'bannerCountdown_comp_heading_' + id_layout + '_' + id);
                        banner_countdown_html = banner_countdown_html.replace(/bannerCountdownList/g, 'bannerCountdownList_' + id_layout + '_' + id);
                        $('.slides').append(banner_countdown_html);

                        preview_content();

                        scrollToBottom();

                        showSuccessMessage(component_add);

                    }

                }

            });



        } else {

            showErrorMessage(limit_reached);

        }

    }

}

function addProductSquare(id) {

    if (id) {

        num_of_component = parseInt($('#number_of_component').val());

        num_of_component = num_of_component + 1;

        $('#number_of_component').val(num_of_component);

        var product_square_html = $('.product-square').html();

        var id_layout = $('#id_layout').val();

        product_square_html = product_square_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);

        product_square_html = product_square_html.replace(/product_square_edit_component/g, 'edit_' + id);

        product_square_html = product_square_html.replace(/product_square_delete_component/g, 'delete_' + id);

        $('.slides').append(product_square_html);

        preview_content();

        scrollToBottom();

    } else {

        num_of_component = parseInt($('#number_of_component').val());

        num_of_component = num_of_component + 1;

        var id_layout = $('#id_layout').val();

        $('#number_of_component').val(num_of_component);

        var a = "products_square";

        if (num_of_component <= 20) {

            $.ajax({

                url: ajaxaction + "&configure=kbmobileapp&assign_component_id=true",

                data: 'component_type=' + a + '&id_layout=' + id_layout,

                type: "post",

                success: function (data)

                {

                    var id = data;

                    if (id) {

                        var product_square_html = $('.product-square').html();

                        var id_layout = $('#id_layout').val();

                        product_square_html = product_square_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);

                        product_square_html = product_square_html.replace(/product_square_edit_component/g, 'edit_' + id);

                        product_square_html = product_square_html.replace(/product_square_delete_component/g, 'delete_' + id);
                        
                        product_square_html = product_square_html.replace(/productSquare_comp_heading/g, 'productSquare_comp_heading_' + id_layout + '_' + id);
                        product_square_html = product_square_html.replace(/product_square_list/g, 'product_square_list_' + id_layout + '_' + id);
                        
                        $('.slides').append(product_square_html);

                        preview_content();

                        scrollToBottom();

                        showSuccessMessage(component_add);

                    }

                }

            });



        } else {

            showErrorMessage(limit_reached);

        }

    }

}

function addProductHorizontalslide(id) {

    if (id) {

        num_of_component = parseInt($('#number_of_component').val());

        num_of_component = num_of_component + 1;

        $('#number_of_component').val(num_of_component);

        var Hproduct_slide_html = $('.Hproduct-slide').html();

        var id_layout = $('#id_layout').val();

        Hproduct_slide_html = Hproduct_slide_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);

        Hproduct_slide_html = Hproduct_slide_html.replace(/product_horizontal_edit_component/g, 'edit_' + id);

        Hproduct_slide_html = Hproduct_slide_html.replace(/product_horizontal_delete_component/g, 'delete_' + id);

        $('.slides').append(Hproduct_slide_html);

        preview_content();

        scrollToBottom();

    } else {

        num_of_component = parseInt($('#number_of_component').val());

        num_of_component = num_of_component + 1;

        var id_layout = $('#id_layout').val();

        $('#number_of_component').val(num_of_component);

        var a = "products_horizontal";

        if (num_of_component <= 20) {

            $.ajax({

                url: ajaxaction + "&configure=kbmobileapp&assign_component_id=true",

                data: 'component_type=' + a + '&id_layout=' + id_layout,

                type: "post",

                success: function (data)

                {

                    var id = data;

                    if (id) {

                        var Hproduct_slide_html = $('.Hproduct-slide').html();

                        var id_layout = $('#id_layout').val();

                        Hproduct_slide_html = Hproduct_slide_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);

                        Hproduct_slide_html = Hproduct_slide_html.replace(/product_horizontal_edit_component/g, 'edit_' + id);

                        Hproduct_slide_html = Hproduct_slide_html.replace(/product_horizontal_delete_component/g, 'delete_' + id);
                        
                        Hproduct_slide_html = Hproduct_slide_html.replace(/slidingProducts_comp_heading/g, 'slidingProducts_comp_heading_' + id_layout + '_' + id);
                        Hproduct_slide_html = Hproduct_slide_html.replace(/product_sliding_banner/g, 'product_sliding_banner_' + id_layout + '_' + id);
                        
                        $('.slides').append(Hproduct_slide_html);

                        preview_content();

                        scrollToBottom();

                        showSuccessMessage(component_add);

                    }

                }

            });



        } else {

            showErrorMessage(limit_reached);

        }

    }

}

function addProductGrid(id) {





    if (id) {

        num_of_component = parseInt($('#number_of_component').val());

        num_of_component = num_of_component + 1;

        $('#number_of_component').val(num_of_component);

        var product_Grid_html = $('.product-grid').html();

        var id_layout = $('#id_layout').val();

        product_Grid_html = product_Grid_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);

        product_Grid_html = product_Grid_html.replace(/product_grid_edit_component/g, 'edit_' + id);

        product_Grid_html = product_Grid_html.replace(/product_grid_delete_component/g, 'delete_' + id);

        $('.slides').append(product_Grid_html);

        preview_content();

        scrollToBottom();

    } else {

        num_of_component = parseInt($('#number_of_component').val());

        num_of_component = num_of_component + 1;

        var id_layout = $('#id_layout').val();

        $('#number_of_component').val(num_of_component);

        var a = "products_grid";

        if (num_of_component <= 20) {

            $.ajax({

                url: ajaxaction + "&configure=kbmobileapp&assign_component_id=true",

                data: 'component_type=' + a + '&id_layout=' + id_layout,

                type: "post",

                success: function (data)

                {

                    var id = data;

                    if (id) {

                        var product_Grid_html = $('.product-grid').html();

                        var id_layout = $('#id_layout').val();

                        product_Grid_html = product_Grid_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);

                        product_Grid_html = product_Grid_html.replace(/product_grid_edit_component/g, 'edit_' + id);

                        product_Grid_html = product_Grid_html.replace(/product_grid_delete_component/g, 'delete_' + id);
                        product_Grid_html = product_Grid_html.replace(/productGrid_comp_heading/g, 'productGrid_comp_heading_' + id_layout + '_' + id);
                        product_Grid_html = product_Grid_html.replace(/product_grid_list/g, 'product_grid_list_' + id_layout + '_' + id);
                        $('.slides').append(product_Grid_html);

                        preview_content();

                        scrollToBottom();

                        showSuccessMessage(component_add);

                    }

                }

            });



        } else {

            showErrorMessage(limit_reached);

        }

    }

}

function addLastAccessed(id) {

    if (id) {

        num_of_component = parseInt($('#number_of_component').val());

        num_of_component = num_of_component + 1;

        $('#number_of_component').val(num_of_component);

        var last_accessed_html = $('.product-lastAccess').html();

        var id_layout = $('#id_layout').val();

        last_accessed_html = last_accessed_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);

        last_accessed_html = last_accessed_html.replace(/last_access_delete_component/g, 'delete_' + id);

        $('.slides').append(last_accessed_html);

        preview_content();

        scrollToBottom();

    } else {

        num_of_component = parseInt($('#number_of_component').val());

        num_of_component = num_of_component + 1;

        var id_layout = $('#id_layout').val();

        $('#number_of_component').val(num_of_component);

        var a = "products_recent";

        if (num_of_component <= 20) {

            $.ajax({

                url: ajaxaction + "&configure=kbmobileapp&assign_component_id=true",

                data: 'component_type=' + a + '&id_layout=' + id_layout,

                type: "post",

                success: function (data)

                {

                    var id = data;

                    if (id) {

                        var last_accessed_html = $('.product-lastAccess').html();

                        var id_layout = $('#id_layout').val();

                        last_accessed_html = last_accessed_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);

                        last_accessed_html = last_accessed_html.replace(/last_access_delete_component/g, 'delete_' + id);

                        $('.slides').append(last_accessed_html);

                        preview_content();

                        scrollToBottom();

                        showSuccessMessage(component_add);

                    }

                }

            });



        } else {

            showErrorMessage(limit_reached);

        }

    }



}



function trashFunction(trash) {

    num_of_component = parseInt($('#number_of_component').val());

    num_of_component = num_of_component - 1;

    $('#number_of_component').val(num_of_component);

    $(trash).parents('.slide').remove();

    preview_content();

}



function preview_content() {

    $('.iframe_html').html('');

    var Display_content = $('.slides').html();

    $('.iframe_html').append(Display_content);
    /*start:changes made by Aayushi Agarwal on 27th March 2020 to make preview changes*/
    if( $.trim($('.iframe_html').html()) == '') {
        $('.iframe_html').append('<img class="pic" src="' + home_pic_gif + '"  style="width:100%; height:484px">');
    };
    /*end:changes made by Aayushi Agarwal on 27th March 2020 to make preview changes*/   
}





function scrollToBottom() {



    var content = jQuery(".layout_gallery"), autoScrollTimer = 200, autoScrollTimerAdjust, autoScroll;



    content.mCustomScrollbar({

        scrollButtons: {

            enable: true

        },

        theme: "dark",

        callbacks: {

            whileScrolling: function () {

                autoScrollTimerAdjust = autoScrollTimer * this.mcs.topPct / 100;

                privateTop = this.mcs.topPct;

                if (privateTop >= 90) {

                    jQuery('.goToLastMessage').hide();

                    count = 0;

                }



            },

            onScroll: function () {

                if (jQuery(this).data("mCS").trigger === "internal") {

                    AutoScrollOff();



                }

            }

        }

    });



    content.addClass("auto-scrolling-on auto-scrolling-to-bottom");

    AutoScrollOn("bottom");



    function AutoScrollOn(to, timer) {



        if (!timer) {

            timer = autoScrollTimer;

        }

        content.addClass("auto-scrolling-on").mCustomScrollbar("scrollTo", to, {

            scrollInertia: timer,

            scrollEasing: "easeInOutSmooth"

        });



    }

    function AutoScrollOff() {

        clearTimeout(autoScroll);

        content.removeClass("auto-scrolling-on").mCustomScrollbar("stop");

    }

}

function addLayoutComponents(b) {

    for (i = 0; i < b.length; i++) {

        if (b[i]['type'] == 'banner_square') {

            addBannerSquare(b[i]['id']);

        } else if (b[i]['type'] == 'top_category') {

            addTopCategory(b[i]['id']);

        } else if (b[i]['type'] == 'banners_countdown') {

            addBannerCountdown(b[i]['id']);

        } else if (b[i]['type'] == 'products_square') {

            addProductSquare(b[i]['id']);

        } else if (b[i]['type'] == 'products_grid') {

            addProductGrid(b[i]['id']);

        } else if (b[i]['type'] == 'products_recent') {

            addLastAccessed(b[i]['id']);

        } else if (b[i]['type'] == 'banners_grid') {

            addBannergrid(b[i]['id']);

        } else if (b[i]['type'] == 'banner_horizontal_slider') {

            addBannerHorizontalslide(b[i]['id']);

        } else if (b[i]['type'] == 'products_horizontal') {

            addProductHorizontalslide(b[i]['id']);

        }

    }

}

function editLayout(a)

{

    $('.slides').empty();

    var id_layout = jQuery.trim($(a).closest('tr').find('.td-vss-code').html());

    $.ajax({

        url: ajaxaction + "&configure=kbmobileapp&getlayoutComponent=true",

        data: 'id_layout=' + id_layout,

        type: "post",

        success: function (data)

        {

            var b = JSON.parse(data);

            $('#id_layout').val(id_layout);

            if (b.length > 0) {

                $('.slides').html('');

                $('.iframe_html').html('');

                addLayoutComponents(b);

            } else {

                $('.slides').html('');

                $('.iframe_html').html('');

            }

            $('#kbmobileapp_configuration_form').hide();

            $('.layout_add_edit_form').slideDown("fast", function () {

                $('#id_layout').val(id_layout);

                $('#add_new').html(add_new_entry);

                $('#add_new').attr('onclick', 'closelayoutForm()');

                $('#form-configuration .panel h3').remove("#vss-button");

                $('#vss-button').appendTo($("#configuration_form .panel-heading"));

            });

            $(".slides").sortable();

        }

    });

    showUrlImage();

    //getproductdata();

}



function addImage() {

    
    $('.kb_error_message').remove();

    var banner_errors = false;

    

    var id_component= $('#id_component_selected').val();

    var id_layout = $('#id_layout').val();

    var component_heading = $('#bs_component_heading').val();;

    var image_url = $('#input-bs-image').val();

    var image_content_mode = $('#bs-image-content-mode').val();

    var redirect_activity = $('#input-bs-redirect_activity').val();

    var link_to = $('#input-bs-activity_link_to').val();
    var bs_image_row = $('#bs_image_row').val();
    if(bs_image_row==null){
        bs_image_row=0;
    }
    
    if(image_url==""){
        banner_errors = true;
        $('#input-bs-image').after('<br><span class="kb_error_message">Please select a image for banner</span>');    
    }
    if(redirect_activity==""){
        banner_errors = true;
        $('#input-bs-redirect_activity').after('<span class="kb_error_message">Please select Redirect Activity</span>');
    } 
    
    if(!banner_errors) {

        $.ajax({
            url: save_element_ajaxaction + "&saveBannerSquare=true",
            data: 'id_layout=' + id_layout + '&id_component=' + id_component + '&image_url=' + image_url + '&image_content_mode=' + image_content_mode + '&redirect_activity=' + redirect_activity + '&link_to=' + link_to + '&component_heading=' + component_heading + '&bs_image_row=' + bs_image_row,
            type: "post",
            success: function (data){
                $('#bs_images tbody').append(data);
                showSuccessMessage(banner_success_message);
                    $('#input-bs-image').val('');
                    $('#bs-image-src').attr('src','view/image/no_image.png');
                    $('#bs-image-content-mode').val(1);
                    $('#input-bs-redirect_activity').val('');
                    $("#div-bs-activity_link_to").hide('slow');
                    
                    $.ajax({
                        url: get_componenttype_ajaxaction,
                        data: 'id_layout=' + id_layout + '&id_component=' + id_component,
                        type: "post",
                        success: function (data){
                            var b = JSON.parse(data);
                            i=0;
                            if(b.type == 'banner_horizontal_slider') {
                               $.each(b, function( key, value ) {
                                    var key_value = 0;
                                    var html = "";
                                    if(key == 'component_data') {
                                        $('.iframe_html #slidingBList_'+ id_layout+ '_'+ id_component).html('');
                                        $.each(value, function(sub_key, sub_value) {
                                            key_value = sub_key+1;
                                            if(key_value<3) {
                                            html += '<div class="bannerHorizontalSlideList" id="bannerHorizontalSlideList_'+ key_value +'">';
                                                html += '<span class="BHSSection">';   
                                                    html += '<img id="bannerHorizontalSlideimg_'+ id_layout+ '_'+ id_component+ '_' + key_value +'" class="bannerHorizontalSlideimg_'+ id_layout+ '_'+ id_component+ '_' + key_value +'" src="../image/'+sub_value.image_url+'">';
                                                html += '</span>';
                                            html += '</div>';
                                            }
                                        });
                                       
                                        $('.iframe_html #slidingBList_'+ id_layout+ '_'+ id_component).html(html);
                                        $('#layout_component_'+ id_layout+ '_'+ id_component +' #slidingBList_'+ id_layout+ '_'+ id_component).html('');
                                        $('#layout_component_'+ id_layout+ '_'+ id_component +' #slidingBList_'+ id_layout+ '_'+ id_component).html(html);
                                    }
                                });
                            } else if(b.type == 'banners_grid') {
                                $.each(b, function(key, value ) {
                                    var key_value = 0;
                                    if(key == 'component_data') {
                                        $('#layout_component_'+ id_layout+ '_'+ id_component+' #bannerGridimg2').show();
                                        $('#layout_component_'+ id_layout+ '_'+ id_component+' #bannerGridimg3').show();
                                        $('#layout_component_'+ id_layout+ '_'+ id_component+' #bannerGridimg4').show();
                                        $('#layout_component_'+ id_layout+ '_'+ id_component+' #bannerGridimg1').show();
                                        $.each(value, function(sub_key, sub_value) {
                                            key_value = sub_key+1;
                                            $('#layout_component_'+ id_layout+ '_'+ id_component+' img[id="bannerGridimg'+ key_value  + '"]').attr('src', '../image/'+sub_value.image_url);
                                        });
                                        if(key_value==1){
                                            $('#layout_component_'+ id_layout+ '_'+ id_component+' #bannerGridimg2').hide();
                                            $('#layout_component_'+ id_layout+ '_'+ id_component+' #bannerGridimg3').hide();
                                            $('#layout_component_'+ id_layout+ '_'+ id_component+' #bannerGridimg4').hide();
                                        } else if (key_value==2) {
                                            $('#layout_component_'+ id_layout+ '_'+ id_component+' #bannerGridimg3').hide();
                                            $('#layout_component_'+ id_layout+ '_'+ id_component+' #bannerGridimg4').hide();
                                        }else if (key_value==3) {
                                            $('#layout_component_'+ id_layout+ '_'+ id_component+' #bannerGridimg4').hide();
                                        }
                                    }                           
                                });
                            } else if(b.type == 'banner_square') {
                                $.each(b, function( key, value ) {
                                    var key_value = 0;
                                    if(key == 'component_data') {
                                        $('.iframe_html #bannerSList_'+ id_layout+ '_'+ id_component).html('');
                                        var html = "";
                                        $.each(value, function(sub_key, sub_value) {
                                            key_value = sub_key+1;
                                            html += '<span class="BSSection" style="padding-top:1em;">';
                                                html +='<img id="bannerSquareBannerimg_'+ id_layout+ '_'+ id_component + '_'+ key_value+'" class="bannerSquareBannerimg_'+ id_layout+ '_'+ id_component+'" src="../image/'+sub_value.image_url+'" style="width: 100%">';
                                            html +='</span>';
    
                                        });
                                        $('.iframe_html #bannerSList_'+ id_layout+ '_'+ id_component).html(html);
                                        $('#layout_component_'+ id_layout+ '_'+ id_component +' #bannerSList_'+ id_layout+ '_'+ id_component).html('');
                                        $('#layout_component_'+ id_layout+ '_'+ id_component +' #bannerSList_'+ id_layout+ '_'+ id_component).html(html);
}
                                });
                            }
                        }
                        
                    });
            }
        });
    }
    
}
function deleteImage(delete_image_row) {
    
    var id_component= $('#id_component_selected').val();
    var id_layout = $('#id_layout').val();
    var image_url = $('#input-bs-image').val();
    var redirect_activity = $('#input-bs-redirect_activity').val();
    var link_to = $('#input-bs-activity_link_to').val();
    var banner_id = $('#bs-image-id-'+delete_image_row+'').val();
    $.ajax({
        url: deleteImage_ajaxaction,
        data: 'id_layout=' + id_layout + '&id_component=' + id_component + '&banner_id=' + banner_id,
        type: "post",
        success: function (data){
            $('#bs-image-'+delete_image_row+'').remove();
            showSuccessMessage(banner_delete_message);
            $.ajax({
                        url: get_componenttype_ajaxaction,
                        data: 'id_layout=' + id_layout + '&id_component=' + id_component,
                        type: "post",
                        success: function (data){
                            var b = JSON.parse(data);
                            i=0;
                            if(b.type == 'banner_horizontal_slider') {
                               $.each(b, function( key, value ) {
                                    var key_value = 0;
                                    var html = "";
                                    if(key == 'component_data') {
                                        $('.iframe_html #slidingBList_'+ id_layout+ '_'+ id_component).html('');
                                        $.each(value, function(sub_key, sub_value) {
                                            key_value = sub_key+1;
                                            if(key_value<3) {
                                            if(typeof sub_value.image_url === 'undefined' || sub_value.image_url === null) {
                                                html += '<div class="bannerHorizontalSlideList" id="bannerHorizontalSlideList1">';
                                                    html += '<span class="BHSSection">';   
                                                        html += '<img id="bannerHorizontalSlideimg_'+ id_layout+ '_'+ id_component+ '_1" class="bannerHorizontalSlideimg_'+ id_layout+ '_'+ id_component+ '_1" src="view/image/Layout_Components/BHS1.jpg">';
                                                    html += '</span>';
                                                html += '</div>';
                                                html += '<div class="bannerHorizontalSlideList" id="bannerHorizontalSlideList2">';
                                                    html += '<span class="BHSSection">';   
                                                        html += '<img id="bannerHorizontalSlideimg_'+ id_layout+ '_'+ id_component+ '_2" class="bannerHorizontalSlideimg_'+ id_layout+ '_'+ id_component+ '_2" src="view/image/Layout_Components/BHS2.jpg">';
                                                    html += '</span>';
                                                html += '</div>';
                                            } else {
                                                html += '<div class="bannerHorizontalSlideList" id="bannerHorizontalSlideList_'+ key_value +'">';
                                                    html += '<span class="BHSSection">';   
                                                        html += '<img id="bannerHorizontalSlideimg_'+ id_layout+ '_'+ id_component+ '_' + key_value +'" class="bannerHorizontalSlideimg_'+ id_layout+ '_'+ id_component+ '_' + key_value +'" src="../image/'+sub_value.image_url+'">';
                                                    html += '</span>';
                                                html += '</div>';
        }
    }
    });
                                        $('.iframe_html #slidingBList_'+ id_layout+ '_'+ id_component).html(html);
                                        $('#layout_component_'+ id_layout+ '_'+ id_component +' #slidingBList_'+ id_layout+ '_'+ id_component).html('');
                                        $('#layout_component_'+ id_layout+ '_'+ id_component +' #slidingBList_'+ id_layout+ '_'+ id_component).html(html);
                                    }
                                });
                            } else if(b.type == 'banners_grid') {
                                $.each(b, function(key, value ) {
                                    var key_value = 0;
                                    if(key == 'component_data') {
                                        $('#layout_component_'+ id_layout+ '_'+ id_component+' #bannerGridimg2').show();
                                        $('#layout_component_'+ id_layout+ '_'+ id_component+' #bannerGridimg3').show();
                                        $('#layout_component_'+ id_layout+ '_'+ id_component+' #bannerGridimg4').show();
                                        $('#layout_component_'+ id_layout+ '_'+ id_component+' #bannerGridimg1').show();    
                                        $.each(value, function(sub_key, sub_value) {
                                            key_value = sub_key+1;
                                            if(typeof sub_value.image_url === 'undefined' || sub_value.image_url === null) {
                                                $('#layout_component_'+ id_layout+ '_'+ id_component+' img[id="bannerGridimg1"]').attr('src','view/image/Layout_Components/BS1.jpg');
                                                $('#layout_component_'+ id_layout+ '_'+ id_component+' img[id="bannerGridimg2"]').attr('src','view/image/Layout_Components/BS2.jpg');
                                                $('#layout_component_'+ id_layout+ '_'+ id_component+' img[id="bannerGridimg3"]').attr('src','view/image/Layout_Components/BS3.jpg');
                                                $('#layout_component_'+ id_layout+ '_'+ id_component+' img[id="bannerGridimg4"]').attr('src','view/image/Layout_Components/BS4.jpg');
                                                key_value = 0;
                                            } else {
                                                $('#layout_component_'+ id_layout+ '_'+ id_component+' img[id="bannerGridimg'+ key_value  + '"]').attr('src', '../image/'+sub_value.image_url);
                                            }    
                                        });
                                        if(key_value==1){
                                            $('#layout_component_'+ id_layout+ '_'+ id_component+' #bannerGridimg2').hide();
                                            $('#layout_component_'+ id_layout+ '_'+ id_component+' #bannerGridimg3').hide();
                                            $('#layout_component_'+ id_layout+ '_'+ id_component+' #bannerGridimg4').hide();
                                        } else if (key_value==2) {
                                            $('#layout_component_'+ id_layout+ '_'+ id_component+' #bannerGridimg3').hide();
                                            $('#layout_component_'+ id_layout+ '_'+ id_component+' #bannerGridimg4').hide();
                                        }else if (key_value==3) {
                                            $('#layout_component_'+ id_layout+ '_'+ id_component+' #bannerGridimg2').hide();
                                        }
                                    }                           
                                });
                            } else if(b.type == 'banner_square') {
                                $.each(b, function( key, value ) {
                                    var key_value = 0;
    
                                    if(key == 'component_data') {
                                        $('.iframe_html #bannerSList_'+ id_layout+ '_'+ id_component).html('');
                                        var html = "";
                                        $.each(value, function(sub_key, sub_value) {
                                            if(typeof sub_value.image_url === 'undefined' || sub_value.image_url === null) {
                                                key_value = sub_key+1;
                                                html += '<span class="BSSection" style="padding-top:1em;">';
                                                    html +='<img id="bannerSquareBannerimg_'+ id_layout+ '_'+ id_component + '_'+ key_value+'" class="bannerSquareBannerimg_'+ id_layout+ '_'+ id_component+'" src="view/image/Layout_Components/square_banner.jpg" style="width: 100%">';
                                                html +='</span>';
                                            } else {
                                                key_value = sub_key+1;
                                                html += '<span class="BSSection" style="padding-top:1em;">';
                                                    html +='<img id="bannerSquareBannerimg_'+ id_layout+ '_'+ id_component + '_'+ key_value+'" class="bannerSquareBannerimg_'+ id_layout+ '_'+ id_component+'" src="../image/'+sub_value.image_url+'" style="width: 100%">';
                                                html +='</span>';
}
                                        });
                                        $('.iframe_html #bannerSList_'+ id_layout+ '_'+ id_component).html(html);
                                        $('#layout_component_'+ id_layout+ '_'+ id_component +' #bannerSList_'+ id_layout+ '_'+ id_component).html('');
                                        $('#layout_component_'+ id_layout+ '_'+ id_component +' #bannerSList_'+ id_layout+ '_'+ id_component).html(html);
                                    }
                                });
                            }
                        }
                        
                    });
            
            
        }
    });
    
}

var hasOwnProperty = Object.prototype.hasOwnProperty;

function isEmpty(obj) {

    // null and undefined are "empty"
    if (obj == null) return true;

    // Assume if it has a length property with a non-zero value
    // that that property is correct.
    if (obj.length > 0)    return false;
    if (obj.length === 0)  return true;

    // If it isn't an object at this point
    // it is empty, but it can't be anything *but* empty
    // Is it empty?  Depends on your application.
    if (typeof obj !== "object") return true;

    // Otherwise, does it have any properties of its own?
    // Note that this doesn't handle
    // toString and valueOf enumeration bugs in IE < 9
    for (var key in obj) {
        if (hasOwnProperty.call(obj, key)) return false;
    }

    return true;
}
function deleteBCImage(delete_image_row) {
    
    var id_component= $('#id_component_selected').val();
    var id_layout = $('#id_layout').val();
    var component_heading = $('#bs_component_heading').val();;
    var image_url = $('#input-bs-image').val();
    var image_content_mode = $('#bs-image-content-mode').val();
    var redirect_activity = $('#input-bs-redirect_activity').val();
    var link_to = $('#input-bs-activity_link_to').val();
    var banner_id = $('#bc-image-id-'+delete_image_row+'').val();
    
    $.ajax({
        url: deleteImage_ajaxaction,
        data: 'id_layout=' + id_layout + '&id_component=' + id_component + '&banner_id=' + banner_id,
        type: "post",
        success: function (data){
            $('#bc-image-'+delete_image_row+'').remove();
            $.ajax({
                        url: get_componenttype_ajaxaction,
                        data: 'id_layout=' + id_layout + '&id_component=' + id_component,
                        type: "post",
                        success: function (data){
                            var b = JSON.parse(data);
                            if (b.type == 'banners_countdown') {
                               $.each(b, function( key, value ) {
                                    var key_value = 0;
                                    var html = "";
                                    if(key == 'component_data') {
                                        $('.iframe_html #bannerCountdownList_'+ id_layout+ '_'+ id_component).html('');
                                        html += '<div class="countdownlistContent">';
                                        $.each(value, function(sub_key, sub_value) {
                                            key_value = sub_key+1;
                                            if(typeof sub_value.image_url === 'undefined' || sub_value.image_url === null) {
                                                 html += '<div class="countdownBackground" id="bannerCountdownimg" style="background:url(\'view/image/Layout_Components/flash-sale.jpg\')">';
                                                         html += '<div id="days"></div>';
                                                         html += '<div class="countDownTimer" style="background:transparent;">';
                                                                 html += '<span class="timer">23 Hours</span>';
                                                                 html += '<span class="timer">21 Minutes</span>';
                                                                 html += '<span class="timer">49 Seconds</span>';
                                                         html += '</div>';
                                                 html += '</div>';
                                            } else {
                                                html += '<div class="countdownBackground" id="bannerCountdownimg" style="background:url(\'../image/'+ sub_value.image_url +'\');background-size: 290px 220px;">';
                                                    html += '<div id="days"></div>';
                                                        if(sub_value.is_enabled_background_color) {
                                                            html += '<div class="countDownTimer" style="background:'+ sub_value.background_color +'">';
                                                        } else {
                                                            html += '<div class="countDownTimer" style="background:transparent;">';
                                                        }
                                                                html += '<span class="timer" style="color:'+ sub_value.text_color +'">23 Hours</span>';
                                                                html += '<span class="timer" style="color:'+ sub_value.text_color +'">21 Minutes</span>';
                                                                html += '<span class="timer" style="color:'+ sub_value.text_color +'">49 Seconds</span>';
                                                        html += '</div>';
                                                    html += '</div>';
                                            }
                                        });
                                        html += '</div>';
                                        $('.iframe_html #bannerCountdownList_'+ id_layout+ '_'+ id_component).html(html);
                                        $('#layout_component_'+ id_layout+ '_'+ id_component +' #bannerCountdownList_'+ id_layout+ '_'+ id_component).html('');
                                        $('#layout_component_'+ id_layout+ '_'+ id_component +' #bannerCountdownList_'+ id_layout+ '_'+ id_component).html(html);
                                    }
                                });
                            }
                            
                        }
                    });
            showSuccessMessage(banner_delete_message);
        }
    });
    
}

function addBCImage() {
    
        var id_component= $('#id_component_selected').val();
        var id_layout = $('#id_layout').val();
        var component_heading = $('#bc_component_heading').val();;
        var image_url = $('#input-bc-image').val();
        var image_content_mode = $('#bc-image-content-mode').val();
        var redirect_activity = $('#input-bc-redirect_activity').val();
        var link_to = $('#input-bc-activity_link_to').val();
        var bc_image_row = $('#bc_image_row').val();
        var bc_background_color = $('#color_9').val();
        var bc_text_color = $('#color_10').val();
        var bc_countdown_validity = $('input[name="bc_countdown_validity"]').val();
        var bc_back_color_status = $('#bc-background-color-status').val();
        
        $('.kb_error_message').remove();
        
        if(bc_image_row == null){
            bc_image_row=0;
        }
        
        error = false;
        
        if(redirect_activity == "") {
            error = true;
            $('#input-bc-redirect_activity').after('<span class="kb_error_message">' + select_redirect_activity_error + '</span>');
        } 
        if(bc_countdown_validity==""){
            error = true;
            $('input[name="bc_countdown_validity"]').after('<span class="kb_error_message">' + error_countdown + '</span>');
            
        }
        if(bc_back_color_status=="1" && bc_background_color==""){
            error = true;
            $('#color_9').after('<span class="kb_error_message">' + error_background_color + '</span>');
            
        }
        if(bc_text_color==""){
            error = true;
            $('#color_10').after('<span class="kb_error_message">' + error_text_color + '</span>');
        }

        

        if(error) {

            showErrorMessage(error_check_message);

            

        } else {

            var fd = new FormData();



            fd.append('id_layout', id_layout);

            fd.append('id_component', id_component);

            fd.append('image_url', image_url);

            fd.append('image_content_mode', image_content_mode);

            fd.append('redirect_activity', redirect_activity);

            fd.append('link_to', link_to);

            fd.append('bc_image_row', bc_image_row);

            fd.append('bc_background_color', bc_background_color);

            fd.append('bc_text_color', bc_text_color);

            fd.append('bc_countdown_validity', bc_countdown_validity);

            fd.append('bc_back_color_status', bc_back_color_status);

            $.ajax({

                url: save_element_ajaxaction + "&saveBannerCountdown=true",

                data: fd,

                type: "post",

                processData: false,

                contentType: false,

                success: function (data){

                    $('#bc_images tbody').append(data);

                    showSuccessMessage(banner_success_message);
                    $('#input-bc-image').val('');
                    $('#bc-image-content-mode').val(1);
                    $('#input-bc-redirect_activity').val('');
                    $('#input-bc-activity_link_to').val('');
                    $('#div-bc-activity_link_to').hide('slow');
                    $('#bc-image-src').attr('src','view/image/no_image.png');
                    $('#color_9').val('');
                    $('#color_10').val('');
                    $('input[name="bc_countdown_validity"]').val('');
                    $('#bc-background-color-status').val('0');
                    
                    $.ajax({
                        url: get_componenttype_ajaxaction,
                        data: 'id_layout=' + id_layout + '&id_component=' + id_component,
                        type: "post",
                        success: function (data){
                            var b = JSON.parse(data);
                            if(b.type == 'banners_countdown') {
                               $.each(b, function( key, value ) {
                                    var key_value = 0;
                                    var html = "";
                                    if(key == 'component_data') {
                                        $('.iframe_html #bannerCountdownList_'+ id_layout+ '_'+ id_component).html('');
                                        html += '<div class="countdownlistContent">';
                                        $.each(value, function(sub_key, sub_value) {
                                            key_value = sub_key+1;
                                                html += '<div class="countdownBackground" id="bannerCountdownimg" style="background:url(\'../image/'+ sub_value.image_url +'\');background-size: 290px 220px;">';
                                                    html += '<div id="days"></div>';
                                                        if(sub_value.is_enabled_background_color) {
                                                            html += '<div class="countDownTimer" style="background:'+ sub_value.background_color +'">';
                                                        } else {
                                                            html += '<div class="countDownTimer" style="background:transparent;">';
                                                        }
                                                                html += '<span class="timer" style="color:'+ sub_value.text_color +'">23 Hours</span>';
                                                                html += '<span class="timer" style="color:'+ sub_value.text_color +'">21 Minutes</span>';
                                                                html += '<span class="timer" style="color:'+ sub_value.text_color +'">49 Seconds</span>';
                                                        html += '</div>';
                                                    html += '</div>';
                                        });
                                        html += '</div>';
                                        $('.iframe_html #bannerCountdownList_'+ id_layout+ '_'+ id_component).html(html);
                                        $('#layout_component_'+ id_layout+ '_'+ id_component +' #bannerCountdownList_'+ id_layout+ '_'+ id_component).html('');
                                        $('#layout_component_'+ id_layout+ '_'+ id_component +' #bannerCountdownList_'+ id_layout+ '_'+ id_component).html(html);
                }
            });
        }
    
}
                    });

                }
            });
        }

}


function countdownbannerDatepicker() {

    $('.datetimepicker').click(function () {

        $('.ui-datepicker').css('z-index', '99999999');

    });

}

function editBannerSquareComponentFunction(a)

{

    var str = $(a).attr('id');

    var array = str.split("_");

    var id_component = array[1];

    var id_layout = $('#id_layout').val();

    $('#id_component_selected').val(id_component);

    var component_heading = $('#component_heading_'+id_component).val();

    $('#bs_component_heading').val(component_heading);

    $.ajax({

        url: get_component_ajaxaction + "&configure=kbmobileapp&getBannerForm=true",

        data: 'id_layout=' + id_layout + '&id_component=' + id_component,

        type: "post",

        beforeSend: function () {

                $('#BannerSquareModal').modal({

                    show: 'true',

                });

            },

        success: function (data)

        {   

            $('.kb_error_message').remove();
            
            for(l=0;l<active_languages.length;l++) {

                $('#banner-heading-'+active_languages[l]).val('');

            }

            

            var b = [null];

            var b = JSON.parse(data);

            

            if(b[0]['heading'].length > 0){

                for (k = 0; k < b[0]['heading'].length; k++) {

                    $('#banner-heading-'+b[0]['heading'][k]['language_id']).val(b[0]['heading'][k]['heading']);

                }

            }

            

            html='';

            $('#bs_images tbody').text(html);

            if (b.length > 0) {

                var i = 0;

                for (i = 0; i < b.length; i++) {

                    var j = i+1;

                    html +='<tr id="bs-image-'+i+'">';

                        html +='<td>'+j+'</td>';                                                                                           

                        html +='<td class="text-center">';

                        if (b[i]['image_url'].length > 0) { 

                        html +='<img src="../image/'+ b[i]['image_url'] +'" class="img-thumbnail" style="width:60px !important; height:60px !important;" />';

                        } else {

                        html +='<span class="img-thumbnail list"><i class="fa fa-camera fa-2x"></i></span>';

                        }                                                                                           

                        html +='<td>'+ b[i]['redirect_activity']+'</td>';                                                                                           

                        html +='<td>'+b[i]['category_id']+'</td>';                                                                                           

                        html +='<td>'+ b[i]['product_id'] +'</td>';                                                                                           

                        html +='<td>';

                        html +='<input type="hidden" value="'+b[i]['id']+'" id="bs-image-id-'+i+'"/>';

                        html +='<button type="button" onclick="deleteImage('+i+');" data-toggle="tooltip" title="" class="btn btn-danger" data-original-title="Delete Banner"><i class="fa fa-minus"></i></button>';

                        html +='</tr>';

                }

                html +='<input type="hidden" name="bs_image_row" value="'+i+'" id="bs_image_row"';

               

                $('#bs_images tbody').append(html);

            }

        }

    });

}

function showHidebackgroundColor() {

    if ($('input[name="is_enabled_background_color"]:checked').val() == "1") {

        $('input[name="timer_background_color"]').parent().parent().show();

    } else {

        $('input[name="timer_background_color"]').parent().parent().hide();

    }

}

function editBannerCountdownComponentFunction(a)

{

    var str = $(a).attr('id');

    var array = str.split("_");

    var id_component = array[1];

    var id_layout = $('#id_layout').val();

    $('#id_component_selected').val(id_component);

    $.ajax({

        url: get_component_ajaxaction + "&configure=kbmobileapp&getBannerCountdownForm=true",

        data: 'id_layout=' + id_layout + '&id_component=' + id_component,

        type: "post",

        beforeSend: function () {

            $('#BannerCountdownModal').modal({

                show: 'true',

            });



        },

        success: function (data)

        {   

            $('.kb_error_message').remove();

            for(l=0;l<active_languages.length;l++) {

                $('#countdown-heading-'+active_languages[l]).val('');

            }

            var b = [null];

            var b = JSON.parse(data);

            

            if(b[0]['heading'].length > 0){

                for (k = 0; k < b[0]['heading'].length; k++) {

                    $('#countdown-heading-'+b[0]['heading'][k]['language_id']).val(b[0]['heading'][k]['heading']);

                }

            }

            

            html='';

            $('#bc_images tbody').text(html);

            if (b.length > 0) {

                var i = 0;

                for (i = 0; i < b.length; i++) {

                    var j = i+1;

                    html +='<tr id="bc-image-'+i+'">';

                        html +='<td>'+j+'</td>';                                                                                           

                        html +='<td class="text-center">';

                        if (b[i]['image_url'].length > 0) { 

                        html +='<img src="../image/'+ b[i]['image_url'] +'" class="img-thumbnail" style="width:60px !important; height:60px !important;" />';

                        } else {

                        html +='<span class="img-thumbnail list"><i class="fa fa-camera fa-2x"></i></span>';

                        }

                        html +='<td>'+ b[i]['countdown']+'</td>';

                        html +='<td>'+ b[i]['background_color']+'</td>';

                        html +='<td>'+ b[i]['text_color']+'</td>';

                        html +='<td>'+ b[i]['redirect_activity']+'</td>';                                                                                           

                        html +='<td>'+b[i]['category_id']+'</td>';                                                                                           

                        html +='<td>'+ b[i]['product_id'] +'</td>';                                                                                           

                        html +='<td>';

                        html +='<input type="hidden" value="'+b[i]['id']+'" id="bc-image-id-'+i+'"/>';

                        html +='<button type="button" onclick="deleteBCImage('+i+');" data-toggle="tooltip" title="" class="btn btn-danger" data-original-title="Delete Banner"><i class="fa fa-minus"></i></button>';

                    html +='</tr>';

                }

                html +='<input type="hidden" name="bc_image_row" value="'+i+'" id="bc_image_row"';

                $('#bc_images tbody').append(html);

                

            }

        }

    });

    //showHidebackgroundColor();





}

function editLayoutName(a) {

    var id_layout = jQuery.trim($(a).closest('tr').find('.td-vss-code').html());

    $.ajax({

        url: ajaxaction + "&configure=kbmobileapp&getlayoutNameForm=true",

        data: 'id_layout=' + id_layout,

        type: "post",

        beforeSend: function () {

            $('#kbGDPRDialogueModel .modal-body').html('');

            $('#layoutNameModel').modal({

                show: 'true',

            });

            $('.modal-layout-body').html('<img id="loader_module_list" style="text-align: center;width:50px;height:50px;align:center" src=" ' + loader_url + '" alt="" border="0" />');



        },

        success: function (data)

        {

            var b = JSON.parse(data);

            $('#id_layout').val(id_layout);

            $('.modal-layout-body').html('');

            $('.modal-layout-body').append(b.html);



        },

        complete: function () {

            //$('#kbsw_show_loader').hide();

        }

    });

}

function addNewLayout(a) {

    var id_layout = 0;

    $.ajax({

        url: ajaxaction + "&configure=kbmobileapp&getlayoutNameForm=true",

        data: 'id_layout=' + id_layout,

        type: "post",

        beforeSend: function () {

            $('#kbGDPRDialogueModel .modal-body').html('');

            $('#layoutNameModel').modal({

                show: 'true',

            });

            $('.modal-layout-body').html('<img id="loader_module_list" style="text-align: center;width:50px;height:50px;align:center" src=" ' + loader_url + '" alt="" border="0" />');



        },

        success: function (data)

        {

            var b = JSON.parse(data);

            $('.modal-layout-body').html('');

            $('.modal-layout-body').append(b.html);

//            $('#layoutNameModel').modal({

//                show: 'true',

//            });

        }

    });

}

function saveLayoutData(a) {

    var id_layout = $('#layout_id').val();

    var layout_name = $('#layout_title').val();

    $.ajax({

        url: ajaxaction + "&configure=kbmobileapp&savelayoutNameForm=true",

        data: 'id_layout=' + id_layout + '&layout_name=' + layout_name,

        type: "post",

        success: function (data)

        {

            var b = JSON.parse(data);

            if (1) {

                if (id_layout == 0) {

                    showSuccessMessage(layout_add_message);

                } else {

                    showSuccessMessage(layout_name_update_message);

                }

                $('#layout_list').html('');

                $('#layout_list').append(b.html);

                $("#layout_list .panel .panel-heading").append(b.button);

                $('#KBMOBILEAPP_HOME_PAGE_LAYOUT').html('');

                $('#KBMOBILEAPP_HOME_PAGE_LAYOUT').append(b.layout_select_options);

                /* changes by rishabh jain

                 * for layput tab

                 */

                $('#form-kb_layouts_list').addClass('col-lg-10 col-md-9');

                $("#form-kb_layouts_list").css("float", "right");

                $('#layoutNameModel').modal('hide');

                /* changes over */

            }

            return false;

        }

    });

    return false;

}

function veloValidateProductForm(a) {

    

    $('.kb_error_message').remove();

    $('input[name="ps_product_count"]').removeClass('kb_error_field');

    $('select[name="ps_activity_link_to"]').removeClass('kb_error_field');

    $('select[name="custom_product"]').removeClass('kb_error_field');

    $('select[name="ps_product_category"]').removeClass('kb_error_field');

    var product_form_error = false;





    var number_of_product = $.trim($('#number_of_products').val());

    var category_id = $.trim($('#input-ps-activity_link_to').val());
    var product_list = $.trim($('#custom_product').val());
    var category_products = $.trim($('#ps-product-category').val());

    var product_type = $('#input-ps-redirect_activity').val();

    var error_message = '';



    if (product_type == 'from_category') {

        if (category_id == 0) {

            product_form_error = true;

            error_message = select_category_txt;

            $('#input-ps-activity_link_to').addClass('kb_error_field');

            $('#input-ps-activity_link_to').after('<span class="kb_error_message">' + error_message + '</span>');

        }

    } else if (product_type == 'custom_product') {

        if (product_list == '') {

            product_form_error = true;

            error_message = "Atleast select one product from list";

            $('#custom-product').addClass('kb_error_field');

            $('#custom-product').after('<span class="kb_error_message">' + error_message + '</span>');

        }

    }



    var key_numeric_err = velovalidation.isNumeric($('#number_of_products'), true);

    if (key_numeric_err != true)

    {

        product_form_error = true;

        error_message = "Product count should be numeric";

        $('#number_of_products').addClass('kb_error_field');

        $('#number_of_products').after('<span class="kb_error_message">' + error_message + '</span>');

    }





    if (product_form_error) {

        showErrorMessage(error_check_message);

        return false;

    } else {

        showSuccessMessage(success_message);

    }

    submitProductform(event);



    return false;
}
function editProductLAFunction(a)
{
    var str = $(a).attr('id');
    var array = str.split("_");
    var id_component = array[1];
    var id_layout = $('#id_layout').val();
    $('#id_component_selected').val(id_component);
    
    $.ajax({
        url: get_component_ajaxaction + "&configure=kbmobileapp&ProductRecent=true",
        data: 'id_layout=' + id_layout + '&id_component=' + id_component,
        type: "post",
        beforeSend: function () {
            $('#ProductLAModal').modal({
                show: 'true',
            });
            for(l=0;l<active_languages.length;l++) {
                $('#product-recent-'+active_languages[l]).val('');
            }
        },
        success: function (data)
        {   
            var b = JSON.parse(data);
            if(b.length > 0){
                for (k = 0; k < b.length; k++) {
                    $('#product-recent-'+b[k]['language_id']).val(b[k]['heading']);
                }
            }
        }
    });
}
function editProductHorizontalComponentFunction(a)
{   
    
    var str = $(a).attr('id');
    var array = str.split("_");
    var id_component = array[1];
    var id_layout = $('#id_layout').val();
    $('#id_component_selected').val(id_component);
    $.ajax({
        url: get_component_ajaxaction + "&configure=kbmobileapp&getProductForm=true",
        data: 'id_layout=' + id_layout + '&id_component=' + id_component,
        type: "post",
        beforeSend: function () {
            $('#ProductHotizontalModal').modal({
                show: 'true',
            });
        },
        success: function (data)
        {   
            $('.kb_error_message').remove();
            for(l=0;l<active_languages.length;l++) {
                $('#product-heading-'+active_languages[l]).val('');
            }
            
            $("#div-ps-product_link_to").hide('slow');
            $('#input-ps-redirect_activity').val('0');
            $('#number_of_products').val('10');
            $('#ps_image_content_mode').val('1');
            $('#input-ps-redirect_activity').val('0');
            $('#input-ps-activity_link_to').val('');
            $('input[name=\'custom_product\']').attr('value','');
            $('#custom-product').text('');
            $("#div-ps-product_link_to").hide('slow');
            $("#div-custom-product").hide('slow');
            var b = JSON.parse(data);
            
            if(b[0]['heading'].length > 0){

                for (k = 0; k < b[0]['heading'].length; k++) {

                    $('#product-heading-'+b[0]['heading'][k]['language_id']).val(b[0]['heading'][k]['heading']);

                }

            }
            
            if (b.length > 0) {
                $("#div-ps-product_link_to").hide('slow');
                $("#div-custom-product").hide('slow');
                $('#input-ps-redirect_activity').val(b[0]['product_type']);
                $('#number_of_products').val(b[0]['number_of_products']);
                $('#ps_image_content_mode').val(b[0]['image_content_mode']);
                if(b[0]['id_category']>0 && b[0]['product_type']=='from_category'){
                    selectProductRedirectActivity(b[0]['product_type'],b[0]['id_category']);
                    var str = b[0]['category_products'];
                    var c = str.split(",");
                    selectProductCategoryRedirectActivity(b[0]['id_category'],c);
                }
                if (typeof b[0]['custom_products'] !== '' && b[0]['product_type']=='custom_product') {
                    selectProductRedirectActivity(b[0]['product_type'],b[0]['custom_products']);
                }
            }
        }
    });
}

function saveCountdownHeading(){
    var id_layout = $('#id_layout').val();
    var id_component = $('#id_component_selected').val();
    error = false;
    $('.front_preview #bannerCountdown_comp_heading_'+id_layout+'_'+id_component).html($('#countdown-heading-'+active_languages[0]).val());
    for(i=0;i<active_languages.length;i++) {
            $.ajax({
                url: save_heading_ajaxaction,
                data: 'id_layout='+id_layout+'&id_component='+id_component+'&language_id='+active_languages[i]+'&heading='+$('#countdown-heading-'+active_languages[i]).val(),
                type: "post",
            });
    }
    if(!error){
        showSuccessMessage(success_heading_message);
    }
}
function saveBannerHeading(){
    var id_layout = $('#id_layout').val();
    var id_component = $('#id_component_selected').val();
    error = false;
    for(i=0;i<active_languages.length;i++) {
       $.ajax({
            url: save_heading_ajaxaction,
            data: 'id_layout='+id_layout+'&id_component='+id_component+'&language_id='+active_languages[i]+'&heading='+$('#banner-heading-'+active_languages[i]).val(),
            type: "post",
        });
        $('.bannerSquare  #bannerSquare_comp_heading_'+id_layout+'_'+id_component).html($('#banner-heading-'+active_languages[i]).val());
        $('.bannerHorizontalSlide  #bannerHorizontalSlide_comp_heading_'+id_layout+'_'+id_component).html($('#banner-heading-'+active_languages[i]).val());
        $('.bannerGrid  #bannerGrid_comp_heading_'+id_layout+'_'+id_component).html($('#banner-heading-'+active_languages[i]).val());
        
        showSuccessMessage(success_heading_message);
    }
}
function saveProductLAHeading(){
    var id_layout = $('#id_layout').val();
    var id_component = $('#id_component_selected').val();
    error = false;
    for(i=0;i<active_languages.length;i++) {
       $.ajax({
            url: save_heading_ajaxaction,
            data: 'id_layout='+id_layout+'&id_component='+id_component+'&language_id='+active_languages[i]+'&heading='+$('#product-recent-'+active_languages[i]).val(),
            type: "post",
        });
        showSuccessMessage(success_heading_message);
    }

}

function saveProductHeading(){

    var id_layout = $('#id_layout').val();

    var id_component = $('#id_component_selected').val();

    error = false;
    $('.front_preview #productGrid_comp_heading_'+id_layout+'_'+id_component).html($('#product-heading-'+active_languages[0]).val());
    $('.front_preview #slidingProducts_comp_heading_'+id_layout+'_'+id_component).html($('#product-heading-'+active_languages[0]).val());
    $('.front_preview #productSquare_comp_heading_'+id_layout+'_'+id_component).html($('#product-heading-'+active_languages[0]).val());
    for(i=0;i<active_languages.length;i++) {
            $.ajax({
                url: save_heading_ajaxaction,
                data: 'id_layout='+id_layout+'&id_component='+id_component+'&language_id='+active_languages[i]+'&heading='+$('#product-heading-'+active_languages[i]).val(),
                type: "post",
            });
    }
    if(!error){
        showSuccessMessage(success_heading_message);
    }

}

    





function veloValidateTopcategoryForm(a) {

    

    $('.kb_error_message').remove();

    

    for (i = 1; i <= 8; i++) {

        $('#category-list-'+i).removeClass('kb_error_field');

        $('#input-category-image-'+ i).removeClass('kb_error_field');

    }

    

    error = false;

    

    var unselected_cat = 0;

    for (i = 1; i <= 8; i++) {

        if ($('#category-list-' + i).val() == '0') {

            unselected_cat++;

        }

        if ($('#category-list-' + i).val() == '0' && $('#input-category-image-'+ i).val() !='') {

            $('#category-list-'+i).addClass('kb_error_field');

            $('#category-list-'+i).after('<br><span class="kb_error_message">' + select_category_error + '</span>');

            error = true;

        } else if($('#category-list-' + i).val() != '0' && ($('#input-category-image-'+ i).val() =='' || $('#input-category-image-'+ i).val() == undefined)) {

            $('#input-category-image-'+ i).addClass('kb_error_field');

            $('#input-category-image-'+ i).after('<br><span class="kb_error_message">Select image for the category</span>');

            error = true;

        }

    }

    if (unselected_cat > 4) {

        var error = true;

        showErrorMessage(min_category_limit);

    } 

    if (error) {

        showErrorMessage(error_check_message);

        return false;

    } 

    else {
        submitTopCategoryForm();

    }
    return false;
}
function submitTopCategoryForm() {
    var id_layout = $('#id_layout').val();
    var id_component = $('#id_component_selected').val();
    var id_category_1 = $('#category-list-1').val();
    var id_category_2 = $('#category-list-2').val();
    var id_category_3 = $('#category-list-3').val();
    var id_category_4 = $('#category-list-4').val();
    var id_category_5 = $('#category-list-5').val();
    var id_category_6 = $('#category-list-6').val();
    var id_category_7 = $('#category-list-7').val();
    var id_category_8 = $('#category-list-8').val();
    
    // changes
    var image_content_mode = $('#image-content-mode').val();
    // changes started
    var fd = new FormData();
    if ($('#input-category-image-1').val() != undefined && $('#input-category-image-1').val() != '' && id_category_1 != 0) {
        fd.append('image_1', $('#input-category-image-1').val());
    }
    if ($('#input-category-image-2').val() != undefined && $('#input-category-image-2').val() !='' && id_category_2 != 0) {
        fd.append('image_2', $('#input-category-image-2').val());
    }
    if ($('#input-category-image-3').val() != undefined && $('#input-category-image-3').val() !='' && id_category_3 != 0) {
        fd.append('image_3', $('#input-category-image-3').val());
    }
    if ($('#input-category-image-4').val() != undefined && $('#input-category-image-4').val() !='' && id_category_4 != 0) {
        fd.append('image_4', $('#input-category-image-4').val());
    }

    if ($('#input-category-image-5').val() != undefined && $('#input-category-image-5').val() !='' && id_category_5 != 0) {
        fd.append('image_5', $('#input-category-image-5').val());
    }
    if ($('#input-category-image-6').val() != undefined && $('#input-category-image-6').val() !='' && id_category_6 != 0) {
        fd.append('image_6', $('#input-category-image-6').val());

    }

    if ($('#input-category-image-7').val() != undefined && $('#input-category-image-7').val() !='' && id_category_7 != 0) {

        fd.append('image_7', $('#input-category-image-7').val());

    }

    if ($('#input-category-image-8').val() != undefined && $('#input-category-image-8').val() !='' && id_category_8 != 0) {

        fd.append('image_8', $('#input-category-image-8').val());

    }



    fd.append('image_content_mode', image_content_mode);

    fd.append('id_layout', id_layout);

    fd.append('id_component', id_component);

    fd.append('id_category_1', id_category_1);

    fd.append('id_category_2', id_category_2);

    fd.append('id_category_3', id_category_3);

    fd.append('id_category_4', id_category_4);

    fd.append('id_category_5', id_category_5);

    fd.append('id_category_6', id_category_6);

    fd.append('id_category_7', id_category_7);

    fd.append('id_category_8', id_category_8);

    $.ajax({

        url: save_element_ajaxaction + "&saveTopcategoryFormData=true",

        data: fd,

        type: "post",

        processData: false,

        contentType: false,

        beforeSend: function () {

            $('#submitOptionsslider2').prop("disabled", true);



        },

        success: function (data)

        {

            var b = JSON.parse(data);

            if (1) {

                showSuccessMessage(success_message);

                $('#topCategoryModal').modal('hide');
                var i =1;
                $.each(b, function( key, value ) {
                    $('img[id="top_category_'+i+'_'+ id_layout + '_' + id_component + '"]').attr('src', '../image/'+value.image_url);
                    i++;
                });
            }

        }

    });

    return false;

}

function deleteCategoryImage(e) {

    $('#kbGDPRDialogueModel').find('.icon-trash').parent().each(function () {

        var str = $(this).parent().parent().find('.category_image_class').attr('id');

        var src = $("#" + str).attr('src');

        if (src == '') {

            $(this).parent().css("display", "none");

        }

        $(this).bind('click', function () {

            var str = $(this).parent().parent().find('.category_image_class').attr('id');

            var array = str.split("_");

            var id_category_component = array[1];

            var id_component = $('#id_component_selected').val();

            $(this).parent().css("display", "none");

            $.ajax({

                url: ajaxaction + "&configure=kbmobileapp&deleteTopCategoryImage=true",

                data: 'id_category_component=' + id_category_component + '&id_component=' + id_component,

                type: "post",

                success: function (data)

                {

                    if (1) {

                        $("#" + str).attr('src', '');

                        $(this).parent().css("display", "none");

                        //$(this).css("display", "none");

                        showSuccessMessage(category_image_delete_message);

                    }

                }

            });

            return false;

        })

    })



}



function editTopCategoryComponentFunction(a)

{

    var str = $(a).attr('id');

    var array = str.split("_");

    var id_component = array[1];

    var id_layout = $('#id_layout').val();

    $('#id_component_selected').val(id_component);



    for (i = 1; i <= 8; i++) {

        $('#category-list-'+i).val('0');

        $('#input-category-image-'+ i).val('');

        $('#category-image-src-'+ i).attr('src','view/image/no_image.png');

    }





    $.ajax({

        url: get_component_ajaxaction + "&getCategoryForm=true",

        data: 'id_layout=' + id_layout + '&id_component=' + id_component,

        type: "post",

        beforeSend: function () {

            $('#topCategoryModal').modal({

                show: 'true'

            });



        },

        success: function (data)

        {   

            $('.kb_error_message').remove();
            $('.sh').attr('style','display:none');
            var b = JSON.parse(data);
            if (b.length > 0) {
                for (i = 0; i < b.length; i++) {
                    var j = i+1;
                    
                    if(b[i]['id_category'] > 0) {
                        
                        $('[name=category_list_'+j+']').val(b[i]['id_category']);
                        $('#category-image-src-'+j).attr('src','../image/'+b[i]['image_url']);
                        $('[name=category_image_'+j+']').val(b[i]['image_url']);
                        if(j>4){
                            $('#show-hide-category-'+j).attr('style','display:block');
                        } else {
                            /*start:changes made by Aayushi Agarwal on 27th March 2020 to make preview changes*/
                            /*
                             * To append preview in the device for already added categories when any layout is edited.
                             */
                            $('img[id="top_category_'+j+'_'+ id_layout + '_' + id_component + '"]').attr('src', '../image/'+b[i]['image_url']);
                             /*end:changes made by Aayushi Agarwal on 27th March 2020 to make preview changes*/
                        }
                    } else {

                    }
                }
            }
        }

    });

}







function submitbannersliderform(a)

{

    var id_layout = $('#id_layout').val();

    var id_component = $('#id_component_selected').val();

    var image_type = $('#image_type').val();

    var image_url = $('#image_url').val();

    var redirect_activity = $('#redirect_activity').val();

    var category_id = $('#category_id').val();

    var redirect_product_id = $('#redirect_banner_product_id').val();

    var image_content_mode = $('#image_content_mode').val();

    var redirect_product_name = $('#redirect_banner_product_name').val();

    var fd = new FormData();

    fd.append('image', $('#slideruploadedfile')[0].files[0]);

    fd.append('id_layout', id_layout);

    fd.append('id_component', id_component);

    fd.append('category_id', category_id);

    fd.append('redirect_activity', redirect_activity);

    fd.append('image_url', image_url);

    var lang = active_languages;

    for (i = 0; i < lang.length; i++) {

        fd.append('banner_heading_' + lang[i], $('#banner_heading_' + lang[i]).val());

    }

    if ($('#countdown_validity').is(":visible")) {

        fd.append('countdown_validity', $('#countdown_validity').val());

        fd.append('is_enabled_background_color', $('input[name="is_enabled_background_color"]:checked').val());

        fd.append('timer_background_color', $('input[name=timer_background_color]').val());

        fd.append('timer_text_color', $('input[name=timer_text_color]').val());

    }

    fd.append('image_content_mode', image_content_mode);

    fd.append('image_type', image_type);

    fd.append('redirect_product_id', redirect_product_id);

    fd.append('redirect_product_name', redirect_product_name);

    $.ajax({

        url: ajaxaction + "&configure=kbmobileapp&saveBannerSliderFormData=true",

        data: fd,

        type: "post",

        processData: false,

        contentType: false,

        beforeSend: function () {

            $('#kbGDPRDialogueModel').modal({

                show: 'true',

            });

            $('#submitOptionsslider2').prop("disabled", true);



        },

        success: function (data)

        {

            var b = JSON.parse(data);

            if (1) {

                $('#kbGDPRDialogueModel .modal-body').html('');

                $('#kbGDPRDialogueModel .modal-body').empty();

                $('#kbGDPRDialogueModel .modal-body').append(b.html);

                $('#sliderimage').hide();

                //$('#category_id').parent().parent().hide();

                $('#is_enabled_background_color_on').parent().closest('.form-group').hide();

                $('#countdown_validity').closest('.form-group').hide();

                $('.kbsw_wheel_color').closest('.form-group').parents('.form-group').hide();

                $('#redirect_banner_product_name').parent().parent().hide();

                $('#image_url').parent().parent().hide();

                $('#slideruploadedfile').parent().parent().parent().parent().hide();

                $('#confirmation_block_modal').show();

                showSuccessMessage(success_message);

                showUrlImage();

                uploadfile();

                autoCompleteProduct();

            }

        }

    });

    return false;
}
function getCustomProduct(product_id) {
    
    if(product_id) {
        var array = product_id.split(",");
    }
    html = '';
    $.ajax({
        url: 'index.php?route=extension/module/webservice/selectProduct&user_token='+user_token,
        dataType: 'json',
        success: function(json) {
            $.each(json, function(i, item) {
                if($.inArray(item.product_id,array) >= 0) {
                    html += '<option value="' + item.product_id + '" selected>' + item.name + '</option>';   
                } else {
                   html += '<option value="' + item.product_id + '">' + item.name + '</option>';  
                }
            });
            $("#custom_product").html(html);
        }
    });
}
function submitProductform(a)

{

    var id_layout = $('#id_layout').val();

    var id_component = $('#id_component_selected').val();

    var number_of_product = $.trim($('#number_of_products').val());

    var category_id = $.trim($('#input-ps-activity_link_to').val());
    var product_list = $.trim($('#custom_product').val());
    var category_products = $.trim($('#ps-product-category').val());

    var product_type = $('#input-ps-redirect_activity').val();

    var image_content_mode = $('#ps_image_content_mode').val();

    var fd = new FormData();

    fd.append('number_of_product', number_of_product);

    fd.append('id_component', id_component);

    fd.append('category_id', category_id);

    fd.append('id_layout', id_layout);

    fd.append('product_type', product_type);

    fd.append('product_list', product_list);

    fd.append('category_products', category_products);

    fd.append('image_content_mode', image_content_mode);

    $.ajax({

        url: save_element_ajaxaction + "&configure=kbmobileapp&saveProductFormData=true",

        data: fd,

        type: "post",

        processData: false,

        contentType: false,

        beforeSend: function () {

            $('#submitOptionsslider2').prop("disabled", true);

        },

        success: function (data)
        {   
            $.ajax({
                url: get_componenttype_ajaxaction,
                data: 'id_layout=' + id_layout + '&id_component=' + id_component,
                type: "post",
                success: function (data){
                    var b = JSON.parse(data);
                    if(b.type == 'products_square') {
                       $.each(b, function( key, value ) {
                            var key_value = 0;
                            var html = "";
                            $('.iframe_html #product_square_list_'+ id_layout+ '_'+ id_component).html('');
                            if(key == 'products') {
                                $.each(value, function(sub_key, sub_value) {
                                    html += '<div class="productSquareList" id="productSList">';
                                            html += '<img id="productSquareimg" class="productSquareimg" src="'+sub_value.src+'">';
                                            html += '<div class="productContent">';
                                                    html += '<div class="productInfo">';
                                                            html += '<h5 id="productName">'+sub_value.name+'</h5>';
                                                            html += '<h6 id="productPrice" class="productPPrice">'+sub_value.price+'</h6>';
                                                    html += '</div>';
                                                    html += '<div class="wishlistProduct">';
                                                            html += '<i class="fa fa-heart-o"></i>';
                                                    html += '</div>';
                                            html += '</div>';
                                    html += '</div>';
            
                                });
                                $('.iframe_html #product_square_list_'+ id_layout+ '_'+ id_component).html(html);
                                $('#layout_component_'+ id_layout+ '_'+ id_component +' #product_square_list_'+ id_layout+ '_'+ id_component).html('');
                                $('#layout_component_'+ id_layout+ '_'+ id_component +' #product_square_list_'+ id_layout+ '_'+ id_component).html(html);
                            }
                        });
                    } else if (b.type == 'products_horizontal') {
                        $.each(b, function( key, value ) {
                            var key_value = 0;
                            var html = "";
                            $('.iframe_html #product_sliding_banner_'+ id_layout+ '_'+ id_component).html('');
                            if(key == 'products') {
                                html += '<div class="slidingProducts" id="slidingPRow1">';	
                                $.each(value, function(sub_key, sub_value) {
                                    key_value = sub_key +1;
                                    if(key_value < 4) {
                                    html += '<div class="productSlideList" id="productSlideList'+ key_value +'">';
                                        html += '<img class="slidingProductsimg" id="slidingProductsimg'+ key_value +'" src="'+sub_value.src+'">';
                                        html += '<div class="productContent">';
                                            html += '<div class="productInfo">';
                                                html += '<h5 id="productName">'+sub_value.name+'</h5>';
                                                html += '<h6 id="productPrice" class="productPPrice">'+sub_value.price+'</h6>';
                                            html += '</div>';
                                            html += '<div class="wishlistProduct">';
                                                html += '<i class="fa fa-heart-o"></i>';
                                            html += '</div>';
                                        html += '</div>';
                                    html += '</div>';
                                    }
                                });
                                html += '</div>';
                                $('.iframe_html #product_sliding_banner_'+ id_layout+ '_'+ id_component).html(html);
                                $('#layout_component_'+ id_layout+ '_'+ id_component +' #product_sliding_banner_'+ id_layout+ '_'+ id_component).html('');
                                $('#layout_component_'+ id_layout+ '_'+ id_component +' #product_sliding_banner_'+ id_layout+ '_'+ id_component).html(html);
                            }                          
                        });
                    } else if (b.type == 'products_grid') {
                        $.each(b, function( key, value ) {
                            var key_value = 0;
                            var html = "";
                            if(key == 'products') {
                                $('.iframe_html #product_grid_list_'+ id_layout+ '_'+ id_component).html('');
                                html += '<div class="productGridRow1" id="productGRow1">';
                                $.each(value, function(sub_key, sub_value) {
                                    key_value = sub_key + 1;
                                    html += '<div class="productGridList" >';
                                        html += '<img id="productGridimg'+key_value+'"  src="'+sub_value.src+'"/>';
                                        html += '<div class="productContent">';
                                            html += '<div class="productInfo">';
                                                html += '<h5 id ="productName">'+sub_value.name+'</h5>';
                                                html += '<h6 id="productPrice" class="productPPrice">'+sub_value.price+'</h6>';
                                            html += '</div>';
                                            html += '<div class="wishlistProduct">';
                                                html += '<i class="fa fa-heart-o"></i>';
                                            html += '</div>';
                                        html += '</div>';
                                    html += '</div>';
                                });
                                html += '</div>';
                                $('.iframe_html #product_grid_list_'+ id_layout+ '_'+ id_component).html(html);
                                $('#layout_component_'+ id_layout+ '_'+ id_component +' #product_grid_list_'+ id_layout+ '_'+ id_component).html('');
                                $('#layout_component_'+ id_layout+ '_'+ id_component +' #product_grid_list_'+ id_layout+ '_'+ id_component).html(html);
                            }                          
                        });
                    }

                }
            });    
        }
    });
    return false;

}

function submitCountdownbannersliderform(a)

{

    var id_layout = $('#id_layout').val();

    var id_component = $('#id_component_selected').val();

    var image_type = $('#image_type').val();

    var image_url = $('#image_url').val();

    var redirect_activity = $('#redirect_activity').val();

    var category_id = $('#category_id').val();

    var redirect_product_id = $('#redirect_banner_product_id').val();

    var image_content_mode = $('#image_content_mode').val();

    var redirect_product_name = $('#redirect_banner_product_name').val();

    var fd = new FormData();

    fd.append('image', $('#slideruploadedfile')[0].files[0]);

    fd.append('id_layout', id_layout);

    fd.append('id_component', id_component);

    fd.append('category_id', category_id);

    fd.append('redirect_activity', redirect_activity);

    fd.append('image_url', image_url);

    var lang = active_languages;

    for (i = 0; i < lang.length; i++) {

        fd.append('banner_heading_' + lang[i], $('#banner_heading_' + lang[i]).val());

    }

    if ($('#countdown_validity').is(":visible")) {

        fd.append('countdown_validity', $('#countdown_validity').val());

        fd.append('is_enabled_background_color', $('input[name="is_enabled_background_color"]:checked').val());

        fd.append('timer_background_color', $('input[name=timer_background_color]').val());

        fd.append('timer_text_color', $('input[name=timer_text_color]').val());

    }

    fd.append('image_content_mode', image_content_mode);

    fd.append('image_type', image_type);

    fd.append('redirect_product_id', redirect_product_id);

    fd.append('redirect_product_name', redirect_product_name);

    $.ajax({

        url: ajaxaction + "&configure=kbmobileapp&saveBannerSliderFormData=true",

        data: fd,

        type: "post",

        processData: false,

        contentType: false,

        beforeSend: function () {

//            $('#kbGDPRDialogueModel').modal({

//                show: 'true',

//            });

//            $('body').addClass("kb_loading");

            //$('modal-body').addClass("kb_loading");

            // $('#kbGDPRDialogueModel .modal-body').append('<img id="loader_module_list" style="width:50px;height:50px;align:center" src=" ' + loader_url + '" alt="" border="0" />');



        },

        success: function (data)

        {

            var b = JSON.parse(data);

            if (1) {

                $('#kbGDPRDialogueModel .modal-body').html('');

                $('#kbGDPRDialogueModel .modal-body').empty();

                $('#kbGDPRDialogueModel .modal-body').append(b.html);

                $('#redirect_banner_product_name').parent().parent().hide();

                $('#image_url').parent().parent().hide();

                $('#slideruploadedfile').parent().parent().parent().parent().hide();

                $('#confirmation_block_modal').show();

                //$('#kbGDPRDialogueModel').modal('hide');

                showSuccessMessage(success_message);

                showUrlImage();

                uploadfile();

                setDate();

                setColor();

                autoCompleteProduct();

            }

        }

    });

    return false;

}

function delete_banner_slider(a) {

    var a = jQuery.trim($(a).closest('tr').find('.td-vss-code').html());

    var id_component = $('#id_component_selected').val();

    $.ajax({

        url: ajaxaction + "&configure=kbmobileapp&deleteSliderBanner=true",

        data: 'id_banner=' + a + '&id_component=' + id_component,

        type: "post",

        success: function (data)

        {

            var b = JSON.parse(data);

            if (1) {

                $('#kbGDPRDialogueModel .modal-body').html('');

                $('#kbGDPRDialogueModel .modal-body').empty();

                $('#kbGDPRDialogueModel .modal-body').append(b.html);

                $('#component_edit_popup').append(b.html);

                $('#category_id').parent().parent().hide();

                $('#redirect_product_name').parent().parent().hide();

                $('#image_url').parent().parent().hide();

                $('#slideruploadedfile').parent().parent().parent().parent().hide();

                showUrlImage();

                uploadfile();

                showSuccessMessage(banner_delete_message);

                countdownbannerDatepicker();

                //showHidebackgroundColor();

                autoCompleteProduct();

                setDate();

                setColor();

                $.ajax({

                    url: ajaxaction + "&configure=kbmobileapp&getComponentType=true",

                    data: '&id_component=' + id_component,

                    type: "post",

                    success: function (data)

                    {

                        if (data != 'banners_countdown') {

                            $('#is_enabled_background_color_on').parent().closest('.form-group').hide();

                            $('#countdown_validity').closest('.form-group').hide();

                            $('.kbsw_wheel_color').closest('.form-group').parents('.form-group').hide();

                        }

                    }

                });



            }

        }

    });

    showUrlImage();

}



function uploadfile() {

    $('#slideruploadedfile').on('change', function (e) {

        if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)

        {

            var files = $(this)[0].files[0];

            var file_data = e.target.files;

            var file_mimetypes = [

                'image/gif',

                'image/jpeg',

                'image/png',

                'application/x-shockwave-flash',

                'image/psd',

                'image/bmp',

                'image/tiff',

                'application/octet-stream',

                'image/jp2',

                'image/iff',

                'image/vnd.wap.wbmp',

                'image/xbm',

                'image/vnd.microsoft.icon',

                'image/webp'

            ];



            var file_format = false;

            for (i = 0; i < file_mimetypes.length; i++) {

                if (files.type == file_mimetypes[i]) {

                    file_format = true;

                }

            }



            if (!file_format)

            {

                $('input[name="slideruploadedfile"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');

                slider_banner_file_error = true;



            } else if (files.size > default_file_size) {

                $('input[name="slideruploadedfile"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');

                slider_banner_file_error = true;

            } else {

                slider_banner_file_error = false;

                if (typeof (FileReader) != "undefined") {



                    var image_holder = $("#sliderimage");



                    image_holder.empty();



                    var reader = new FileReader();

                    reader.onload = function (e) {

                        $('#sliderimage').attr('src', e.target.result);

                    }

                    image_holder.show();

                    reader.readAsDataURL($(this)[0].files[0]);

                }

                $('input[name="slideruploadedfile"]').parent().find('.kb_error_message').remove();

            }



        }

        else // Internet Explorer 9 Compatibility

        {

            $('#notification_error').html(invalid_file_txt);

            file_error = true;

        }

    });

}

function uploadtopCategoryfile() {

    $('#slideruploadedfile_1').on('change', function (e) {

        if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)

        {

            var files = $(this)[0].files[0];

            var file_data = e.target.files;

            var file_mimetypes = [

                'image/gif',

                'image/jpeg',

                'image/png',

                'application/x-shockwave-flash',

                'image/psd',

                'image/bmp',

                'image/tiff',

                'application/octet-stream',

                'image/jp2',

                'image/iff',

                'image/vnd.wap.wbmp',

                'image/xbm',

                'image/vnd.microsoft.icon',

                'image/webp'

            ];



            var file_format = false;

            for (i = 0; i < file_mimetypes.length; i++) {

                if (files.type == file_mimetypes[i]) {

                    file_format = true;

                }

            }



            if (!file_format)

            {

                $('input[name="slideruploadedfile_"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');

                slider_banner_file_error = true;



            } else if (files.size > default_file_size) {

                $('input[name="slideruploadedfile_1"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');

                slider_banner_file_error = true;

            } else {

                slider_banner_file_error = false;

                if (typeof (FileReader) != "undefined") {



                    var image_holder = $("#sliderimage_1");



                    image_holder.empty();



                    var reader = new FileReader();

                    reader.onload = function (e) {

                        $('#sliderimage_1').attr('src', e.target.result);

                    }

                    image_holder.show();

                    reader.readAsDataURL($(this)[0].files[0]);

                }

                $('input[name="slideruploadedfile_1"]').parent().find('.kb_error_message').remove();

            }



        }

        else

        {

            $('#notification_error').html(invalid_file_txt);

            file_error = true;

        }

    });

    $('#slideruploadedfile_2').on('change', function (e) {

        if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)

        {

            var files = $(this)[0].files[0];

            var file_data = e.target.files;

            var file_mimetypes = [

                'image/gif',

                'image/jpeg',

                'image/png',

                'application/x-shockwave-flash',

                'image/psd',

                'image/bmp',

                'image/tiff',

                'application/octet-stream',

                'image/jp2',

                'image/iff',

                'image/vnd.wap.wbmp',

                'image/xbm',

                'image/vnd.microsoft.icon',

                'image/webp'

            ];



            var file_format = false;

            for (i = 0; i < file_mimetypes.length; i++) {

                if (files.type == file_mimetypes[i]) {

                    file_format = true;

                }

            }



            if (!file_format)

            {

                $('input[name="slideruploadedfile_2"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');

                slider_banner_file_error = true;



            } else if (files.size > default_file_size) {

                $('input[name="slideruploadedfile_2"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');

                slider_banner_file_error = true;

            } else {

                slider_banner_file_error = false;

                if (typeof (FileReader) != "undefined") {



                    var image_holder = $("#sliderimage_2");



                    image_holder.empty();



                    var reader = new FileReader();

                    reader.onload = function (e) {

                        $('#sliderimage_2').attr('src', e.target.result);

                    }

                    image_holder.show();

                    reader.readAsDataURL($(this)[0].files[0]);

                }

                $('input[name="slideruploadedfile_2"]').parent().find('.kb_error_message').remove();

            }



        }

        else // Internet Explorer 9 Compatibility

        {

            $('#notification_error').html(invalid_file_txt);

            file_error = true;

        }

    });

    $('#slideruploadedfile_4').on('change', function (e) {

        if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)

        {

            var files = $(this)[0].files[0];

            var file_data = e.target.files;

            var file_mimetypes = [

                'image/gif',

                'image/jpeg',

                'image/png',

                'application/x-shockwave-flash',

                'image/psd',

                'image/bmp',

                'image/tiff',

                'application/octet-stream',

                'image/jp2',

                'image/iff',

                'image/vnd.wap.wbmp',

                'image/xbm',

                'image/vnd.microsoft.icon',

                'image/webp'

            ];



            var file_format = false;

            for (i = 0; i < file_mimetypes.length; i++) {

                if (files.type == file_mimetypes[i]) {

                    file_format = true;

                }

            }



            if (!file_format)

            {

                $('input[name="slideruploadedfile_4"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');

                slider_banner_file_error = true;



            } else if (files.size > default_file_size) {

                $('input[name="slideruploadedfile_4"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');

                slider_banner_file_error = true;

            } else {

                slider_banner_file_error = false;

                if (typeof (FileReader) != "undefined") {



                    var image_holder = $("#sliderimage_2");



                    image_holder.empty();



                    var reader = new FileReader();

                    reader.onload = function (e) {

                        $('#sliderimage_4').attr('src', e.target.result);

                    }

                    image_holder.show();

                    reader.readAsDataURL($(this)[0].files[0]);

                }

                $('input[name="slideruploadedfile_4"]').parent().find('.kb_error_message').remove();

            }



        }

        else // Internet Explorer 9 Compatibility

        {

            $('#notification_error').html(invalid_file_txt);

            file_error = true;

        }

    });

    $('#slideruploadedfile_3').on('change', function (e) {

        if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)

        {

            var files = $(this)[0].files[0];

            var file_data = e.target.files;

            var file_mimetypes = [

                'image/gif',

                'image/jpeg',

                'image/png',

                'application/x-shockwave-flash',

                'image/psd',

                'image/bmp',

                'image/tiff',

                'application/octet-stream',

                'image/jp2',

                'image/iff',

                'image/vnd.wap.wbmp',

                'image/xbm',

                'image/vnd.microsoft.icon',

                'image/webp'

            ];



            var file_format = false;

            for (i = 0; i < file_mimetypes.length; i++) {

                if (files.type == file_mimetypes[i]) {

                    file_format = true;

                }

            }



            if (!file_format)

            {

                $('input[name="slideruploadedfile_3"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');

                slider_banner_file_error = true;



            } else if (files.size > default_file_size) {

                $('input[name="slideruploadedfile_3"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');

                slider_banner_file_error = true;

            } else {

                slider_banner_file_error = false;

                if (typeof (FileReader) != "undefined") {



                    var image_holder = $("#sliderimage_3");



                    image_holder.empty();



                    var reader = new FileReader();

                    reader.onload = function (e) {

                        $('#sliderimage_3').attr('src', e.target.result);

                    }

                    image_holder.show();

                    reader.readAsDataURL($(this)[0].files[0]);

                }

                $('input[name="slideruploadedfile_3"]').parent().find('.kb_error_message').remove();

            }



        }

        else // Internet Explorer 9 Compatibility

        {

            $('#notification_error').html(invalid_file_txt);

            file_error = true;

        }

    });

    $('#slideruploadedfile_5').on('change', function (e) {

        if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)

        {

            var files = $(this)[0].files[0];

            var file_data = e.target.files;

            var file_mimetypes = [

                'image/gif',

                'image/jpeg',

                'image/png',

                'application/x-shockwave-flash',

                'image/psd',

                'image/bmp',

                'image/tiff',

                'application/octet-stream',

                'image/jp2',

                'image/iff',

                'image/vnd.wap.wbmp',

                'image/xbm',

                'image/vnd.microsoft.icon',

                'image/webp'

            ];



            var file_format = false;

            for (i = 0; i < file_mimetypes.length; i++) {

                if (files.type == file_mimetypes[i]) {

                    file_format = true;

                }

            }



            if (!file_format)

            {

                $('input[name="slideruploadedfile_5"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');

                slider_banner_file_error = true;



            } else if (files.size > default_file_size) {

                $('input[name="slideruploadedfile_5"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');

                slider_banner_file_error = true;

            } else {

                slider_banner_file_error = false;

                if (typeof (FileReader) != "undefined") {



                    var image_holder = $("#sliderimage_5");



                    image_holder.empty();



                    var reader = new FileReader();

                    reader.onload = function (e) {

                        $('#sliderimage_5').attr('src', e.target.result);

                    }

                    image_holder.show();

                    reader.readAsDataURL($(this)[0].files[0]);

                }

                $('input[name="slideruploadedfile_5"]').parent().find('.kb_error_message').remove();

            }



        }

        else // Internet Explorer 9 Compatibility

        {

            $('#notification_error').html(invalid_file_txt);

            file_error = true;

        }

    });

    $('#slideruploadedfile_6').on('change', function (e) {

        if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)

        {

            var files = $(this)[0].files[0];

            var file_data = e.target.files;

            var file_mimetypes = [

                'image/gif',

                'image/jpeg',

                'image/png',

                'application/x-shockwave-flash',

                'image/psd',

                'image/bmp',

                'image/tiff',

                'application/octet-stream',

                'image/jp2',

                'image/iff',

                'image/vnd.wap.wbmp',

                'image/xbm',

                'image/vnd.microsoft.icon',

                'image/webp'

            ];



            var file_format = false;

            for (i = 0; i < file_mimetypes.length; i++) {

                if (files.type == file_mimetypes[i]) {

                    file_format = true;

                }

            }



            if (!file_format)

            {

                $('input[name="slideruploadedfile_6"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');

                slider_banner_file_error = true;



            } else if (files.size > default_file_size) {

                $('input[name="slideruploadedfile_6"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');

                slider_banner_file_error = true;

            } else {

                slider_banner_file_error = false;

                if (typeof (FileReader) != "undefined") {



                    var image_holder = $("#sliderimage_6");



                    image_holder.empty();



                    var reader = new FileReader();

                    reader.onload = function (e) {

                        $('#sliderimage_6').attr('src', e.target.result);

                    }

                    image_holder.show();

                    reader.readAsDataURL($(this)[0].files[0]);

                }

                $('input[name="slideruploadedfile_6"]').parent().find('.kb_error_message').remove();

            }



        }

        else // Internet Explorer 9 Compatibility

        {

            $('#notification_error').html(invalid_file_txt);

            file_error = true;

        }

    });

    $('#slideruploadedfile_7').on('change', function (e) {

        if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)

        {

            var files = $(this)[0].files[0];

            var file_data = e.target.files;

            var file_mimetypes = [

                'image/gif',

                'image/jpeg',

                'image/png',

                'application/x-shockwave-flash',

                'image/psd',

                'image/bmp',

                'image/tiff',

                'application/octet-stream',

                'image/jp2',

                'image/iff',

                'image/vnd.wap.wbmp',

                'image/xbm',

                'image/vnd.microsoft.icon',

                'image/webp'

            ];



            var file_format = false;

            for (i = 0; i < file_mimetypes.length; i++) {

                if (files.type == file_mimetypes[i]) {

                    file_format = true;

                }

            }



            if (!file_format)

            {

                $('input[name="slideruploadedfile_7"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');

                slider_banner_file_error = true;



            } else if (files.size > default_file_size) {

                $('input[name="slideruploadedfile_7"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');

                slider_banner_file_error = true;

            } else {

                slider_banner_file_error = false;

                if (typeof (FileReader) != "undefined") {



                    var image_holder = $("#sliderimage_7");



                    image_holder.empty();



                    var reader = new FileReader();

                    reader.onload = function (e) {

                        $('#sliderimage_7').attr('src', e.target.result);

                    }

                    image_holder.show();

                    reader.readAsDataURL($(this)[0].files[0]);

                }

                $('input[name="slideruploadedfile_7"]').parent().find('.kb_error_message').remove();

            }



        }

        else // Internet Explorer 9 Compatibility

        {

            $('#notification_error').html(invalid_file_txt);

            file_error = true;

        }

    });

    $('#slideruploadedfile_8').on('change', function (e) {

        if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)

        {

            var files = $(this)[0].files[0];

            var file_data = e.target.files;

            var file_mimetypes = [

                'image/gif',

                'image/jpeg',

                'image/png',

                'application/x-shockwave-flash',

                'image/psd',

                'image/bmp',

                'image/tiff',

                'application/octet-stream',

                'image/jp2',

                'image/iff',

                'image/vnd.wap.wbmp',

                'image/xbm',

                'image/vnd.microsoft.icon',

                'image/webp'

            ];



            var file_format = false;

            for (i = 0; i < file_mimetypes.length; i++) {

                if (files.type == file_mimetypes[i]) {

                    file_format = true;

                }

            }



            if (!file_format)

            {

                $('input[name="slideruploadedfile_8"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');

                slider_banner_file_error = true;



            } else if (files.size > default_file_size) {

                $('input[name="slideruploadedfile_8"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');

                slider_banner_file_error = true;

            } else {

                slider_banner_file_error = false;

                if (typeof (FileReader) != "undefined") {



                    var image_holder = $("#sliderimage_8");



                    image_holder.empty();



                    var reader = new FileReader();

                    reader.onload = function (e) {

                        $('#sliderimage_8').attr('src', e.target.result);

                    }

                    image_holder.show();

                    reader.readAsDataURL($(this)[0].files[0]);

                }

                $('input[name="slideruploadedfile_8"]').parent().find('.kb_error_message').remove();

            }



        }

        else // Internet Explorer 9 Compatibility

        {

            $('#notification_error').html(invalid_file_txt);

            file_error = true;

        }

    });

}

function trashBannerSquareComponentFunction(a) {

    

    num_of_component = parseInt($('#number_of_component').val());

    num_of_component = num_of_component - 1;

    $('#number_of_component').val(num_of_component);

    var str = $(a).attr('id');

    var array = str.split("_");

    var id_component = array[1];

    var id_layout = $('#id_layout').val();

    $('#id_component_selected').val(id_component);

    $.ajax({

        url: delete_ajaxaction + "&configure=kbmobileapp&deleteBannerSquarecomponent=true",

        data: 'id_layout=' + id_layout + '&id_component=' + id_component,

        type: "post",

        success: function (data)

        {

            $(a).parents('.slide').remove();

            preview_content();

            showSuccessMessage(component_delete);

        }

    });



}

function trashLastAccessComponentFunction(a) {

    num_of_component = parseInt($('#number_of_component').val());

    num_of_component = num_of_component - 1;

    $('#number_of_component').val(num_of_component);

    var str = $(a).attr('id');

    var array = str.split("_");

    var id_component = array[1];

    var id_layout = $('#id_layout').val();

    $('#id_component_selected').val(id_component);

    $.ajax({

        url: delete_ajaxaction + "&configure=kbmobileapp&deleteLastAccesscomponent=true",

        data: 'id_layout=' + id_layout + '&id_component=' + id_component,

        type: "post",

        success: function (data)

        {

            $(a).parents('.slide').remove();

            preview_content();

            showSuccessMessage(component_delete);

        }

    });



}



function trashProductHorizontalComponentFunction(a) {

    num_of_component = parseInt($('#number_of_component').val());

    num_of_component = num_of_component - 1;

    $('#number_of_component').val(num_of_component);

    var str = $(a).attr('id');

    var array = str.split("_");

    var id_component = array[1];

    var id_layout = $('#id_layout').val();

    $('#id_component_selected').val(id_component);

    $.ajax({

        url: delete_ajaxaction + "&configure=kbmobileapp&deleteProductcomponent=true",

        data: 'id_layout=' + id_layout + '&id_component=' + id_component,

        type: "post",

        success: function (data)

        {

            $(a).parents('.slide').remove();

            preview_content();

            showSuccessMessage(component_delete);

        }

    });



}

function trashTopcategoryComponentFunction(a) {

    

    num_of_component = parseInt($('#number_of_component').val());

    num_of_component = num_of_component - 1;

    $('#number_of_component').val(num_of_component);



    var str = $(a).attr('id');

    var array = str.split("_");

    var id_component = array[1];

    var id_layout = $('#id_layout').val();

    $('#id_component_selected').val(id_component);

    $.ajax({

        url: delete_ajaxaction + "&configure=kbmobileapp&deleteTopcategorycomponent=true",

        data: 'id_layout=' + id_layout + '&id_component=' + id_component,

        type: "post",

        success: function (data)

        {

            $(a).parents('.slide').remove();

            preview_content();

            showSuccessMessage(component_delete);

        }

    });



}

function trashBannerCountdownComponentFunction(a) {



    num_of_component = parseInt($('#number_of_component').val());

    num_of_component = num_of_component - 1;

    $('#number_of_component').val(num_of_component);

    var str = $(a).attr('id');

    var array = str.split("_");

    var id_component = array[1];

    var id_layout = $('#id_layout').val();

    $('#id_component_selected').val(id_component);

    $.ajax({

        url: ajaxaction + "&configure=kbmobileapp&deleteBannerCountdowncomponent=true",

        data: 'id_layout=' + id_layout + '&id_component=' + id_component,

        type: "post",

        success: function (data)

        {

            if (1) {

                $(a).parents('.slide').remove();

                preview_content();



            }

        }

    });



}

function showSuccessMessage(message) {

      $.growl.notice({ title: "", message: message});

}

function showErrorMessage(message) {

      $.growl.error({ title: "", message: message});

}
