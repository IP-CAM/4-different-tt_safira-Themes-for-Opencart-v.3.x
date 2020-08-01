<?php
$email = $this->data ? $this->data['email'] : $this->config->get('config_email');

return array(
    1 => array(
        'id' => 1,
        'text' => "Technical problems / Hard to use",
        'details' =>
            '<div class="eapps-deactivation-popup-details-item-header">' . "Please describe your issue in brief." . '</div>
             <div class="eapps-deactivation-popup-details-item-textarea"><textarea name="reason_message" rows="4"></textarea></div>',
        'btn_text' => "Open a ticket",
        'email_text' => "Our support will contact you at",
        'callback' => "We've received your ticket and will contact you at" . ' <b class="eapps-deactivation-popup-callback-email">' . $email . '</b><br>' . "Please keep you plugin activated. Helps on the way"  . ' <span class="eapps-deactivation-smiley">&#128641;</span>'
    ),
    2 => array(
        'id' => 2,
        'text' => "Missing functionality",
        'details' =>
            '<div class="eapps-deactivation-popup-details-item-header">' . "Describe the features you require. There might be a solution." . '</div>
             <div class="eapps-deactivation-popup-details-item-textarea"><textarea name="reason_message" rows="4" autocomplete="off"></textarea></div>',
        'btn_text' => "Send message",
        'email_text' => "Our support will contact you at",
        'callback' => "We've got it and will contact you at" . ' <b class="eapps-deactivation-popup-callback-email">' . $email . '</b>'
    ),
    3 => array(
        'id' => 3,
        'text' => "Free version is too limited",
        'details' =>
            '<div class="eapps-deactivation-popup-details-item-header">' . "We are very sorry that you are facing usage limits" . ' <span class="eapps-deactivation-smiley">&#128549;</span><br>' . "We have a great special offer for you" . ' <span class="eapps-deactivation-smiley">&#128176;</span></div>',
        'btn_text' => "Get offer",
        'email_text' => "You will get your personal offer at",
        'email_subtext' => "the email you've registered your plugin for",
        'callback' => "We've sent your special offer to" . ' <b class="eapps-deactivation-popup-callback-email">' . $email . '</b><br>' . "Hopefully it will change your mind" . ' <span class="eapps-deactivation-smiley">&#128522;</span>'
    ),
    4 => array(
        'id' => 4,
        'text' => "Premium is expensive",
        'details' =>
            '<div class="eapps-deactivation-popup-details-item-header">' . "We have a special discount just for you" . ' <span class="eapps-deactivation-smiley">&#128176;</span></div>',
        'btn_text' => "Get coupon",
        'email_text' => "We'll send your discount coupon at",
        'email_subtext' => "the email you've registered your plugin for",
        'callback' => "We've sent your discount coupon to" . ' <b class="eapps-deactivation-popup-callback-email">' . $email . '</b><br>' . "Hopefully it will change your mind" . ' <span class="eapps-deactivation-smiley">&#128522;</span>'
    ),
    5 => array(
        'id' => 5,
        'text' => "Temporary uninstallation",
        'callback' => "We hope you come back!"
    ),
    6 => array(
        'id' => 6,
        'text' => "Other reason",
        'details' =>
            '<div class="eapps-deactivation-popup-details-item-header">' . "We totally get your feelings, but everything can be solved!" . ' <span class="eapps-deactivation-smiley">&#127804;</span><br>' . "Let us know that the matter is, and we'll look into it." . '</div>
             <div class="eapps-deactivation-popup-details-item-textarea"><textarea name="reason_message" rows="4" autocomplete="off"></textarea></div>',
        'btn_text' => "Send message",
        'email_text' => "Your email (optional)",
        'callback' => "We've got your feedback and in case a solution is required," . '<br>' . "we'll be happy to provide you with it."
    )
);