<?php
namespace DancePressTRWA\Library;

/**
 * Logger.
 * Log output to file
 * @package DancePressTRWA\Library
 * @since 1.0
 */
class Logger
{
    private $file = '';
    private $fp = false;

    public function __construct($file = false)
    {
        $this->file = $file;
    }

    public function __destruct()
    {
        if ($this->fp) {
            fclose($this->fp);
        }
    }

    public function log($string)
    {
        if (!$this->fp) {
            $this->open();
        }

        fwrite($this->fp, $string . "\n");
    }

    private function open()
    {
        $this->fp = fopen($this->file, "a"); //open debug file
    }
}
