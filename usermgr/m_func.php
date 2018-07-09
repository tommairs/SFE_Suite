<?php
/*
 *
 * Shamelessly stolen from Steve Tuck as a hack-patch while I fix autoload.
*/

//require_once '../vendor/autoload.php';

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


//--------------------------------------------------------------------------------------------------------------------
// Helpers for accessing assoc arrays, such as _POST data
//--------------------------------------------------------------------------------------------------------------------

function get_elem($arr, $k)
{
    if (!array_key_exists($k, $arr)) {
        return null;
    } else {
        return $arr[$k];
    }
}

// same as above, but we log error & exit if not set
function get_elem_mandatory($arr, $k)
{
    global $app_log;
    $c = get_config($arr, $k);
    if($c) {
        return $c;
    } else {
        $app_log->error($k . " not defined in " . print_r($arr, true));
        exit(1);
    }
}
