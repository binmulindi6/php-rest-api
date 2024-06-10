<?php

namespace App\Model;

use Exception;
use App\Config\MailConfig;

class Mail extends Model
{


    protected $table_name = 'mails';
    protected $class_name = 'App\Model\Mail';
    public $recever;
    public $sender_name;
    public $sender_telephone;
    public $sender;
    public $message;


    function _construct()
    {
    }

    public function send()
    {

        try {
            $to = $this->recever;
            $subject = "Contact From Website";
            $cc = "you@gmail.com";
            $message = "
                <p>Name : " . $this->sender_name . "</p> " .
                "<p> Telephone : " . $this->sender_telephone . "</p>" .
                "<p> Mail : " . $this->sender . "</p>" .
                "<p> Message : " . $this->message . "</p>";

            $headers = "From:PHPRESTAPI <" . $this->sender . ">\r\n" .
                "CC:" . $cc . "" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

            if (mail($to, $subject, $message, $headers)) {
                // return 'success';
                return $this->reception($this->sender, $this->sender_name, $this->message);
            } else {
                $this->delete();
                return 'not sent';
            }
        } catch (Exception $e) {
            return 'Message could not be sent. Mailer Error: ' . $e;
        }
    }

    public function send1()
    {

        try {
            $to = $this->recever;
            $subject = "Contact From Website";
            $cc = "you@gmail.com";
            $message = "
                <p>Name : " . $this->sender_name . "</p> " .
                "<p> Telephone : " . $this->sender_telephone . "</p>" .
                "<p> Mail : " . $this->sender . "</p>" .
                "<p> Message : " . $this->message . "</p>";

            $headers = "From:PHPRESTAPI <" . $this->sender . ">\r\n" .
                "CC:" . $cc . "" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

            if (mail($to, $subject, $message, $headers)) {
                // return 'success';
                return $this->reception($this->sender, $this->sender_name, $this->message);
            } else {
                $this->delete();
                return 'not sent';
            }
        } catch (Exception $e) {
            return 'Message could not be sent. Mailer Error: ' . $e;
        }
    }

    public function reception($mail, $name, $message)
    {
        $to = $mail;
        $subject = "Accusé de Réception";
        $message = "
                <h3>Cher " . $name . ",</h3> " .
            "<p> Nous Accusons la réception de votre message dont le contenu est : </p>" .
            "<p>' " . $message . " '</p>" .
            "<p> Nous Vous rémercions de nous avoir contacté, nous vous prometons de vous revenir dans le plus bref delai.</p>" .
            "<p>  </p>" .
            "<h3> l'équipe d'<b>easyChik.</b> </h3>";

        $headers = "From:PHPRESTAPI <contact@company.com>\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        if (mail($to, $subject, $message, $headers)) {
            return 'success';
        } else {
            $this->delete();
            return 'not sent';
        }
    }
}
