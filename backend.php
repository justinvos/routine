<?php
  date_default_timezone_set('Pacific/Auckland');

  function connect() {
    $config = json_decode(file_get_contents('http://localhost/routine/config.json'), true);

    $db = new PDO('mysql:host=' . $config['db_address'] . ';dbname=' . $config['db_name'] . ';charset=utf8', $config['db_username'], $config['db_password']);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    return $db;
  }

  function authenticate($email, $password) {
    $db = connect();
    $query = $db->prepare("SELECT password, salt FROM accounts WHERE email=:email LIMIT 1;");

    // PARAMETER SETUP
    $query->bindParam(":email", $email);

    $query->execute();
    $dataset = $query->fetchAll();

    if(count($dataset) > 0) {
      if(md5($dataset[0]['salt'] . $password) == $dataset[0]['password']) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  function valid_session($account, $key) {
    $db = connect();
    $query = $db->prepare("SELECT time_created FROM sessions WHERE account=:account AND `key`=:key LIMIT 1;");

    // PARAMETER SETUP
    $query->bindParam(':account', $account);
    $query->bindParam(':key', $key);

    $query->execute();
    $dataset = $query->fetchAll();

    if(count($dataset) > 0) {
      if($dataset[0]['time_created'] + 60 * 180 >= time()) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
?>
