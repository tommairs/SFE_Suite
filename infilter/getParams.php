<?php
/*
 * Common .ini file parsing routine
 *
 * Steve Tuck, SparkPost - June 2018
 */

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
