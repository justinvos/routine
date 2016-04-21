<?php
  function connect() {
    $config = json_decode(file_get_contents('http://localhost/routine/config.json'), true);

    $db = new PDO('mysql:host=' . $config['db_address'] . ';dbname=' . $config['db_name'] . ';charset=utf8', $config['db_username'], $config['db_password']);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    return $db;
  }

  function authenticate($email, $password) {
    $db = connect();
    $query = $db->prepare("SELECT email, password, salt FROM templates WHERE email=:email LIMIT 1;");

    // PARAMETER SETUP
    $query->bindParam(":email", $email);
    $email = $_GET['email'];

    $query->execute();
    $dataset = $query->fetchAll();

    if(count($dataset) > 0) {
      if(md5($password . $dataset[0]['salt']) == md5($dataset[0]['password'])) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
?>
