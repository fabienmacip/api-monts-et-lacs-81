<?php
class Database {
  public static function connect() {
      $host = 'localhost';
      //$db = 'monts_et_lacs_81';
      $db = 'mydb';
      $user = 'user';
      $pass = 'password';
      return new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass, [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
      ]);
  }
}
