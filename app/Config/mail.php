<?php

 /*
 |------------------------------------
 |  PHPMailer library SMTP config
 |------------------------------------
 */



return array(

    'host' => setting('MAIL_HOST'),

    'port' => setting('MAIL_PORT'),

    'username' => setting('MAIL_USER'),

    'password' => setting('MAIL_PASS'),

    'smtp_secure' => setting('MAIL_SMTP_SECURE'),

    'charset' => setting('MAIL_CHARSET', 'utf-8'),

    'html' => setting('MAIL_HTML', true), // true or false

    'show_error' => setting('MAIL_SHOW_ERROR') // value 1 show message and error . value 2 show  error


);
