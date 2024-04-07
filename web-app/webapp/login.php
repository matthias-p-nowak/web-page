<?php
namespace WebApp;

class Login
{
    const ALFABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ23456789';

    function __construct()
    {
        error_log('Entry controller constructed');
    }
    function Login()
    {
        global $config;
        error_log('considering login');
        if (!isset($_SESSION)) {
            session_start();
        }
        if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
            error_log('trying to authenticate');
            $db = Db\DbCtx::GetInstance();
            $user=$db->FindRows('AppUser',['Email' => $_SERVER['PHP_AUTH_USER']]);
            $user=\iterator_to_array($user);
            if(count($user)==1){
                $user=$user[0];
                $authenticated=false;
                foreach($db->FindRows('UserPassword',[ 'UserId' => $user->UserId]) as $pwRow){
                    if(\password_verify($_SERVER['PHP_AUTH_PW'],$pwRow->Password)){
                        $authenticated=true;
                        $foundRow=$pwRow;
                        break;
                    }
                }
                if($authenticated){
                    error_log('user authenticated');
                    $foundRow->Used=date('Y-m-d H:i:s');
                    $db->StoreRow($foundRow);
                    $_SESSION['UserLevel']=$user->Level;
                    $_SESSION['UserId']=$user->UserId;
                    $sv = new ShowView();
                    $sv->ShowForm('LoginSuccess');
                    return;
                }
            }
        }
        if (($_SESSION['loginRN'] ?? 0) == ($_POST['loginRN'] ?? 1)
            && ($_POST['magic'] ?? -1) == ($config->magic_number ?? 0)
            && isset($_POST['email'])
        ) {
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
                $user = $db->FindRows('AppUser', ['Email' => $_POST['email']]);
                $user = \iterator_to_array($user);
                if (count($user) == 0) {
                    $user = new Db\AppUser();
                    $user->Email = $_POST['email'];
                    $user->Level =0;
                    $db->StoreRow($user);
                }elseif(count($user)==1){
                    $user=$user[0];
                    break;
                }
            }
            $upw=new Db\UserPassword();
            $upw->UserId = $user->UserId;
            $upw->Password = $pw_hash;
            $db->StoreRow($upw);
            $message = <<< MESSAGE_END
Hello,

please use the following password next time '$pw'. (Remove the quotes).

Greetings from the admin
MESSAGE_END;
            $r = \mail($_POST['email'], 'password provided', $message);
            error_log("mail returned with ${r}");
            $sv=new ShowView();
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
}
