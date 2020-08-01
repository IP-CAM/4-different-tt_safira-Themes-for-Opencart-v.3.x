(function($) {
    let $popup = jQuery('.eapps-deactivation-popup');
    let $popupOverlay = jQuery('.eapps-deactivation-popup-overlay');

    let $deactivateForm  = jQuery('#whatsapp-chat-deactivateForm');

    let $deactivatePopupCallback = $popup.find('.eapps-deactivation-popup-callback');
    let $deactivatePopupCallbackItems = $deactivatePopupCallback.find('.eapps-deactivation-popup-callback-item');

    let $deactivateFormReasonId = $deactivateForm.find('[name=reason_id]');
    let $deactivatePopupReasonDetailsItems = $popup.find('.eapps-deactivation-popup-details-clone .eapps-deactivation-popup-details-item');
    let $deactivatePopupReasonDetailsContainer = $popup.find('.eapps-deactivation-popup-details');
    let $deactivatePopupReasonDetailsEmail = $deactivateForm.find('[name=email]');

    let $deactivatePopupBody = $popup.find('.eapps-deactivation-popup-body');

    let $proceedDeactivate = $popup.find('#proceedDeactivate');
    let $submitDeactivate = $popup.find('#submitDeactivate');

    let proceedDeactivateForce = false;
    let deactivateFormSubmitted = localStorage.getItem('whatsapp-chat-deactivateFormSubmitted');
    deactivateFormSubmitted = deactivateFormSubmitted ? deactivateFormSubmitted : false;

    let deactivatePopupOpened = true;
    let deactivatePopupTimeout;

    jQuery('.eapps-deactivation-popup-header-close, .eapps-deactivation-popup-overlay').click(() =>{
        closePopup(0)
    });

    $deactivateFormReasonId.on('change', function(){
        let id = jQuery(this).val();

        $deactivatePopupReasonDetailsItems.each(function (i, item) {
            var $item = jQuery(item);

            if ($item.attr('id') === 'deactivate-details-' + id) {
                $deactivatePopupReasonDetailsContainer.html($item);

                // bind submit reason details event
                $submitDetails = $popup.find('.eapps-deactivation-popup-details-item-button button')

                $submitDetails.on('click', function(e){
                    e.preventDefault();

                    submitForm({deactivate: false, show_callback: true}, id);
                });
            }
        });
    });

    function submitForm(deactivate_options, id) {
        var data = $deactivateForm.serializeArray();

        data.push({name: 'action', value: 'whatsapp-chat-deactivate'});
        data.push({name: 'deactivate', value: deactivate_options.deactivate ? 'true' : 'false'});
        data.push({name: 'submit', value: 'true'});

        if (!deactivateFormSubmitted) {
            localStorage.setItem('whatsapp-chat-deactivateFormSubmitted', true);
            deactivateFormSubmitted = true;

            jQuery.post('./controller/extension/module/elfsight_whatsapp_chat.php', jQuery.param(data)).then(function(result) {
                if (deactivate_options.show_callback) {
                    showCallback();
                    $deactivatePopupCallback.show().find('#submit-callback-' + id).show();
                }

                closePopup(5000);
            })
        } else {
            if (deactivate_options.show_callback) {
                showCallback();
                $deactivatePopupCallbackItems.hide();
                $deactivatePopupCallback.find('#submitted-callback').show();
            }
        }
        if (deactivate_options.deactivate) {
            deactivatePlugin(deactivate_options.deactivate_timeout);
        }
    }

    function closePopup(timeout) {
        if (deactivatePopupOpened) {
            deactivatePopupTimeout = setTimeout(function () {
                $popup.remove();
                $popupOverlay.remove();
            }, timeout);
        }
    }

    function showCallback() {
        // toggle body and reset
        $deactivatePopupBody.hide();
        $deactivatePopupCallback.show();
        // hide submit
        $proceedDeactivate.css('display', 'inline-block');
        $submitDeactivate.hide();
        // enable force deactivate
        proceedDeactivateForce = true;
    }
})(window.jQuery);