<?php

namespace App\Controller\Auth;

use App\Http\Request;
use App\Model\User;
use App\Controller\Controller;
use App\Controller\MailController;
use App\Model\PersonalToken;

// save mail in DB
class AuthenticationController extends Controller
{

    public static function register()
    {

        if (Request::validate([
            'names',
            'email',
            'telephone',
            'password',
        ])) {
            $params = Request::params();
            $instance = new User();
            // // $mail = new Mail();
            // if ($instance->checkemail($params["email"])) {
            //     http_response_code(400);
            //     return "email already exists";
            // }
            if ($instance->checkEmail($params["email"])) {
                http_response_code(400);
                return "Email already taken";
            }
            if ($instance->checkPhone($params["telephone"])) {
                http_response_code(400);
                return "Phone Number already taken";
            }
            // if (isset($params["promo_code"])) {
            //     $parrain = $instance->findByOptions(["promo_code" => $params["promo_code"]]);
            // }

            $verify_code = mt_rand(11111, 99999);
            $promo = mt_rand(1111, 9999);
            $user = $instance->create(
                [
                    'names' => $params["names"],
                    'email_verification_code' => $verify_code,
                    'email' => $params["email"],
                    'telephone' => $params["telephone"],
                    'password' => password_hash($params["password"], PASSWORD_DEFAULT),
                ]
            );


            if ($user) {
                if (MailController::send_code($user->email, $verify_code)) {
                    return $user;
                } else {
                    http_response_code(400);
                    return "email or not sent try again";
                }
            } else {
                // return "oklm";
                http_response_code(400);
                return "User not created ";
            }
        } else {
            // return "oklm";
            http_response_code(400);
            return "please check params ";
        }
    }
    public static function login()
    {
        // password_verify($userEnteredPassword, $storedHashedPassword)
        // var_dump($_SERVER['HTTP_AUTHORIZATION']);
        if (Request::validate([
            'email',
            'password',
        ])) {
            $params = Request::params();
            $instance = new User();
            $user = $instance->findByOptions(["email" => $params['email']]) ? $instance->findByOptions(["email" => $params['email']]) : $instance->findByOptions(["email" => $params['email']]);
            if (!is_null($user) && password_verify($params['password'], $user->password)) {

                if ($user->email_verified_at !== NULL) {
                    if ((int)$user->is_active === 1) {

                        $token = ($user->getLastToken()  && $user->getLastToken()[0]) ?  $user->getLastToken()[0]->token : self::generateToken($user);
                        // return $token
                        return
                            [
                                "user" => $user,
                                "token" => $token,
                            ];
                    } else {
                        http_response_code(403);
                        return "Acces au Système non autorisé!";
                    }
                } else {
                    if (MailController::send_code($user->email, $user->email_verification_code)) {
                        return
                            [
                                "email" => $user->email,
                            ];
                    } else {
                        http_response_code(400);
                        return "email not sent try again";
                    }
                }
            } else {
                // return "oklm";
                http_response_code(400);
                return "email or password incorrect";
            }
        } else {
            // return "oklm";
            http_response_code(400);
            return "please check params ";
        }
    }
    public static function loginAdmin()
    {
        // password_verify($userEnteredPassword, $storedHashedPassword)
        // var_dump($_SERVER['HTTP_AUTHORIZATION']);
        if (Request::validate([
            'email',
            'password',
        ])) {
            $params = Request::params();
            $instance = new User();
            $user = $instance->findByOptions(["email" => $params['email']]) ? $instance->findByOptions(["email" => $params['email']]) : $instance->findByOptions(["email" => $params['email']]);
            if (!is_null($user) && password_verify($params['password'], $user->password) && (int)$user->is_admin === 1) {

                if ($user->email_verified_at !== NULL) {

                    $token = ($user->getLastToken()  && $user->getLastToken()[0]) ?  $user->getLastToken()[0]->token : self::generateToken($user);
                    // return $token
                    return
                        [
                            "user" => $user,
                            "token" => $token,
                        ];
                } else {
                    if (MailController::send_code($user->email, $user->email_verification_code)) {

                        return
                            [
                                "email" => $user->email,
                            ];
                    } else {
                        http_response_code(400);
                        return "email or not sent try again";
                    }
                }
            } else {
                // return "oklm";
                http_response_code(400);
                return "email or password incorrect";
            }
        } else {
            // return "oklm";
            http_response_code(400);
            return "please check params ";
        }
    }

    public static function verify_email()
    {

        if (Request::validate([
            'email',
            // 'code',
        ])) {
            $params = Request::params();
            $instance = new User();
            ///mailing
            if ($instance->checkEmail($params["email"])) {
                $verify_code = mt_rand(11111, 99999);
                $user = $instance->findByOptions(['email' => $params["email"]]);
                $user->save([
                    'email_verification_code' => $verify_code,
                ]);
                if (MailController::send_code($user->email, $verify_code)) {
                    return 'success';
                } else {
                    http_response_code(400);
                    return "email or not sent try again";
                }
            } else {
                http_response_code(400);
                return "Email user doesn't exist";
            }
        }
    }
    public static function verify_token()
    {

        if (Request::validate([
            'email',
            // 'code',
        ])) {
            $params = Request::params();
            $passport  = new PersonalToken();

            return $passport->check($params['token']);
        }
    }
    public static function verify_code()
    {


        if (Request::validate([
            'email',
            'code',
        ])) {
            $params = Request::params();
            $instance = new User();
            ///mailing
            if ($instance->checkEmail($params["email"])) {
                $user = $instance->findByOptions(['email' => $params["email"], 'email_verification_code' => $params["code"]]);
                if ($user) {
                    if ($user->email_verified_at === NULL) {
                        $user->makeDeposit(0);
                        ///validate parrainage here
                        $parrain = $user->parrain();
                        if ($parrain) $parrain->makeDeposit(0.05);
                    }

                    $user->save([
                        "email_verified_at" => date('Y-m-d H:i'),
                    ]);


                    $token = ($user->getLastToken()  && $user->getLastToken()[0]) ?  $user->getLastToken()[0]->token : self::generateToken($user);
                    // return $token
                    return
                        [
                            "user" => $user,
                            "token" => $token,
                        ];
                } else {
                    http_response_code(400);
                    return "Code OTP Invalide";
                }
            } else {
                http_response_code(400);
                return "Email user doesn't exist";
            }
        } else {
            // return "oklm";
            http_response_code(400);
            return "please check params ";
        }
    }
    public static function generateToken($user)
    {
        $tok = new PersonalToken();
        $ability = $user->isAdmin() ? 'admin' : 'simple';
        $token = $tok->generate($user->id, $ability);
        return $token->token;
    }
    public static function user()
    {
        $token = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null;
        $passport  = new PersonalToken();
        // $passport->check()
        return $passport->findByOptions(['token' => $token]) ? $passport->findByOptions(['token' => $token])->user() : false;
    }
}
