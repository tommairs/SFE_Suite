<?php
/*
 * Simple AV file scanner
 *
 * Based on https://github.com/Elycin/php-clamav/wiki/composer-install
 *
 * Steve Tuck, SparkPost - June 2018
 */

require_once "getParams.php";

class Clamd_service
{
    private $socket_path;
    private $socket;
    private $buffer_length = 1024;

    private $character_prefix = "n";

    public function __construct($socket_path = "/tmp/clamd.ctl")
    {
        $this->socket_path = $socket_path;
        return $this->doesSocketExist();
    }

    public function doesSocketExist()
    {
        return is_file($this->socket_path);
    }

    private function connect()
    {
        $this->socket = fsockopen("unix://" . $this->socket_path);
        return $this->socket;
    }

    // low level socket access function - no need to call this directly
    private function send($query)
    {
        if(!$this->connect()) {
            echo("Can't connect to socket " . $this->socket_path);
            exit(1);
        }
        fwrite($this->socket, $query);
        $response = fread($this->socket, $this->buffer_length);
        fclose($this->socket);
        return $response;
    }

    // multipurpose method supporting all clamd commands. Maps method name into uppercase command.
    public function __call($name, $arguments)
    {
        // prevent PHP warning with empty arguments
        if(empty($arguments)) {
            $arguments[0] = "";
        }
        $pending_command = trim(sprintf("%s%s %s",
                $this->character_prefix, strtoupper($name), $arguments[0])) . "\n";
        return $this->send($pending_command);
    }

    private function exceptionSocketDoesNotExist()
    {
        return new \Exception(sprintf("IPC Socket File %s does not exist.", $this->socket_path));
    }
}


//--------------------------------------------------------------------------------------------------------------------
// Main code
//--------------------------------------------------------------------------------------------------------------------
$p = getParams("suite.ini");
$avParams = $p["infilter"];
// Check working message directory set up and accessible
if(!array_key_exists("workdir", $avParams)) {
    echo "avscanner problem - workdir not defined - check .ini file";
    exit(1);
}
$wd = $avParams["workdir"];
if(!$wd) {
    // don't allow empty string, as later, realpath would default to current directory: see
    // http://php.net/manual/en/function.realpath.php
    echo "avscanner problem workdir - workdir empty string - check .ini file";
    exit(1);
}
$workdir_path = realpath($wd);
if(!$workdir_path) {
    echo "avscanner problem - can't open " . $workdir_path ;
    exit(1);
}
if(!is_readable($workdir_path)) {
    echo "avscanner problem - can't open " . $workdir_path . " for reading";
    exit(1);
}
// Find all message files matching our expected file extension
$file_list = glob($workdir_path .  DIRECTORY_SEPARATOR . "*" . $avParams["file_extension"]);

// Open connection towards Clamd, log AV database version number etc
$my_clam = new Clamd_service($avParams["LocalSocket"]);
echo "** Beginning scan with " . trim($my_clam->version()) . ", files in " . $workdir_path . ": " . sizeof($file_list) . "\n";

foreach($file_list as $f) {
    // Extract the RFC822-format mail, complete with headers, MIME parts, attachments to a local directory.
    // This enables the clamd scanner to use multiple scan threads on each mail. See clamdoc.pdf, "MULTISCAN".
    echo $my_clam->multiscan($f);
}
