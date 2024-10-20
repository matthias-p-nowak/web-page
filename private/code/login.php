<?php
namespace Code;

/**
 *
 */
class Login
{

    /**
     * reduced base64, removed confusing doubles
     */
    const ALFABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ23456789';

    function __construct()
    {
        error_log('constructing login Object');
    }

    function Login(): void
    {
        global $config, $data;
        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            $user = $_SERVER['PHP_AUTH_USER'];
            $pw = $_SERVER['PHP_AUTH_PW'];
            if (!in_array($user, $config->editors)) {
                $this->Unauthorized();
                return;
            }
            if (empty($pw)) {
                $this->SendNewPassword($user);
                return;
            }
            error_log("trying to authenticate $user");
            return;
        }
        $this->Unauthorized();
        // echo <<< EOM
        // <div id="topbox" x-action="replace"><a href="/signin">Login</a></div>
        // EOM;
    }
    function Unauthorized(): void
    {
        http_response_code(401);
        header('www-authenticate: Basic realm="' . $config->title . '", charset="UTF-8"');
        echo <<< EOM
        use email as user name and let password stay empty to request a new password sent by email
        EOM;
    }
    /**
     * @param string $user
     */
    function SendNewPassword($user): void
    {
        global $config;
        $pw = '';
        $e = 1;
        $l = strlen(SELF::ALFABET);
        while ($e < ($config->pw_complexity ?? 1e12)) {
            $e *= $l;
            $i = random_int(0, $l - 1);
            $pw .= SELF::ALFABET[$i];
        }
    }
}
