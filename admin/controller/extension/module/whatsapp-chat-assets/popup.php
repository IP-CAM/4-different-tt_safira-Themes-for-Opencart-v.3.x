<link rel="stylesheet" href="./controller/extension/module/whatsapp-chat-assets/popup.css">

<div class="eapps-deactivation-popup-overlay"></div>

<div class="eapps-deactivation-popup">
    <form method="post" id="whatsapp-chat-deactivateForm" action="">
        <div class="eapps-deactivation-popup-header">
            <div class="eapps-deactivation-popup-header-heading">You have succesfully uninstalled Elfsight Whatsapp Chat</div>

            <div class="eapps-deactivation-popup-header-close"><i class="fa fa-times-circle" aria-hidden="true"></i></div>
        </div>

        <div class="eapps-deactivation-popup-body">
            <div class="eapps-deactivation-popup-body-text">Help other OpenCart users by leaving your feedback.</div>

            <div class="eapps-deactivation-popup-reasons">
                <?php foreach ($reasons as $reason) { ?>
                    <div class="eapps-deactivation-popup-reasons-item">
                        <label>
                            <input type="radio"
                                   value="<?php echo $reason['id']; ?>"
                                   name="reason_id" >
                            <span><?php echo $reason['text']; ?></span>
                        </label>
                    </div>
                <?php } ?>
            </div>

            <div class="eapps-deactivation-popup-details"></div>
        </div>

        <div class="eapps-deactivation-popup-callback">
            <div class="eapps-deactivation-popup-callback-item"
                 id="submit-callback-0">
                <span class="eapps-deactivation-popup-callback-smiley">&#9785;</span>
                <br>"We hope you come back!"
            </div>

            <?php foreach( $reasons as $reason_slug => $reason ) { ?>
                <div class="eapps-deactivation-popup-callback-item"
                     id="submit-callback-<?php echo $reason['id'];?>">
                    <?php echo $reason['callback'];?>
                </div>
            <?php } ?>

            <div class="eapps-deactivation-popup-callback-item" id="deactivate-callback">
                "We hope you come back!"
            </div>

            <div class="eapps-deactivation-popup-callback-item" id="submitted-callback">
                You have already sent a message. <br><br>If you have not received a response, please contact us by email <a href="mailto:support@elfsight.com">support@elfsight.com</a>
            </div>

            <div class="eapps-deactivation-popup-callback-item" id="error-callback">
                The request failed. Please contact us by email <a href="mailto:support@elfsight.com">support@elfsight.com</a>
            </div>
        </div>
    </form>

    <div class="eapps-deactivation-popup-details-clone">
        <?php foreach ($reasons as $reason) { ?>
            <div class="eapps-deactivation-popup-details-item" id="deactivate-details-<?php echo $reason['id']; ?>">
                <?php if (!empty($reason['details'])) { ?>
                    <?php echo $reason['details']; ?>
                <?php } ?>

                <?php if (!empty($reason['email_text'])) { ?>
                    <div class="eapps-deactivation-popup-details-item-email">
                            <span>
                                <?php echo $reason['email_text']; ?><br>

                                <?php if (!empty($reason['email_subtext'])) { ?>
                                    <small>(<?php echo $reason['email_subtext']; ?>)</small>
                                <?php } ?>
                            </span>

                        <input name="email" value="<?php if ($reason['id'] !== 6) echo $this->config->get('config_email'); ?>">
                    </div>
                <?php } ?>

                <?php if (!empty($reason['btn_text'])) { ?>
                    <div class="eapps-deactivation-popup-details-item-button"><button><?php echo $reason['btn_text']; ?></button></div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>

<script>
    jQuery('#instagram-feed-script').remove();
    var node = document.createElement('script');
    node.async = true;
    node.src = './controller/extension/module/whatsapp-chat-assets/popup.js';
    node.id = 'instagram-feed-script';
    $('head')[0].appendChild(node);
</script>