var velovalidation = {
    language: {},
    script_regex: /(<script[\s\S]*?>[\s\S]*?<\/script>)|(<script[\s\S]*?>)|([\s\S]*?<\/script>)/i,
    style_regex: /(<style[\s\S]*?>[\s\S]*?<\/style>)|(<style[\s\S]*?>)|([\s\S]*?<\/style>)/i,
    iframe_regex: /(<iframe[\s\S]*?>[\s\S]*?<\/iframe>)|(<iframe[\s\S]*?>)|([\s\S]*?<\/iframe>)/i,
    specail_chars: "*|,\":<>[]{}`\';()@&$#%",
    checkTags: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        if (this.script_regex.test(val)) {
            return_val = velovalidation.error('script');
        } else if (this.style_regex.test(val)) {
            return_val = velovalidation.error('style');
        } else if (this.iframe_regex.test(val)) {
            return_val = velovalidation.error('iframe');
        }
        return return_val;
    },
    checkFirstname: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 32;
        minchar = typeof minchar !== 'undefined' ? minchar : 1;
        if (val != '') {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
            if (val.length <= maxchar) {
                if (val.length < minchar) {
                    return_val = velovalidation.error('minchar_fname').replace(/{%d}/g, minchar);
                }
            } else {
                return_val = velovalidation.error('maxchar_fname').replace(/{%d}/g, maxchar);
            }
        }
        return return_val;
    },
    checkMidname: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 32;
        minchar = typeof minchar !== 'undefined' ? minchar : 1;
        if (val != '') {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
            if (val.length <= maxchar) {
                if (val.length < minchar) {
                    return_val = velovalidation.error('minchar_mname').replace(/{%d}/g, minchar);
                }
            } else {
                return_val = velovalidation.error('maxchar_mname').replace(/{%d}/g, maxchar);
            }
        }
        return return_val;
    },
    checkLastname: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 32;
        minchar = typeof minchar !== 'undefined' ? minchar : 1;
        if (val != '') {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
            if (val.length <= maxchar) {
                if (val.length < minchar) {
                    return_val = velovalidation.error('minchar_lname').replace(/{%d}/g, minchar);
                }
            } else {
                return_val = velovalidation.error('maxchar_lname').replace(/{%d}/g, maxchar);
            }
        }
        return return_val;
    },
    checkPassword: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 40;
        minchar = typeof minchar !== 'undefined' ? minchar : 6;
        if (val != '') {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
            if (val.length <= maxchar) {
                if (val.length < minchar) {
                    return_val = velovalidation.error('minchar_pass').replace(/{%d}/g, minchar);
                } else {

                    if (!val.match(".*[A-Z].*"))
                        return velovalidation.error('capital_alphabets_pass');

                    if (!val.match(".*[a-z].*"))
                        return velovalidation.error('small_alphabets_pass');

                    if (!val.match(".*\\d.*"))
                        return velovalidation.error('digit_pass');

                    var splChars = "*|,\":<>[]{}`\';()@&$#%";
                    var splChars_exist = false;
                    for (var i = 0; i < val.length; i++) {
                        if (splChars.indexOf(val.charAt(i)) != -1) {
                            splChars_exist = true;
                            break;
                        }
                    }
                    if (!splChars_exist) {
                        return velovalidation.error('specialchar_pass');
                    }
                }
            } else {
                return_val = velovalidation.error('maxchar_pass').replace(/{%d}/g, maxchar);
            }

        }
        return return_val;
    },
    checkMandatory: function (ele, maxchar, minchar) {
        var val = ele.val().trim();
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 255;
        minchar = typeof minchar !== 'undefined' ? minchar : 0;
        var return_val = true;
        if (val == '') {
            return_val = velovalidation.error('empty_field');
        } else if (val.length > maxchar) {
            return_val = velovalidation.error('maxchar_field').replace(/{%d}/g, maxchar);
        } else if (val.length < minchar) {
            return_val = velovalidation.error('minchar_field').replace(/{%d}/g, minchar);
        } else {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
        }
        return return_val;
    },
    checkAmount: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        if (val != '') {
            if (!val.match(/^-?\d*(\.\d+)?$/)) {
                return_val = velovalidation.error('valid_amount');
            } else if (val < 0) {
                return_val = velovalidation.error('positive_amount');
            }
        }
        return return_val;
    },
    checkPercentage: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        if (val != '') {
            if (!val.match(/^-?\d*(\.\d+)?$/)) {
                return_val = velovalidation.error('valid_percentage');
            } else if (val < 0 || val > 100) {
                return_val = velovalidation.error('between_percentage');
            }
        }
        return return_val;
    },
    isNumeric: function (ele, positive) {
        var val = ele.val().trim();
        positive = typeof positive !== 'undefined' ? positive : true;
        var return_val = true;
        if (positive) {
            if (!(/^\d+$/.test(val))) {
                return_val = velovalidation.error('number_field');
            }
        } else {
            if (!(/^-\d+$/.test(val))) {
                return_val = velovalidation.error('number_field');
            }
        }
        return return_val;
    },
    isBetween: function (ele, min, max) {
        var val = ele.val().trim();
        var return_val = true;
        if (val != '') {
            if (val >= min && val <= max) {
                return return_val;
            } else {
                return_val = velovalidation.error('validate_range');
            }
        }
        return return_val;
    },
    checkEmail: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 96;
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (val != '') {
            if (val.length > maxchar) {
                return_val = velovalidation.error('max_email').replace(/{%d}/g, maxchar);
            } else if (!regex.test(val)) {
                return_val = velovalidation.error('validate_email');
            }
        }
        return return_val;
    },
    checkCountry: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 64;
        minchar = typeof minchar !== 'undefined' ? minchar : 3;
        if (val != '') {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
            if (val.length <= maxchar) {
                if (val.length < minchar) {
                    return_val = velovalidation.error('minchar_country').replace(/{%d}/g, minchar);
                }
            } else {
                return_val = velovalidation.error('maxchar_country').replace(/{%d}/g, maxchar);
            }
        }
        return return_val;
    },
    checkState: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 64;
        minchar = typeof minchar !== 'undefined' ? minchar : 3;
        if (val != '') {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
            if (val.length <= maxchar) {
                if (val.length < minchar) {
                    return_val = velovalidation.error('minchar_state').replace(/{%d}/g, minchar);
                }
            } else {
                return_val = velovalidation.error('maxchar_state').replace(/{%d}/g, maxchar);
            }
        }
        return return_val;
    },
    checkCity: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 64;
        minchar = typeof minchar !== 'undefined' ? minchar : 3;
        if (val != '') {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
            if (val.length <= maxchar) {
                if (val.length < minchar) {
                    return_val = velovalidation.error('minchar_city').replace(/{%d}/g, minchar);
                }
            } else {
                return_val = velovalidation.error('maxchar_city').replace(/{%d}/g, maxchar);
            }
        }
        return return_val;
    },
    checkProductName: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 255;
        minchar = typeof minchar !== 'undefined' ? minchar : 3;
        if (val != '') {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
            if (val.length <= maxchar) {
                if (val.length < minchar) {
                    return_val = velovalidation.error('minchar_proname').replace(/{%d}/g, minchar);
                }
            } else {
                return_val = velovalidation.error('maxchar_proname').replace(/{%d}/g, maxchar);
            }
        }
        return return_val;
    },
    checkCategoryName: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 255;
        minchar = typeof minchar !== 'undefined' ? minchar : 3;
        if (val != '') {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
            if (val.length <= maxchar) {
                if (val.length < minchar) {
                    return_val = velovalidation.error('minchar_catname').replace(/{%d}/g, minchar);
                }
            } else {
                return_val = velovalidation.error('maxchar_catname').replace(/{%d}/g, maxchar);
            }
        }
        return return_val;
    },
    checkZip: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 10;
        minchar = typeof minchar !== 'undefined' ? minchar : 4;
        if (val != '') {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
            if (val.length <= maxchar) {
                if (val.length < minchar) {
                    return_val = velovalidation.error('minchar_zip').replace(/{%d}/g, minchar);
                }
            } else {
                return_val = velovalidation.error('maxchar_zip').replace(/{%d}/g, maxchar);
            }
            var splChars = "*|,\":<>[]{}`\';()@&$#%";
            for (var i = 0; i < val.length; i++) {
                if (splChars.indexOf(val.charAt(i)) != -1) {
                    return_val = velovalidation.error('specialchar_zip');
                    break;
                }
            }
        }
        return return_val;
    },
    checkUsername: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 100;
        minchar = typeof minchar !== 'undefined' ? minchar : 3;
        if (val != '') {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
            if (val.length <= maxchar) {
                if (val.length < minchar) {
                    return_val = velovalidation.error('minchar_username').replace(/{%d}/g, minchar);
                }
            } else {
                return_val = velovalidation.error('maxchar_username').replace(/{%d}/g, maxchar);
            }
        }
        return return_val;
    },
    checkDateddmmyy: function (ele) {
        var dateformat = /^(0?[1-9]|[12][0-9]|3[01])[\/\-](0?[1-9]|1[012])[\/\-]\d{4}$/;
        var return_val = true;
        var val = ele.val().trim();
        if (val != '') {
            if (val.match(dateformat))
            {
                var opera1 = val.split('/');
                var opera2 = val.split('-');
                lopera1 = opera1.length;
                lopera2 = opera2.length;
                if (lopera1 > 1)
                {
                    var pdate = val.split('/');
                }
                else if (lopera2 > 1)
                {
                    var pdate = val.split('-');
                }
                var dd = parseInt(pdate[0]);
                var mm = parseInt(pdate[1]);
                var yy = parseInt(pdate[2]);
                var ListofDays = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
                if (mm == 1 || mm > 2)
                {
                    if (dd > ListofDays[mm - 1])
                    {
                        return_val = velovalidation.error('invalid_date');
                    }
                }
                if (mm == 2)
                {
                    var lyear = false;
                    if ((!(yy % 4) && yy % 100) || !(yy % 400))
                    {
                        lyear = true;
                    }
                    if ((lyear == false) && (dd >= 29))
                    {
                        return_val = velovalidation.error('invalid_date');
                    }
                    if ((lyear == true) && (dd > 29))
                    {
                        return_val = velovalidation.error('invalid_date');
                    }
                }
            }
            else
            {
                return_val = velovalidation.error('invalid_date');
            }
        } else {
            return_val = velovalidation.error('invalid_date');
        }
        return return_val;
    },
    checkDatemmddyy: function (ele) {
        var dateformat = /^(0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])[\/\-]\d{4}$/;
        var return_val = true;
        var val = ele.val().trim();
        if (val != '') {
            if (val.match(dateformat)) {
                var opera1 = val.split('/');
                var opera2 = val.split('-');
                lopera1 = opera1.length;
                lopera2 = opera2.length;
                // Extract the string into month, date and year  
                if (lopera1 > 1)
                {
                    var pdate = val.split('/');
                }
                else if (lopera2 > 1)
                {
                    var pdate = val.split('-');
                }
                var mm = parseInt(pdate[0]);
                var dd = parseInt(pdate[1]);
                var yy = parseInt(pdate[2]);
                // Create list of days of a month [assume there is no leap year by default]  
                var ListofDays = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
                if (mm == 1 || mm > 2)
                {
                    if (dd > ListofDays[mm - 1])
                    {
                        return_val = velovalidation.error('invalid_date');
                    }
                }
                if (mm == 2)
                {
                    var lyear = false;
                    if ((!(yy % 4) && yy % 100) || !(yy % 400))
                    {
                        lyear = true;
                    }
                    if ((lyear == false) && (dd >= 29))
                    {
                        return_val = velovalidation.error('invalid_date');
                    }
                    if ((lyear == true) && (dd > 29))
                    {
                        return_val = velovalidation.error('invalid_date');
                    }
                }
            }
            else
            {
                return_val = velovalidation.error('invalid_date');
            }
        } else {
            return_val = velovalidation.error('invalid_date');
        }
        return return_val;
    },
    checkSKU: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 64;
        if (val != '') {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
            if (val.length <= maxchar) {
                var splChars = "*|,\":<>[]{}`\';()@&$#%";
                for (var i = 0; i < val.length; i++) {
                    if (splChars.indexOf(val.charAt(i)) != -1) {
                        return_val = velovalidation.error('specialchar_sku');
                        break;
                    }
                }
            } else {
                return_val = velovalidation.error('maxchar_sku').replace(/{%d}/g, maxchar);
            }
        }

        return return_val;
    },
    checkPhoneNumber: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 32;
        var regex = /^[0-9()-]+$/;
        if (val != '') {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
            if (val.length <= maxchar) {
                if (!regex.test(val)) {
                    return_val = velovalidation.error('invalid_phone');
                }
            } else {
                return_val = velovalidation.error('maxchar_phone').replace(/{%d}/g, maxchar);
            }
        }

        return return_val;
    },
    checkAddress: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 128;
        minchar = typeof minchar !== 'undefined' ? minchar : 1;
        if (val != '') {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
            if (val.length <= maxchar) {
                if (val.length < minchar) {
                    return_val = velovalidation.error('minchar_address').replace(/{%d}/g, minchar);
                }
            } else {
                return_val = velovalidation.error('maxchar_adress').replace(/{%d}/g, maxchar);
            }
        }

        return return_val;
    },
    checkCompany: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 32;
        if (val != '') {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
            if (val.length <= maxchar) {
            } else {
                return_val = velovalidation.error('maxchar_company').replace(/{%d}/g, maxchar);
            }
        }

        return return_val;
    },
    checkBrandName: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 64;
        if (val != '') {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
            if (val.length <= maxchar) {
            } else {
                return_val = velovalidation.error('maxchar_brand').replace(/{%d}/g, maxchar);
            }
        }
        return return_val;
    },
    checkCarrierName: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 255;
        if (val != '') {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
            if (val.length <= maxchar) {
            } else {
                return_val = velovalidation.error('maxchar_shipment').replace(/{%d}/g, maxchar);
            }
        }
        return return_val;
    },
    checkIP: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        var testip = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
        if (val != '') {
            if (!val.match(testip)) {
                return_val = velovalidation.error('invalid_ip');
            }
        }
        return return_val;
    },
    checkUrl: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        var myRegExp = /^(?:(?:https?|ftp):\/\/)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/[^\s]*)?$/i;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 255;
        if (val != '') {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
            if (val.length <= maxchar) {
                if (!myRegExp.test(val)) {
                    return_val = velovalidation.error('invalid_url');
                }
            } else {
                return_val = velovalidation.error('max_url').replace(/{%d}/g, maxchar);
            }
        }
        return return_val;
    },
    checkSize: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 10;
        if (val != '') {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
            if (val.length <= maxchar) {
                var splChars = "*|,\":<>[]{}`\';()@&$#%";
                for (var i = 0; i < val.length; i++) {
                    if (splChars.indexOf(val.charAt(i)) != -1) {
                        return_val = velovalidation.error('specialchar_size');
                        break;
                    }
                }
            } else {
                return_val = velovalidation.error('maxchar_size').replace(/{%d}/g, maxchar);
            }
        }
        return return_val;
    },
    checkUPC: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 12;
        if (val != '') {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
            if (val.length <= maxchar) {
                var splChars = "*|,\":<>[]{}`\';()@&$#%";
                for (var i = 0; i < val.length; i++) {
                    if (splChars.indexOf(val.charAt(i)) != -1) {
                        return_val = velovalidation.error('specialchar_upc');
                        break;
                    }
                }
            } else {
                return_val = velovalidation.error('maxchar_upc').replace(/{%d}/g, maxchar);
            }
        }
        return return_val;
    },
    checkEAN: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 14;
        if (val != '') {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
            if (val.length <= maxchar) {
                var splChars = "*|,\":<>[]{}`\';()@&$#%";
                for (var i = 0; i < val.length; i++) {
                    if (splChars.indexOf(val.charAt(i)) != -1) {
                        return_val = velovalidation.error('specialchar_ean');
                        break;
                    }
                }
            } else {
                return_val = velovalidation.error('maxchar_ean').replace(/{%d}/g, maxchar);
            }
        }
        return return_val;
    },
    checkBarcode: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 255;
        if (val != '') {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
            if (val.length <= maxchar) {
                var splChars = "*|,\":<>[]{}`\';()@&$#%";
                for (var i = 0; i < val.length; i++) {
                    if (splChars.indexOf(val.charAt(i)) != -1) {
                        return_val = velovalidation.error('specialchar_bar');
                        break;
                    }
                }
            } else {
                return_val = velovalidation.error('maxchar_bar').replace(/{%d}/g, maxchar);
            }
        }
        return return_val;
    },
    isColor: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        maxchar = typeof maxchar !== 'undefined' ? maxchar : 6;
        if (val != '') {
            var tag_check = velovalidation.checkTags(ele);
            if (tag_check != true) {
                return tag_check;
            }
            var myRegExp = /(^[0-9A-F]{6}$)|(^[0-9A-F]{3}$)/i;
            if (val != '') {
                if (val.length <= maxchar) {
                    if (!myRegExp.test(val)) {
                        return_val = velovalidation.error('invalid_color');
                    }
                } else {
                    return_val = velovalidation.error('maxchar_color').replace(/{%d}/g, maxchar);
                }
            }
        }
        return return_val;
    },
    isSpecialChar: function (ele) {
        var val = ele.val().trim();
        var return_val = true;
        var splChars = "*|,\":<>[]{}`\';()@&$#%";
        for (var i = 0; i < val.length; i++) {
            if (splChars.indexOf(val.charAt(i)) != -1) {
                return_val = velovalidation.error('specialchar');
                break;
            }
        }
        return return_val;
    },
    checkImage: function (ele, setsize, checkby) {
        var val = ele.val().trim();
        var return_val = true;
        /* default size 2 MB */
        setsize = typeof setsize !== 'undefined' ? setsize : 2097152;
        checkby = typeof checkby !== 'undefined' ? checkby : 'kb';
        var show = '';
        var maxval = 0;
        switch (checkby) {
            case 'b':
                show = 'bytes';
                maxval = setsize;
                break;
            case 'kb':
                show = 'KB';
                maxval = setsize / 1024;
                break;

            case 'mb':
                show = 'MB';
                maxval = setsize / (1024 * 1024);
                break;
            default:
                show = 'KB';
                maxval = setsize / 1024;
        }

        if (ele.prop("files")[0].size > setsize) {
            return_val = velovalidation.error('image_size').replace(/{%d}/g, maxval + ' ' + show);
        }
        var Extension = val.substring(val.lastIndexOf('.') + 1).toLowerCase();
        if (Extension == "jpeg" || Extension == "JPEG" || Extension == "png" || Extension == "jpg" || Extension == "gif") {
        } else {
            return_val = velovalidation.error('not_image');
        }
        return return_val;
    },
    checkAllIP: function (ele, separator) {
        var val = ele.val().trim();
        var return_val = true;
        if (val != '') {
            separator = typeof separator !== 'undefined' ? separator : ',';
            var result = val.split(separator);
            var error = false;
            $.each(result, function (key, value) {
                var testip = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
                if (!value.match(testip)) {
                    error = true;
                }
            });
            if (error) {
                return_val = velovalidation.error('invalid_ip');
            }
        }
        return return_val;
    },
    error: function (key) {
        var error_arr = {
            empty_fname: 'Please enter First name.',
            maxchar_fname: 'First name can not be greater than {%d} characters.',
            minchar_fname: 'First name can not be less than {%d} characters.',
            empty_mname: 'Please enter middle name.',
            maxchar_mname: 'Middle name can not be greater than {%d} characters.',
            minchar_mname: 'Middle name can not be less than {%d} characters.',
            only_alphabet: 'Only alphabets are allowed.',
            empty_lname: 'Please enter Last name.',
            maxchar_lname: 'Last name can not be greater than {%d} characters.',
            minchar_lname: 'Last name can not be less than {%d} characters.',
            alphanumeric: 'Field should be alphanumeric.',
            empty_pass: 'Please enter Password.',
            maxchar_pass: 'Password can not be greater than {%d} characters.',
            minchar_pass: 'Password can not be less than {%d} characters.',
            specialchar_pass: 'Password should contain atleast 1 special character.',
            alphabets_pass: 'Password should contain alphabets.',
            capital_alphabets_pass: 'Password should contain atleast 1 capital letter.',
            small_alphabets_pass: 'Password should contain atleast 1 small letter.',
            digit_pass: 'Password should contain atleast 1 digit.',
            empty_field: 'Field can not be empty.',
            number_field: 'You can enter only numbers.',
            positive_number: 'Number should be greater than 0.',
            maxchar_field: 'Field can not be greater than {%d} characters.',
            minchar_field: 'Field can not be less than {%d} character(s).',
            empty_email: 'Please enter Email.',
            validate_email: 'Please enter a valid Email.',
            empty_country: 'Please enter country name.',
            maxchar_country: 'Country can not be greater than {%d} characters.',
            minchar_country: 'Country can not be less than {%d} characters.',
            empty_city: 'Please enter city name.',
            maxchar_city: 'City can not be greater than {%d} characters.',
            minchar_city: 'City can not be less than {%d} characters.',
            empty_state: 'Please enter state name.',
            maxchar_state: 'State can not be greater than {%d} characters.',
            minchar_state: 'State can not be less than {%d} characters.',
            empty_proname: 'Please enter product name.',
            maxchar_proname: 'Product can not be greater than {%d} characters.',
            minchar_proname: 'Product can not be less than {%d} characters.',
            empty_catname: 'Please enter category name.',
            maxchar_catname: 'Category can not be greater than {%d} characters.',
            minchar_catname: 'Category can not be less than {%d} characters.',
            empty_zip: 'Please enter zip code.',
            maxchar_zip: 'Zip can not be greater than {%d} characters.',
            minchar_zip: 'Zip can not be less than {%d} characters.',
            empty_username: 'Please enter Username.',
            maxchar_username: 'Username can not be greater than {%d} characters.',
            minchar_username: 'Username can not be less than {%d} characters.',
            invalid_date: 'Invalid date format.',
            maxchar_sku: 'SKU can not be greater than {%d} characters.',
            minchar_sku: 'SKU can not be less than {%d} characters.',
            invalid_sku: 'Invalid SKU format.',
            empty_sku: 'Please enter SKU.',
            validate_range: 'Number is not in the valid range.',
            empty_address: 'Please enter address.',
            minchar_address: 'Address can not be less than {%d} characters.',
            maxchar_address: 'Address can not be greater than {%d} characters.',
            empty_company: 'Please enter company name.',
            minchar_company: 'Company name can not be less than {%d} characters.',
            maxchar_company: 'Company name can not be greater than {%d} characters.',
            invalid_phone: 'Phone number is invalid.',
            empty_phone: 'Please enter phone number.',
            minchar_phone: 'Phone number can not be less than {%d} characters.',
            maxchar_phone: 'Phone number can not be greater than {%d} characters.',
            empty_brand: 'Please enter brand name.',
            maxchar_brand: 'Brand name can not be greater than {%d} characters.',
            minchar_brand: 'Brand name can not be less than {%d} characters.',
            empty_shipment: 'Please enter Shimpment.',
            maxchar_shipment: 'Shipment can not be greater than {%d} characters.',
            minchar_shipment: 'Shipment can not be less than {%d} characters.',
            invalid_ip: 'Invalid IP format.',
            invalid_url: 'Invalid URL format.',
            empty_url: 'Please enter URL.',
            empty_amount: 'Amount can not be empty.',
            valid_amount: 'Amount should be numeric.',
            max_email: 'Email can not be greater than {%d} characters.',
            specialchar_zip: 'Zip should not have special characters.',
            specialchar_sku: 'SKU should not have special characters.',
            max_url: 'URL can not be greater than {%d} characters.',
            valid_percentage: 'Percentage should be in number.',
            between_percentage: 'Percentage should be between 0 and 100.',
            maxchar_size: 'Size can not be greater than {%d} characters.',
            specialchar_size: 'Size should not have special characters.',
            specialchar_upc: 'UPC should not have special characters.',
            maxchar_upc: 'UPC can not be greater than {%d} characters.',
            specialchar_ean: 'EAN should not have special characters.',
            maxchar_ean: 'EAN can not be greater than {%d} characters.',
            specialchar_bar: 'Barcode should not have special characters.',
            maxchar_bar: 'Barcode can not be greater than {%d} characters.',
            positive_amount: 'Amount should be positive.',
            maxchar_color: 'Color could not be greater than {%d} characters.',
            invalid_color: 'Color is not valid.',
            specialchar: 'Special characters are not allowed.',
            script: 'Script tags are not allowed.',
            style: 'Style tags are not allowed.',
            iframe: 'Iframe tags are not allowed.',
            not_image: 'Uploaded file is not an image',
            image_size: 'Uploaded file size must be less than {%d}.'
        };
        if (typeof this.language[key] === "undefined") {
            return error_arr[key];
        } else {
            return this.language[key];
        }
    },
    setErrorLanguage: function (language_object) {
        if (typeof language_object === "object") {
            this.language = (language_object);
        } else {
            throw "Object was not passed in function setErrorLanguage()";
        }
    }

}