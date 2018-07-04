<?php
/*
 * Common .ini file parsing routine
 *
 * Steve Tuck, SparkPost - June 2018
 *
 * External dependencies:
 *  https://github.com/katzgrau/KLogger
 *      use Composer command shown on ths page
 */

require_once '../vendor/autoload.php';

// List mandatory .ini sections here
const mandatory = ["SparkPost", "infilter", "admin"];

// This function reads the ini file into an assoc. array for further processing. Checks if specified sections exist.
function getParams($iniFile)
{
    if(!file_exists($iniFile)) {
        echo("Error: can't find initialisation file:" . $iniFile);
        exit(1);
    }
    $paramArray = parse_ini_file($iniFile, true);

    // check mandatory sections are present
    foreach(mandatory as $i) {
        if (!array_key_exists($i, $paramArray)) {
            echo("Error: missing [" . $i . "] section in " . $iniFile . "\n");
            exit(1);
        }
    }
    return $paramArray;
}

// helper to get config from assoc array
function get_config($avParams, $k)
{
    if (!array_key_exists($k, $avParams)) {
        return null;
    } else {
        return $avParams[$k];
    }
}

// same as above, but we exit if not set
function get_config_mandatory($avParams, $k)
{
    global $app_log;
    $c = get_config($avParams, $k);
    if($c) {
        return $c;
    } else {
        $app_log->error($k . " not defined - check .ini file");
        exit(1);
    }
}

// same as above, but specifically for directory entries.
// Checks if configured directories are set up and readable/writeable according to mode
function chk_config($avParams, $k, $mode)
{
    global $app_log;
    $d = get_config_mandatory($avParams, $k);
    $dpath = realpath($d);
    if (!$dpath) {
        $app_log->error("can't open " . $dpath);
        exit(1);
    }
    if ($mode === "r") {
        if (!is_readable($dpath)) {
            $app_log->error("can't open " . $dpath . " for reading");
            exit(1);
        }
    } elseif ($mode === "w") {
        if (!is_writeable($dpath)) {
            $app_log->error("can't open " . $dpath . " for writing");
            exit(1);
        }
    }
    return $dpath;              // all OK
}

// Wrapper for Klogger that includes the basename of the PHP script in the log format
class App_log
{
    private $logger, $progname;

    // default to current directory if not set
    public function __construct($logdir = __DIR__, $progname)
    {
        $this->logger = new Katzgrau\KLogger\Logger($logdir, Psr\Log\LogLevel::DEBUG,
            array("logFormat" => "{date}|" . $progname . "|{level}|{message}") );
        $this->progname = $progname;
    }

    // pass thru some basic methods
    public function info($arg)
    {
        return $this->logger->info($arg);
    }

    public function warning($arg)
    {
        return $this->logger->warning($arg);
    }

    public function error($arg)
    {
        return $this->logger->error($arg);
    }

    public function debug($arg)
    {
        return $this->logger->debug($arg);
    }
}