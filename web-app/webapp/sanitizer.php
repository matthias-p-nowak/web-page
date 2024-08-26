<?php
namespace WebApp;

/**
 * User input needs to sanitized before it can be presented to other users.
 * Cross site scripting need to be prevented, because there are evil actors out there.
 */
class Sanitizer
{
    /**
     * only allowing text that matches emails
     * @param $input email to sanitize
     */
    public static function SanitizeEmail($input): string
    {
        if (preg_match('/^[a-zA-Z0-9._%+-]+@([a-zA-Z0-9.-]+\.[a-zA-Z]{2,}|localhost)$/', $input)) {
            return $input;
        } else {
            error_log('attempt to hack - email =', print_r($input, true));
            die();
        }
    }

    /**
     * first iteration at a plain text document
     * @param $input turn string into an html-string
     */
    public static function PlainText($input): string
    {
        if(str_contains($input,'<script') || str_contains($input, '<iframes') ){
            error_log('seems to be an attack: '.$input);
            die();
        }
        return htmlspecialchars($input, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8');
    }

}
