<?php
namespace WebApp;

class Login
{
    const ALFABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ23456789';

    function __construct()
    {
        error_log('Entry controller constructed');
    }

    function Login(): void
    {
        global $config,$scriptURL;
        error_log('considering login');
        if (!isset($_SESSION)) {
            session_start();
        }
        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            error_log('trying to authenticate');
            $db = Db\DbCtx::GetInstance();
            $user = $db->FindRow('AppUser', ['Email' => $_SERVER['PHP_AUTH_USER']]);
            if ($user) {
                $authenticated = false;
                foreach ($db->FindRows('UserPassword', ['UserId' => $user->UserId]) as $pwRow) {
                    if (\password_verify($_SERVER['PHP_AUTH_PW'], $pwRow->Password)) {
                        $authenticated = true;
                        $foundRow = $pwRow;
                        break;
                    }
                }
                if ($authenticated) {
                    error_log('user authenticated');
                    $foundRow->Used = date('Y-m-d H:i:s');
                    $db->StoreRow($foundRow);
                    $_SESSION['Level'] = $user->Level;
                    $_SESSION['UserId'] = $user->UserId;
                    header("Refresh: 2; URL=".$scriptURL);
                    echo('Login was successful');
                    exit(0);
                }
            }
        }
        if (($_SESSION['loginRN'] ?? 0) == ($_POST['loginRN'] ?? 1)
            && ($_POST['magic'] ?? -1) == ($config->magic_number ?? 0)
            && isset($_POST['email'])
        ) {
            $email = Sanitizer::SanitizeEmail($_POST['email']);
            error_log('consider sending email with password');
            $_SESSION['loginRN'] = 'used';
            $db = Db\DbCtx::GetInstance();
            $pw = '';
            $e = 1;
            $l = strlen(SELF::ALFABET);
            while ($e < ($config->pw_complexity ?? 1e12)) {
                $e *= $l;
                $i = random_int(0, $l - 1);
                $pw .= SELF::ALFABET[$i];
            }
            $pw_hash = \password_hash($pw, PASSWORD_BCRYPT);
            for ($trial = 0; $trial < 2; ++$trial) {
                $user = $db->FindRow('AppUser', ['Email' => $email]);
                if ($user) {
                    break;
                } else {
                    $user = new Db\AppUser();
                    $user->Email = $email;
                    $user->Level = Db\AppUser::Newbie;
                    $db->StoreRow($user);
                    // need to fetch row anew to get the right id...
                }
            }
            $upw = new Db\UserPassword();
            $upw->UserId = $user->UserId;
            $upw->Password = $pw_hash;
            $db->StoreRow($upw);
            $message = <<< MESSAGE_END
Hello,

please use the following password next time '$pw'. (Remove the quotes).

Greetings from the admin
MESSAGE_END;
            $r = \mail($email, 'password provided', $message);
            error_log("mail returned with ${r}");
            $sv = new ShowView();
            $sv->ShowForm('PasswordSent');
            return;
        }
        http_response_code(401);
        header('www-authenticate: Basic realm="' . $config->title . '", charset="UTF-8"');
        $sv = new ShowView();
        $rn = \mt_rand();
        $_SESSION['loginRN'] = $rn;
        $sv->loginRN = $rn;
        $sv->ShowForm('SendPasswd');
    }

    function Logout(): void
    {
        global $scriptURL;
        session_destroy();
        setcookie(session_name(), '', 0, '/');
        header("Refresh: 2; URL=".$scriptURL);
        echo('Logout was successful');
        exit(0);
    }
    function Permissions(): void
    {
        global $config;
        if (($_SESSION['Level'] ?? -1) < 0) {
            (new ShowView())->ShowPage('home');
            return;
        }
        $db = Db\DbCtx::GetInstance();
        $user = $db->FindRow('AppUser', ['UserId' => $_SESSION['UserId']]);
        if ($user->Level < Db\AppUser::Admin) {
            try {
                foreach ($config->admin as $pos => $email) {
                    if (strcmp($email, $user->Email) == 0) {
                        $user->Level = Db\AppUser::Admin;
                        $db->StoreRow($user);
                    }
                }
            } catch (\Exception $ex) {
                error_log('could not determine admin rights due to' . $ex);
            }
        }
        $_SESSION['Level'] = $user->Level;
        $sv=new ShowView();
        $sv->level = match($user->Level) { 
            Db\AppUser::Newbie => "logged in but unauthorized",
            Db\AppUser::Editor => "editorial rights",
            Db\AppUser::Admin => "administrator rights",
            default => "unauthorized"
        };
        $sv->ShowForm('rights');
    }
}

error_log(__FILE__ .' read');
