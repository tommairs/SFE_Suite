<?php
/*
 * Simple AV file scanner
 *
 * Based on https://github.com/Elycin/php-clamav/
 *
 * Steve Tuck, SparkPost - June 2018
 */


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
        global $app_log;
        if(!$this->connect()) {
            $app_log->error("Can't connect to socket " . $this->socket_path);
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
}
