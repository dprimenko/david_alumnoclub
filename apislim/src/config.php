<?php
define('TABLE', "sites");
define('OK', 200);
define('NOT_COMPLETED', 202);
define('CONFLICT', 409);
class Result {
    var $code;
    var $status;
    var $message;
    var $sites;
    var $lastId;
    function setCode($c) {$this->code = $c;}
    function getCode() {return $this->code;}
    function setStatus($s) {$this->status = $s;}
    function getStatus() {return $this->status;}
    function setMessage($m) {$this->message = $m;}
    function getMessage() {return $this->message;}
    function setSites($s) {$this->sites = $s;}
    function getSites() {return $this->sites;}
    function setLast($s){ $this->lastId = $s; }
}
class Site {
    var $id;
    var $name;
    var $link;
    var $email;
}
class Email {
    var $from;
    var $password;
    var $to;
    var $subject;
    var $message;
}
