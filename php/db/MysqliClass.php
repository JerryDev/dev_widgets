<?php



class MysqliClass{


    private $host;

    private $username;

    private $password;

    private $database;

    private $charset;




    public function __construct($host, $username, $password, $database){
        $link = new mysqli($host, $username, $password, $database);
    }

















}


















