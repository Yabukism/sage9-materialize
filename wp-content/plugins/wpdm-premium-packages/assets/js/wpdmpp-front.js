jQuery(function ($) {

    try { $('.ttip').tooltip(); } catch (e){}

    $('.file-price').on('click', function () {
        var pid = $(this).data('pid'), ps = 0, files = [], uc = 0, al = '';
        var haslic = parseInt($('.license-'+pid).length);
        if(haslic > 0)
            al = $('.license-'+pid+':checked').val();

        $('.file-price-'+pid).each(function () {

            if($(this).is(':checked')){
                ps += al == ''?parseFloat($(this).val()):parseFloat($(this).data(al));
                files.push($(this).data('file'));
            }
            else uc++;
        });
        ps = ps.toFixed(2);
        var ppc = al == ''?parseFloat($('#price-'+pid).attr('content')):parseFloat($('.license-'+pid+ '[value='+al+']').data('price'));
        if(ps == 0 || uc == 0 || ps > parseFloat(ppc)) ps = ppc.toFixed(2);
        ps += wpdmpp_extra_gigs();
        //$('.price-'+pid).html(wpdmpp_currency_sign+ps);
        $('.price-'+pid).html(wpdmpp_csign_before+ps+wpdmpp_csign_after);
        $('#files_'+pid).val(files);
    });


    $('.wpdmpp-extra-gig').on('click', function () {
        var pid = $(this).data('product-id'), ps = 0, files = [], uc = 0, al = '';
        var haslic = parseInt($('.license-'+pid).length);
        if(haslic > 0)
            al = $('.license-'+pid+':checked').val();

        $('.file-price-'+pid).each(function () {

            if($(this).is(':checked')){
                ps += al == ''?parseFloat($(this).val()):parseFloat($(this).data(al));
                files.push($(this).data('file'));
            }
            else uc++;
        });

        ps = ps.toFixed(2);
        var ppc = al == ''?parseFloat($('#price-'+pid).attr('content')):parseFloat($('.license-'+pid+ '[value='+al+']').data('price'));
        if(ps == 0 || uc == 0 || ps > parseFloat(ppc)) ps = ppc.toFixed(2);
        ps = parseFloat(wpdmpp_extra_gigs())+parseFloat(ps);
        ps = ps.toFixed(2);
        //$('.price-'+pid).html(wpdmpp_currency_sign+ps);
        $('.price-'+pid).html(wpdmpp_csign_before+ps+wpdmpp_csign_after);
        $('#files_'+pid).val(files);
    });


    $('.price-variation').on('click', function () {

        var pid = $(this).data('product-id'), price = 0, license = $(this).val(), sfp =0;
        /*
         $('.price-variation-' + pid).each(function () {
         if ($(this).is(':checked'))
         price += parseFloat($(this).data('price'));
         });
         */
        price = parseFloat($(this).data('price'));

        $('#premium-files-' + pid+' .premium-file').each(function () {
            $(this).find('.badge').html($(this).find('.badge').data(license));

        });

        $('.file-price-' + pid).each(function () {
            if ($(this).is(':checked')) sfp += parseFloat($(this).data(license));
        });

        //var pricehtml = "<i class='fa fa-shopping-cart'></i> Add to Cart <span class='label label-primary'>" + $('#total-price-' + pid).data('curr') + price + "<label>";
        if(sfp > 0 && sfp < price)
            price = sfp;
        price += wpdmpp_extra_gigs();
        //$('.price-'+pid).html(wpdmpp_currency_sign+price.toFixed(2));
        $('.price-'+pid).html(wpdmpp_csign_before+price.toFixed(2)+wpdmpp_csign_after);
        //$('#cart_submit').html(pricehtml);

    });


    $('#licreq').on('click', function () {
        if($(this).is(":checked")) {
            $('.file-price-field').hide();
            $('.file-price-table').show();
            $('#licopt').slideDown();
        }
        else {
            $('.file-price-field').show();
            $('.file-price-table').hide();
            $('#licopt').slideUp();
        }

    });
    $('.lic-enable').each(function () {
        if($(this).is(":checked") && !$(this).is(":disabled")) {
            $("#lic-price-" + $(this).data('lic')).removeAttr('disabled');
            $(".lic-file-price-" + $(this).data('lic')).removeAttr('disabled');

        }
        else {
            $("#lic-price-" + $(this).data('lic')).attr('disabled', 'disabled');
            if(!$(this).is(":checked"))
                $(".lic-file-price-" + $(this).data('lic')).attr('disabled', 'disabled');
        }
    });
    $('.lic-enable').on('click', function () {
        if($(this).is(":checked") && !$(this).is(":disabled")) {
            $("#lic-price-" + $(this).data('lic')).removeAttr('disabled');
            $(".lic-file-price-" + $(this).data('lic')).removeAttr('disabled');
        }
        else {
            $("#lic-price-" + $(this).data('lic')).attr('disabled', 'disabled');
            if(!$(this).is(":checked"))
                $(".lic-file-price-" + $(this).data('lic')).attr('disabled', 'disabled');
        }
    });


    $('.wpdm_cart_form').submit(function () {
        var btnaddtocart = $(this).find('.btn-addtocart');
        btnaddtocart.css('width', btnaddtocart.css('width'));
        btnaddtocart.attr('disabled', 'disabled');
        var form = $(this);
        var btnlbl = btnaddtocart.html();
        btnaddtocart.html('<i class="fa fa-refresh fa-spin"></i>');
        $(this).ajaxSubmit({
            success: function (res) {
                if (btnaddtocart.data('cart-redirect') == 'on') {
                    location.href = res;
                    return false;
                }
                //btnaddtocart.removeAttr('disabled');
                form.find('.btn-viewcart').remove();
                btnaddtocart.addClass('btn-wc');
                btnaddtocart.html('<i class="fa fa-check-circle"></i>').after('<a href="' + res + '" class="' + btnaddtocart.attr('class').replace('btn-addtocart', 'btn-checkout') + ' btn-viewcart ttip" type="button" title="<i class=\'fa fa-check-circle\'></i> &nbsp;Added to Cart">Checkout <i class="fa fa-long-arrow-right"></i></a>');
                btnaddtocart.removeAttr('disabled').remove();
                $('.ttip').tooltip({html: true});
                window.postMessage("cart_updated", window.location.protocol + "//" + window.location.hostname);
            }
        });
        return false;
    });


    $('#checkoutbtn').click(function(){
        $(this).attr('disabled','disabled');
        $('#checkoutarea').slideDown();
    });


    /* Delete Order */
    $('.delete_order').on('click',function(){
        var nonce = $(this).attr('nonce');
        var order_id = $(this).attr('order_id');
        var url = ajax_url;
        var th = $(this);
        jQuery('#order_'+order_id).fadeTo('0.5');
        if(confirm("Are you sure you want to delete this order ?")){
            $(this).html('<i class="fa fa-spinner fa-spin"></i>').css('outline','none');
            jQuery.ajax({
                type : "post",
                dataType : "json",
                url : url,
                data : {action: "wpdmpp_delete_frontend_order", order_id : order_id, nonce: nonce},
                success: function(response) {
                    if(response.type == "success") {
                        $('#order_'+order_id).slideUp();
                    }
                    else {
                        alert("Something went wrong during deleting...")
                    }
                }
            });
        }
        return false;
    });


    /* Checkout */

    $('body').on('submit', '#payment_form', function(e){
        e.preventDefault();
        if(navigator.userAgent.indexOf("Safari") > -1 && ($('#f-name').val() == '' || $('#email_m').val() == '')){
            alert('Please Enter Your Name & Email');
            return false;
        }

        $('#pay_btn').data('label', $('#pay_btn').html()).attr('disabled','disabled').html('<i class="fa fa-spin fa-spinner"></i>').css('outline','none');
        $('#wpdmpp-cart-form .btn').attr('disabled','disabled');
        $(this).ajaxSubmit({
            'url': '?task=paynow',
            'beforeSubmit':function(){
                //jQuery('#payment_w8').fadeIn();
            },
            'success':function(res){
                $('#paymentform').html(res);
                if(res.match(/error/)){
                    alert(res);
                    $('#pay_btn').removeAttr('disabled').html($('#pay_btn').data('label'));
                }else{
                    $('#payment_w8').fadeOut();
                }
            }
        });
        return false;
    });
    $(".pm-list .list-group-item:first-child").addClass('active');
    $(".pm-list .list-group-item:first-child input[type=radio]").attr('checked','checked');
    $(".pm-list .list-group-item").on('click', function(){
        $('.pm-list .list-group-item').removeClass('active');
        $(this).addClass('active');
    });

    $('body').on('change', '.calculate-tax', function () {
        var country = $('#country').val();
        var state = $('#region').val() != null ? $('#region').val() : $('#region-txt').val();

        $.get(ajax_url+'?action=gettax&country='+country+'&state=' + state, function (res) {
            //console.log(res);
            var tax_info = JSON.parse(res);
            $('#wpdmpp_cart_tax').text(tax_info.tax);
            $('#wpdmpp_cart_grand_total').text(tax_info.total);
            $('.cart-total-final').removeClass('hide');
            $('.cart-total-final .badge').text(' ' + tax_info.total);
        });
    });

    $('body').on('change','#select-payment-method #country', function () {
        populateStates($(this).val());
    });

    $('#save-cart').on('click', function(){
        $('#wpdm-save-cart').removeClass('hide');
        $(this).attr('disabled','disabled').html('<i class="fa fa-spinner fa-spin"></i>');
        $.post(location.href, { action: 'wpdm_pp_ajax_call', execute: 'SaveCart' }, function(res){
            $('#carturl').val( wpdmpp_cart_url + res );
            $('#save-cart').html('<i class="fa fa-check-circle"></i> Saved');
        });
    });

    $('body').on('click', '#email-cart', function(){
        var send_to = $('#cmail').val();

        if(send_to.trim() == ''){
            $('#cmail').css({'border' : '1px solid #f00'});
            //alert('Enter an Email');
            return;
        }

        $('#fae').removeClass('fa-envelope').addClass('fa-spinner fa-spin');
        $('#email-cart').attr('disabled','disabled').html('Sending...');
        $.post(location.href, {action: 'wpdm_pp_ajax_call', execute: 'EmailCart', email: $('#cmail').val(), carturl: $('#carturl').val()}, function(res){
            $('#fae').removeClass('fa-spinner fa-spin').addClass('fa-envelope');
            $('#email-cart').html('Sent');
        });
    });


});


function populateCountryState() {

    var $ = jQuery;

    var dataurl = wpdmpp_base_url + '/assets/js/data/';

    var countries = [], states = [], countryOptions ="",  stateOptions ="", countrySelect = $('#country'), stateSelect = $('#region');

    $.getJSON(dataurl+'countries.json', function(data){
        $.each(data, function(i, country){
            countries[""+country.code] = country.filename;
            countryOptions += "<option value='"+country.code+"'>"+country.name+"</option>";
        });
        countrySelect.html(countryOptions);
    });
    countrySelect.change(function() {
        var countryCode = $(this).val();
        loadStates(countryCode);

    });

    function loadStates(countryCode){
        var filename = countries[countryCode];
        if(filename != undefined) {
            $('#region-txt').attr('disabled','disabled').hide();
            $('#region').removeAttr('disabled').show();
            $.getJSON(dataurl + 'countries/' + filename + '.json', function (data) {
                stateOptions = "";
                $.each(data, function (i, state) {
                    states["" + state.code] = state;
                    var scode = state.code.replace(countryCode + "-", "");
                    stateOptions += "<option value='" + scode + "'>" + state.name + "</option>";
                });
                stateSelect.html(stateOptions);
            });
        } else {
            $('#region').attr('disabled','disabled').hide();
            $('#region-txt').removeAttr('disabled').show();
        }

    }
}

function populateStates(countryCode){
    var $ = jQuery;

    var dataurl = wpdmpp_base_url + '/assets/js/data/';
    var countries = [], states = [], countryOptions ="",  stateOptions ="", countrySelect = $('#country'), stateSelect = $('#region'), filename = '';
    $.getJSON(dataurl+'countries.json', function(data){
        $.each(data, function(i, country){
            if(countryCode == country.code) {
                filename = country.filename;
            }

        });

        if(filename != undefined && filename != '') {
            $('#region-txt').attr('disabled','disabled').hide();
            $('#region').removeAttr('disabled').show();
            $.getJSON(dataurl + 'countries/' + filename + '.json', function (data) {
                stateOptions = "";
                $.each(data, function (i, state) {
                    states["" + state.code] = state;
                    var scode = state.code.replace(countryCode + "-", "");
                    stateOptions += "<option value='" + scode + "'>" + state.name + "</option>";
                });
                stateSelect.html(stateOptions);
            });
        } else {
            $('#region').attr('disabled','disabled').hide();
            $('#region-txt').removeAttr('disabled').show();
        }

    });

}


function  wpdmpp_pp_remove_cart_item(id){

    if(!confirm('Are you sure?')) return false;
    jQuery('#save-cart').removeAttr('disabled');
    jQuery('#cart_item_'+id+' *').css('color','#ccc');
    jQuery.post('?wpdmpp_remove_cart_item='+id ,function(res){
        var obj = jQuery.parseJSON(res);

        jQuery('#cart_item_'+id).fadeOut().remove();
        jQuery('#wpdmpp_cart_grand_total').html(obj.cart_total);
        jQuery('#wpdmpp_cart_discount').html(obj.cart_discount);
        jQuery('#wpdmpp_cart_subtotal').html(obj.cart_subtotal); });
    return false;
}

function  wpdmpp_pp_remove_cart_item2(id,item){
    if(!confirm('Are you sure?')) return false;
    jQuery('#cart_item_'+id+'_'+item+' *').css('color','#ccc');
    jQuery.post('?wpdmpp_remove_cart_item='+ id + '&item_id='+item
        ,function(res){
            var obj = jQuery.parseJSON(res);
            jQuery('#save-cart').removeAttr('disabled');
            jQuery('#cart_item_'+id+'_'+item).fadeOut().remove();
            jQuery('#wpdmpp_cart_grand_total').html(obj.cart_total);
            jQuery('#wpdmpp_cart_discount').html(obj.cart_discount);
            jQuery('#wpdmpp_cart_subtotal').html(obj.cart_subtotal); });
    return false;
}

function wpdmpp_extra_gigs() {
    var exgigs = [], sum = 0, added = [];
    jQuery('.wpdmpp-extra-gig').each(function () {
        if(jQuery(this).is(':checked') && added.indexOf(parseInt(jQuery(this).val())) < 0){
            added.push(parseInt(jQuery(this).val()));
            sum += parseFloat(jQuery(this).data('price'));
        }
    });

    return sum;

}
