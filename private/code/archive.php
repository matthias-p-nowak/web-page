<?php

namespace Code;

class Archive
{
    private static $savedAlready = false;
    /**
     * @return void
     */
    public static function SaveState(): void
    {
        global $htmlDir, $archive, $config;
        if (self::$savedAlready) {
            return;
        }
        self::$savedAlready = true;
        error_log('got to save state to ' . $archive);
        if (file_exists($archive)) {
            $lm = filemtime($archive);
        } else {
            $lm = 0;
        }
        try {
            $ds = date('YmdHi');
            foreach (['*.html', '*.css'] as $fpattern) {
                foreach (glob($htmlDir . DIRECTORY_SEPARATOR . $fpattern, GLOB_NOSORT) as $fn) {
                    if (is_link($fn)) {
                        continue;
                    }
                    if (filemtime($fn) > $lm) {
                        if (is_null($za)) {
                            $za = new \ZipArchive();
                            $za->open($archive, \ZipArchive::CREATE);
                            $mfs = $config->maxArchive ?? 10e8;
                            if (filesize($archive) > $mfs) {
                                $cnt = $za->count;
                                $cnt = $cnt / 20;
                                for ($i = 0; $i < $cnt; $i += 1) {
                                    if (!$za->deleteIndex(0)) {
                                        error_log("can't delete index 0 the $i time");
                                    };
                                }
                            }
                        }
                        $p = explode(DIRECTORY_SEPARATOR, $fn);
                        $sn = $p[count($p) - 1] . '-' . $ds;
                        // $sn = $p[count($p) -1];
                        if (!$za->addFile($fn, $sn, 0, 0, \ZipArchive::FL_OVERWRITE)) {
                            error_log("can't add $fn to ZipArchive $archive");
                        };
                    }
                }
            }
        } finally {
            if ($za != null) {
                $za->close();
            }
        }
    }

}
