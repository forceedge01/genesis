<?php

Set::Config('Errors', array(
   
    'showDBErrors' => true,
    'mailDBErrors' => false,
    'mailTriggeredErrors' => false,
    'errorsEmailAddress' => 'error-no-reply@genesis.com',
    'enableHtmlValidation' => false,
    'mailSMTPDebug' => false,
));

define('SHOW_DATABASE_ERRORS', true);

define('MAIL_DATABASE_ERROR', false); //set to false to turn it off.

define('MAIL_TRIGGERED_ERRORS', false);

define('TRIGGERED_ERROR_EMAIL', 'no-reply@genesis.com');

define('ENABLE_HTML_VALIDATION', true);

define('MAIL_SMTP_DEBUG', false);