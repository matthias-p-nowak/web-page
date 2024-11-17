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
        if (file_exists($archive)) {
            $lm = filemtime($archive);
        } else {
            $lm = 0;
        }
        $mfs = $config->maxArchive ?? 10e8;
        try {
            $ds = date('YmdHi');
            $media = 'media' . DIRECTORY_SEPARATOR . '*.*';
            $p = explode(DIRECTORY_SEPARATOR, $htmlDir);
            $htmlLen = count($p);
            foreach (['*.html', '*.css', $media] as $fpattern) {
                foreach (glob($htmlDir . DIRECTORY_SEPARATOR . $fpattern, GLOB_NOSORT) as $fn) {
                    if (is_link($fn)) {
                        continue;
                    }
                    if (filemtime($fn) > $lm) {
                        error_log(__FILE__ . ':' . __LINE__ . ' ' . __FUNCTION__ . ' ' . $fn);
                        if (is_null($za)) {
                            $za = new \ZipArchive();
                            $za->open($archive, \ZipArchive::CREATE);
                            if (filesize($archive) > $mfs) {
                                error_log(__FILE__ . ':' . __LINE__ . ' ' . __FUNCTION__ . ' ' . filesize($archive));
                                $cnt = $za->count();
                                $cnt = $cnt / 20 + 2;
                                for ($i = 0; $i < $cnt; $i += 1) {
                                    if (!$za->deleteIndex($i)) {
                                        error_log("can't delete index 0 the $i time");
                                    };
                                }
                            }
                        }
                        $p = explode(DIRECTORY_SEPARATOR, $fn, $htmlLen + 1);
                        $sn = $p[count($p) - 1] . '-' . $ds;
                        // $sn = $p[count($p) -1];
                        if (!$za->addFile($fn, $sn, 0, 0, \ZipArchive::FL_OVERWRITE)) {
                            error_log("can't add $fn to ZipArchive $archive");
                        };
                    }
                }
            }
        } finally {
            if(isset($za)){
                $za?->close();
            }
        }
    }

}
