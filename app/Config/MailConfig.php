<?php

namespace App\Config;

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;

class MailConfig
{
    public const SMTPDebug = SMTP::DEBUG_OFF; // Enable verbose debug output
    public const Host = 'mail.company.com';
    public const SMTPAuth = true;
    public const Username = 'contact@company.com';
    public const Password = '';
    public const SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    public const Port = 465;


    public static $noreply = 'noreply@company.com';
    public static $contact = 'hello@company.com';
    public static $name = 'PHPRESTAPI';
}
