<?php
#error_reporting(0);
show_source(__FILE__);
class CatchRecord
{

        public $filename, $out, $msg;

        public function __weakup()
        {
                $this->filename = 1;
        }

        public function __destruct()
        {
                if ($this->filename != 0)
                        $this->filename = "error.log";
                else
                        $this->filename = "access.log";
                if (!is_dir(date("Y/m/d/H/i/s") . "/" . $_SERVER['REMOTE_ADDR'])) {
                        mkdir(date("Y/m/d/H/i/s") . "/" . $_SERVER['REMOTE_ADDR'], 0777, true);
                }
                $this->out = date("Y/m/d/H/i/s") . "/" . $_SERVER['REMOTE_ADDR'] . "/" . $this->msg;
                if (!is_dir(date("Y/m/d/H/i/s") . "/" . $_SERVER['REMOTE_ADDR'])) {
                        mkdir(date("Y/m/d/H/i/s") . "/" . $_SERVER['REMOTE_ADDR'], 0777, true);
                }
                file_put_contents($this->filename, $this->out);
        }
}

$ans = $_GET['ans'];
unserialize($ans);
