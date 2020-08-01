/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 *
 * Description
 *
 * Gamification wheel for offering discount coupons.
 */

var copy_msg_show = true;
var globaltime = 0;
var intervalVariable;
var wheelStartTime = 0;
var currentRotation = 0;
var rotationDegree = 0;
var wheelEndTime = 0;
var wheelcurrentRotation = 0;
var startTime = 0;
var winningCode = "";
// $('#velsof_wheel_container').show();
// var winningangle = parseInt(720 + ((2 - 1) * 30));
//rotateWheel(winningangle, 9000);
function onRotateWheel() {
var email = $("input[name='spin_wheel_email']").val().trim();
var email_error = checkEnteredEmail_spinwin(email);
if (email_error === false) {
        if (email_recheck_spinwin == 1) {              
            if(email_pop_spinwin=='3'){
                fetchCoupon_spinwin();
            } if(email_pop_spinwin=='2'){
                sendEmail_spinwin();
            } if(email_pop_spinwin=='1'){
                fetchCoupon_spinwin();
            }
        }else{   
            if(email_pop_spinwin=='3'){
                checkEmail_spinwin();
            } if(email_pop_spinwin=='2'){
                sendEmail_spinwin1();
            } if(email_pop_spinwin=='1'){
                fetchCoupon_spinwin1();
            }
        }
    }

}

function fetchCoupon_spinwin(){
    var email = $("input[name='spin_wheel_email']").val().trim();
    $.ajax({               
        url: spin_wheel_front_path1,
        type: 'post',
        data: 'spinwinajax=true&email=' + email + '&email_pop=' + email_pop_spinwin,
        dataType: 'json',   
        beforeSend: function () {                   
                       $('.saving').show();
                        $('#rotate_btn').hide();
                  },     
        success: function (data) {                   
            if(data.type=="success"){
                $.ajax({               
                    url: spin_wheel_front_path,
                    type: 'post',
                    data: 'spinwinajax=true&email=' + email + '&email_pop=' + email_pop_spinwin,
                    dataType: 'json',
                    beforeSend: function () {                   
                       $('.saving').show();
                        $('#rotate_btn').hide();
                  },
                  success: function (json) { 
                        var code = json['code'];
                        winningCode = json['code'];
                        var slice_no = json['slice_no'];
                        var winningangle = parseInt(720 + ((slice_no - 1) * 30));
                        rotateWheel(winningangle, 9000);
                        setCookie_spinwin('velsof_wheel_used', 2);
                        $('#suc_msg').html(json['suc_msg']);
                        $('#velsof_success_description').html(json['suc_desc']);
                    },
                    complete: function () {
                        $('.saving').show();
                        $('#rotate_btn').hide();
                        setWheelFlag();
                    }
            });
            }else{
                $('#velsof_spin_wheel').tooltipster('content', email_check_spinwin);
                $('#velsof_spin_wheel').tooltipster('show');
                setTimeout(function () {
                    $('#velsof_spin_wheel').tooltipster('destroy');
                    $('.saving').hide();
                        $('#rotate_btn').show();
                }, 2000);
            }
        },                
    });
}

function fetchCoupon_spinwin1(){
    var email = $("input[name='spin_wheel_email']").val().trim();;
    $.ajax({               
        url: spin_wheel_front_path,
        type: 'post',
        data: 'spinwinajax=true&email=' + email + '&email_pop=' + email_pop_spinwin,
        dataType: 'json',
       beforeSend: function () {                   
           $('.saving').show();
            $('#rotate_btn').hide();
      },
      success: function (json) {
          if(json.type=="constant_error"){
          
                $('#velsof_spin_wheel').tooltipster('content', email_check_spinwin);
                $('#velsof_spin_wheel').tooltipster('show');
                setTimeout(function () {
                    $('#velsof_spin_wheel').tooltipster('destroy');
                    $('.saving').hide();
                        $('#rotate_btn').show();
                }, 2000);
            }
         else{ 
        var code = json['code'];
        winningCode = json['code'];
        var slice_no = json['slice_no'];
        var winningangle = parseInt(720 + ((slice_no - 1) * 30));
        rotateWheel(winningangle, 9000);
        setCookie_spinwin('velsof_wheel_used', 2);
        $('#suc_msg').html(json['suc_msg']);
        $('#velsof_success_description').html(json['suc_desc']);
    }
    },
    complete: function () {
        $('.saving').show();
        $('#rotate_btn').hide();
        setWheelFlag();
    }
});
}

function checkEmail_spinwin(){
   var email = $("input[name='spin_wheel_email']").val().trim();;
    $.ajax({               
        url: spin_wheel_front_path,
        type: 'post',
        data: 'spinwinajax=true&email=' + email + '&email_pop=' + email_pop_spinwin,
        dataType: 'json',
        beforeSend: function () {                   
         $('.saving').show();
            $('#rotate_btn').hide();
      },
      success: function (json) {     
            var code = json['code'];
            var coupon_value=json['value'];
            winningCode = json['code'];
            var slice_no = json['slice_no'];
            var winningangle = parseInt(720 + ((slice_no - 1) * 30));
            rotateWheel(winningangle, 9000);
            setCookie_spinwin('velsof_wheel_used', 2);
            $('#suc_msg').html(json['suc_msg']);
            $('#velsof_success_description').html(json['suc_desc']);
            
        },
        complete: function () {
            $('.saving').show();
            $('#rotate_btn').hide();
            setWheelFlag();
        }
});
}

function setWheelFlag(){
    $.ajax({               
        url: "index.php?route=webservices/api&method=appSpinWin&version=1.6&is_wheel_used=1",
        type: 'post',
        data: 'is_wheel_used=1'
    });
    if(device_type == 'iOS') {
        window.location = "index.php?route=webservices/api&method=appSpinWin&version=1.6&is_wheel_used=1";
    }
}

function sendEmail_spinwin(){
    var email = $("input[name='spin_wheel_email']").val().trim();;
    $.ajax({               
        url: spin_wheel_front_path1,
        type: 'post',
        data: 'spinwinajax=true&email=' + email + '&email_pop=' + email_pop_spinwin,
        dataType: 'json',      
        beforeSend: function () {                   
                       $('.saving').show();
                        $('#rotate_btn').hide();
                  },  
        success: function (data) {                   
            if(data.type=="success"){
                $.ajax({               
                    url: spin_wheel_front_path2,
                    type: 'post',
                    data: 'spinwinajax=true&email=' + email + '&email_pop=' + email_pop_spinwin,
                    dataType: 'json',
                     beforeSend: function () {                   
                           $('.saving').show();
                            $('#rotate_btn').hide();
                      },
                      success: function (json) {
                        var code = json['code'];
                        var coupon_value=json['value'];
                        winningCode = json['code'];
                        var slice_no = json['slice_no'];
                        var winningangle = parseInt(720 + ((slice_no - 1) * 30));
                        rotateWheel(winningangle, 9000);
                        setCookie_spinwin('velsof_wheel_used', 2);
                        $('#suc_msg').html(json['suc_msg']);
                        $('#velsof_success_description').html(json['suc_desc']);
                        
                    },
                    complete: function () {
                        $('.saving').show();
                        $('#rotate_btn').hide();
                        setWheelFlag();
                    }
            });
            }else{
                $('#velsof_spin_wheel').tooltipster('content', email_check_spinwin);
                $('#velsof_spin_wheel').tooltipster('show');
                setTimeout(function () {
                    $('#velsof_spin_wheel').tooltipster('destroy');
                    $('.saving').hide();
                        $('#rotate_btn').show();
                }, 2000);
            }
        },                
    });
}

function sendEmail_spinwin1(){
    var email = $("input[name='spin_wheel_email']").val().trim();
        $.ajax({               
            url: spin_wheel_front_path2,
            type: 'post',
            data: 'spinwinajax=true&email=' + email + '&email_pop=' + email_pop_spinwin,
            dataType: 'json',
            beforeSend: function () {                   
               $('.saving').show();
                $('#rotate_btn').hide();
          },
          success: function (json) {
            var code = json['code'];
            var coupon_value=json['value'];
            winningCode = json['code'];
            var slice_no = json['slice_no'];
            var winningangle = parseInt(720 + ((slice_no - 1) * 30));
            rotateWheel(winningangle, 9000);
            setCookie_spinwin('velsof_wheel_used', 2);
            $('#suc_msg').html(json['suc_msg']);
            $('#velsof_success_description').html(json['suc_desc']);
            
        },
        complete: function () {
            $('.saving').show();
            $('#rotate_btn').hide();
            setWheelFlag();
        }
    });            

}
function animationFrame(animate)
{
    if (window.requestAnimationFrame) {
        window.requestAnimationFrame(animate);
    } else if (window.webkitRequestAnimationFrame) {
        window.webkitRequestAnimationFrame(animate);
    } else if (window.mozRequestAnimationFrame) {
        window.mozRequestAnimationFrame(animate);
    } else {
        Console.log('Sorry! No Supported Browser');
    }
}

function rotateWheel(degreeToRotate, rotationTime)
{
    currentRotation = 0;
    rotationDegree = degreeToRotate;
    wheelStartTime = 0;
    wheelEndTime = rotationTime;
    startTime = 0;
    animationFrame(animate);
}

function wheelRotation(movement)
{
    return 1 - Math.pow(1 - movement, 5);
}

function pointerMovement(movement)
{
    var n = (-Math.pow((1 - (movement * 2)), 2) + 1);
    if (n < 0)
        n = 0;
    return n;
}

function animate(timestamp)
{
    if (!startTime) {
        startTime = timestamp;
    }

    wheelStartTime = timestamp - startTime;

    if (wheelStartTime > wheelEndTime) {
        wheelStartTime = wheelEndTime;
    }

    wheelcurrentRotation = wheelRotation(((rotationDegree / wheelEndTime) * wheelStartTime) / rotationDegree);
    currentRotation = wheelcurrentRotation * rotationDegree;

    /** Stop Pointer Movement if wheel rotation is 1 */
    if (wheelcurrentRotation > 0.99) {
        if(wheel_design != "1") {
            $('#velsof_wheel_pointer').css({'transform': 'translateY(0%) rotate3d(0,0,1,0deg)', '-webkit-transform': 'translateY(0%) rotate3d(0,0,1,0deg)'});
        }
    }

    tickerRotation = currentRotation - Math.floor(currentRotation / 360) * 360;
    for (i = 1; i <= 12; i++) {
        if ((tickerRotation >= (i * 30) - 20) && (tickerRotation <= (i * 30)))
        {
            angleRotation = 0.2;
            if (wheelcurrentRotation > angleRotation) {
                angleRotation = wheelcurrentRotation;
            }
            var pointerAngle = pointerMovement(-(((i * 30) - 20) - tickerRotation) / 10) * (30 * angleRotation);
            if(wheel_design != "1") {
                $('#velsof_wheel_pointer').css({'transform': 'translateY(0%)  rotate3d(0,0,1,' + (0 - pointerAngle) + 'deg)', '-webkit-transform': 'translateY(0%)  rotate3d(0,0,1,' + (0 - pointerAngle) + 'deg)'});
            }
        }
    }

    //console.log(wheelcurrentRotation);
    
    if (wheelcurrentRotation < 1) {
        $('#velsof_spinner').css({'transform': 'rotate3d(0,0,1,' + currentRotation + 'deg)', '-webkit-transform': 'rotate3d(0,0,1,' + currentRotation + 'deg)'});
        animationFrame(animate);
    }
    if (wheelcurrentRotation > .999) {
        $('#main_title').hide();
        $('#velsof_description').hide();
        $('.velsof_ul').hide();
        $('#rotate_btn').hide();
        $('#velsof_spin_wheel').hide();
        $('.saving').hide();
        $('#exit').hide();
        $('#suc_msg').show();
        $('#velsof_success_description').show();

        if (display_option_spinwin == 2) {
            $('#continue_btn').show();
        } else {
            if (winningCode !== '') {
                copy_msg_show = false;
                $('#velsof_spin_wheel').val(winningCode);
                $('#velsof_spin_wheel').attr('readonly','readonly');
                $('#velsof_spin_wheel').show();
                if (show_fireworks == "1") {
                    $('#velsof_wheel_main_container').fireworks();
                }
            }
            $('#continue_btn').show();
        }
    }
}

function checkEnteredEmail_spinwin(email) {
    var error = false;
    $('.spin_error').remove();
    $('#velsof_spin_wheel').tooltipster({
        animation: 'swing',
        'theme': ['tooltipster-default', 'tooltipster-velsofspinwheel']
    });

    var email_mand = checkMandatory_spinwin($("input[name='spin_wheel_email']"));
    var email_valid = checkEmail_spinwin1($("input[name='spin_wheel_email']"));
    if (email_mand !== true) {
        error = true;
        $('#velsof_spin_wheel').tooltipster('content', email_mand);
        $('#velsof_spin_wheel').tooltipster('show');
        setTimeout(function () {
            $('#velsof_spin_wheel').tooltipster('destroy');
        }, 2000);
        return error;
    } else if (email_valid !== true) {
        error = true;
        $('#velsof_spin_wheel').tooltipster('content', email_valid);
        $('#velsof_spin_wheel').tooltipster('show');
        setTimeout(function () {
            $('#velsof_spin_wheel').tooltipster('destroy');
        }, 2000);
        return error;
    } else {
        return error;
    }
}

function checkMandatory_spinwin(email){
    if(email.val().trim()==""){
        return empty_email_spinwin;
    }else{
        return true;
    }
}

function checkEmail_spinwin1(email){
   var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
   if(re.test(email.val().trim().toLowerCase())){
    return true;
}else{
    return validate_email_spinwin;
}
}

function wheelAction(data, email) {
    $('.before_loader').hide();
    if (data['type'] === 'Win') {
        var code = data['code'];
        var slice_no = data['slice_no'];
        $.ajax({
            url: spin_wheel_front_path,
            type: 'post',
            data: 'sendEmail_spinwin=true&email=' + email + '&code=' + code + '&slice_no=' + slice_no,
            dataType: 'json',
            success: function (json) {
                console.log(json);
            }
        });
        var code = data['code'];
        var label = data['label'];
    }
}

function getCookie(name) {
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0)
            return null;
    } else
    {
        begin += 2;
        var end = document.cookie.indexOf(";", begin);
        if (end == -1) {
            end = dc.length;
        }
    }
    return decodeURI(dc.substring(begin + prefix.length, end));
}
$(document).ready(function () {
    
    var show = true;
    var velsof_wheel_used = getCookie('velsof_wheel_used');
    
    if (min_screen_size_spinwin !== '' && min_screen_size_spinwin != '6') {
        var screen = min_screen_size_spinwin.split('_');
        var width = screen[0];
        var height = screen[1];
        if (window.screen.width < width) {
            $('#velsof_wheel_main_container').removeClass('transform');
            $("#velsof_wheel_container").hide();
            $('#pull_out').hide();
            show = false;
        }
    }else if(min_screen_size_spinwin != '' && min_screen_size_spinwin == '6'){
        if (window.screen.width > min_screen_size_spinwin) {
            $('#velsof_wheel_main_container').removeClass('transform');
            $("#velsof_wheel_container").hide();
            $('#pull_out').hide();
            show = false;
        }
    }
    if (show) {
        var myCookie = getCookie("velsof_spin_wheel_tab");
        var velsof_wheel_used = getCookie('velsof_wheel_used');
        if ((myCookie == null && velsof_wheel_used == null) || (myCookie == '' && velsof_wheel_used == '') || (myCookie && velsof_wheel_used)) {
            $('#velsof_wheel_container').show();
            setTimeout(function () {
                $('#velsof_wheel_main_container').addClass('transform');
            }, 500);
        } else {
            var cookie = getCookie('velsof_wheel_used');
            if (show_pull_out == 1 && cookie == null) {

                $('#pull_out').show();
            }
        }

        if(visit_spinwin!='0'){
            if(visit_spinwin=='1'){
                setCookie_spinwinHourly('velsof_wheel_used', 3, 1);
            }else if(visit_spinwin=='2'){
                setCookie_spinwin1('velsof_wheel_used', 3, 1);
            }else if(visit_spinwin=='3'){
                setCookie_spinwin1('velsof_wheel_used', 3, 7);
            }else if(visit_spinwin=='4'){
             setCookie_spinwin1('velsof_wheel_used', 3, 30);
            }
        }
    }
    

    $('.spin_toggle').on('click', function () {
        $('#velsof_wheel_container').show();

        setTimeout(
                function () {
                    $('#velsof_wheel_main_container').addClass('transform');
                }, 500);
    });

});

function setCookie_spinwin(cookie_name, cookie_value) {
    date = new Date();
    date.setTime(date.getTime() + 24 * 60 * 60 * 1000);
    expires = "; expires=" + date.toUTCString();
    document.cookie = cookie_name + '=' + cookie_value + expires + '; path=/';
}

function setCookie_spinwin1(cookie_name, cookie_value, days) {
    date = new Date();
    date.setTime(date.getTime() + 24 * 60 * 60 * 1000 * days);
    expires = "; expires=" + date.toUTCString();
    document.cookie = cookie_name + '=' + cookie_value + expires + '; path=/';
}

function setCookie_spinwinHourly(cookie_name, cookie_value, hour) {
    date = new Date();
    date.setTime(date.getTime() + 60 * 60 * 1000 * hour);
    expires = "; expires=" + date.toUTCString();
    document.cookie = cookie_name + '=' + cookie_value + expires + '; path=/';
}

function copyToClipboard(elem) {
    // create hidden text element, if it doesn't already exist
    var targetId = "_hiddenCopyText_";
    var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
    var origSelectionStart, origSelectionEnd;
    if (isInput) {
        // can just use the original source element for the selection and copy
        target = elem;
        origSelectionStart = elem.selectionStart;
        origSelectionEnd = elem.selectionEnd;
    } else {
        // must use a temporary form element for the selection and copy
        target = document.getElementById(targetId);
        if (!target) {
            var target = document.createElement("textarea");
            target.style.position = "absolute";
            target.style.left = "-9999px";
            target.style.top = "0";
            target.id = targetId;
            document.body.appendChild(target);
        }
        target.textContent = elem.textContent;
    }
    // select the content
    var currentFocus = document.activeElement;
    target.focus();
    target.setSelectionRange(0, target.value.length);

    // copy the selection
    var succeed;
    try {
        succeed = document.execCommand("copy");
    } catch (e) {
        succeed = false;
    }
    // restore original focus
    if (currentFocus && typeof currentFocus.focus === "function") {
        currentFocus.focus();
    }

    if (isInput) {
        // restore prior selection
        elem.setSelectionRange(origSelectionStart, origSelectionEnd);
    } else {
        // clear temporary content
        target.textContent = "";
    }
    return succeed;
}