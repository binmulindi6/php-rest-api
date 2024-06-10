<?php

namespace App\Controller;

use App\Config\MailConfig;
use App\Model\Mail;
use App\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;

// save mail in DB
class MailController extends Controller
{

    public static function getMail($id)
    {
        $mails = new Mail();
        return $mails->find($id);
    }
    public static function getMails()
    {
        // return [];
        $mails = new Mail();
        return $mails->all();
    }

    public static function store()
    {
        // var_dump(new Mail());
        if (Request::validate([
            'sender',
            'recever',
            'subject',
            'message',
            'name',
            'telephone',
        ])) {
            $params = Request::params();
            $mail = new Mail();
            // $mail = new Mail();
            $created =  $mail->create(
                [
                    'sender' => $params['sender'],
                    'recever' => $params['recever'],
                    'subject' => $params['subject'],
                    'message' => $params['message'],
                    'sender_name' => $params['name'],
                    'sender_telephone' => $params['telephone'],
                ]
            );

            // return 'success';
            return $created->send();
            // return 'success';
        } else {
            // return "oklm";
            return "check params " . http_response_code(400);
        }
    }

    public static function send_code($to = 'binmulindi.abraham@gmail.com', $code = "12345")
    {


        // $to = 'binmulindi.abraham@gmail.com';
        $subject = 'Email Verification';

        $headers['From'] = MailConfig::$name . ' < ' . MailConfig::$noreply . '>';
        // $headers['From'] = 'very_email@mudecapital.com';
        // $headers['MIME-Version'] = 'MIME-Version: 1.0';
        $headers['Content-type'] = 'text/html; charset=UTF-8';
        // $code = 9393939;
        $message = self::getBody($code);

        $result = mail($to, $subject, $message, $headers);

        if ($result) {
            // echo 'Success!' . PHP_EOL;
            return true;
        } else {
            // echo 'Error.' . PHP_EOL;
            return false;
        }
    }

    public static function test_2()
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'mail.mudecapital.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'noreply@mudecapital.com';
        $mail->Password = 'admin@mudeCapital';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 465;

        $mail->setFrom(address: 'bonjour@mudecapital.com', name: 'Mude Capital');
        $mail->addAddress(address: 'binmulindi.abraham@gmail.com', name: 'Abraham');
        $mail->Subject = 'Mude Capital - Email Verification';

        $code = '44939';

        $mail->isHTML(isHtml: TRUE);
        $mail->Body = '
        <html>
        <head>
        <title>Mude Capital - Email Verification</title>
        </head>
        <body>
                <p>Use the OPT code Bellow to verify your email account:</p>
                <h1>' . $code . ' </h1>
                <br/>
                <br/>
                <br/>
                <h3>Mude Capital</h3>
            </body>
            </html>
            ';
        $mail->AltBody = 'Hi there, we are happy to confirm your booking. Please check the document in the attachment.';

        if (!$mail->send()) {
            echo 'Message could not be sent.';
            return 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            return 'Message has been sent';
        }
    }

    public static function test_3()
    {
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 2;
        $mail->Host = 'mail.mudecapital.com';
        $mail->Port = 465;
        $mail->SMTPAuth = true;
        $mail->Username = 'support@mudecapital.com';
        $mail->Password = 'admin@mudeCapital';
        $mail->setFrom('support@mudecapital.com', 'Mude Capital');
        $mail->addReplyTo('support@mudecapital.com', 'Mude Capital');
        // $mail->addReplyTo('ins-fpkylsoo@isnotspam.com', 'IS NO SPAM');
        $mail->addAddress('ins-fpkylsoo@isnotspam.com', 'IS NO SPAM');
        $mail->Subject = 'Essai de PHPMailer';
        // $mail->msgHTML(file_get_contents('message.html'), __DIR__); //if html
        $mail->Body = 'Ceci est le contenu du message en texte clair';
        //$mail->addAttachment('test.txt');
        if (!$mail->send()) {
            return 'Erreur de Mailer : ' . $mail->ErrorInfo;
        } else {
            return 'Le message a été envoyé.';
        }
    }
    public static function getBody($code)
    {
        return '
                <html>
                    <head>
                        <meta charset="UTF-8" />
                        <title>Mude Capital - Email Verification</title>
                    </head>
                    <body style="padding: 0; margin: 0">
                        <div>
                        <div
                            style="
                            background-color: #eaeaea;
                            padding: 24px;
                            font-family: Verdana, Geneva, Tahoma, sans-serif;
                            display: flex;
                            flex-direction: column;
                            gap: 10px;
                            "
                        >
                        <table>
                            <tr >
                                <td style="padding-bottom: 10px;">Bonjour,</td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 10px;">Voici votre code de verification pour terminer la vérification de votre compte,</td>
                            </tr>
                            <tr>
                                <td ><h2 style="color: #485c76 !important">CODE : ' . $code . '</h2></td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 10px;">
                            </td>
                            </tr>
                        </table>
                        </div>
                        <div
                            style="
                            background-color: #485c76;
                            padding: 24px;
                            font-family: Verdana, Geneva, Tahoma, sans-serif;
                            display: flex;
                            flex-direction: row;
                            gap: 20px;
                            flex-wrap: wrap;
                            align-items: center;
                            "
                        >
                            <img
                            src="https://app.company.com/favicon.png"
                            alt=""
                            style="height: 40px; margin-right: 10px"
                            />
                            <div
                            style="
                                display: flex;
                                flex-direction: column;
                                gap: 1px;
                                color: #fefefe;
                            "
                            >
                            <tr>
                                <td><span style="display: block; color: #fefefe">PHPRESTAPI</span></td>
                            </tr>
                            <tr>
                                <td>      <a style="display: block;color: #fefefe"" href="https://company.com"
                                >company.com</a
                            ></td>
                            </tr>
                            <tr>
                                <td>  <a
                                style="display: block; color: #fefefe"
                                href="mailto:hello@company.com"
                                >hello@company.com</a
                            ></td>
                            </tr>
                            <tr>
                                <td> <a style="display: block; color: #fefefe" href="tel:+0000000000000"
                                >+0000000000000</a
                            ></td>
                            </tr>
                            </table>
                            </div>
                        </div>
                        </div>
                    </body>
                    </html>
        ';
    }
}