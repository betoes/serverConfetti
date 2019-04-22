<?php
    class db{
        private $dbHost = 'localhost';
        private $dbUser = 'Confetti';
        private $dbPass = 'tech_2019';
        private $dbName = 'mydb';

        public function conDB(){
            $mysqlConnect = "mysql:host=$this->dbHost;dbname=$this->dbName";
            $dbConnection = new PDO($mysqlConnect, $this->dbUser, $this->dbPass);
            $dbConnection -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $dbConnection;
        }
    }