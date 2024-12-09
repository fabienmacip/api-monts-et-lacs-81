<?php
class Database {
  public static function connect() {
      $host = 'localhost:3307';
      $db = 'monts_et_lacs';
      //$db = 'mydb';
      //$user = 'user';
      $user = 'root';
      //$pass = 'password';
      $pass = '';
      return new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass, [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
      ]);
  }
}
