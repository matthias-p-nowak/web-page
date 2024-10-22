<?php

namespace Code;

class Login
{

    /**
     * reduced base64, removed confusing doubles
     */
    const ALFABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ23456789';


    public static function Login(): void
    {
        global $config, $data;
        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            $user = $_SERVER['PHP_AUTH_USER'];
            $pw = $_SERVER['PHP_AUTH_PW'];
            if (!in_array($user, $config->editors)) {
                self::Unauthorized();
                return;
            }
            if (empty($pw)) {
                if(($data->last_pw_sent ?? 0) < (time() - 300)){
                    self::SendNewPassword($user);
                    $data->last_pw_sent=time();
                }else{
                    self::Unauthorized();
                    return;
                }
                return;
            }
            $passwords=$data->reg_pws[$user] ?? [];
            foreach($passwords as $idx => $pw_hash){
                if(password_verify($pw,$pw_hash)){
                    self::SignIn($user);
                    unset($passwords[$idx]);
                    $passwords[]=$pw_hash;
                    $data->reg_pws[$user]=$passwords;
                    return;
                }
            }
            $pwList=$data->sent_pws ?? [];
            $t_user=''; // to prevent warning on following line
            foreach($pwList as [$t_user,$pw_hash]){
                if($t_user != $user)
                continue;
                if(password_verify($pw, $pw_hash)){
                    self::SignIn($user);
                    $passwords[]=$pw_hash;
                    if(!isset($data->reg_pws))
                        $data->reg_pws=[];
                    while(count($passwords > 10))
                        array_shift($passwords);
                    $data->reg_pws[$user]=$passwords;
                    return;
                }
                
            }
            
        }
        self::Unauthorized();
        // echo <<< EOM
        // <div id="topbox" x-action="replace"><a href="/signin">Login</a></div>
        // EOM;
    }

    private  static function Unauthorized(): void
    {
        global $config;
        http_response_code(401);
        header('www-authenticate: Basic realm="' . $config->title . '", charset="UTF-8"');
        echo <<< EOM
        use email as user name and let password stay empty to request a new password sent by email
        EOM;
    }
    /**
     * @param string $email the email of the user
     */
    private static function SendNewPassword($email): void
    {
        global $config, $data;
        $pw = '';
        $e = 1;
        $l = strlen(SELF::ALFABET);
        while ($e < ($config->pw_complexity ?? 1e12)) {
            $e *= $l;
            $i = random_int(0, $l - 1);
            $pw .= SELF::ALFABET[$i];
        }
        $message = <<< MESSAGE_END
            Hello,

            please use the following password next time '$pw'. (Remove the quotes).

            Greetings from the admin
            MESSAGE_END;
        $pws = $data->sent_pws ?? [];
        $hpw = password_hash($pw, PASSWORD_DEFAULT);
        $pws[] = [$email, $hpw];
        while (count($pws) > 10) {
            array_shift($pws);
        }
        $r = \mail($email, 'password provided', $message);
        error_log("mail returned with ${r}");
        if ($r) {
            echo <<< EOM
            <script>alert('An email has been sent. Wait for email and try again.');</script>
            EOM;
            $data->sent_pws = $pws;
            
        }else{
            http_response_code(500);
            echo <<< EOM
            Could not send an email to $email.
            EOM;
        }
    }
    /**
     * @return void
     * @param mixed $user
     */
    private static function SignIn($user): void
    {
        error_log("signing in user $user");
        session_start();
        // setcookie('simple-web','login',0,'/',httponly: false);
        setcookie('simple-web','login',[ 'expires' => 0, 'httponly'=> false, 'path' => '/', 'samesite' => 'None'] );
        MakeEditor::Add();
    }

    public static function Logout(): void
    {
        session_destroy();
        setcookie('simple-web', '', time() - 3600);
        MakeEditor::Remove();
    }
}
