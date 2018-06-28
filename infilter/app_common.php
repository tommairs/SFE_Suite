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
const mandatory = ["SparkPost", "infilter"];

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