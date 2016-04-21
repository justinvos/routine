<?php

  include('../backend.php');

  parse_str(file_get_contents('php://input'), $_ARGS);

  $response = array(
    'error' => true
  );

  if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_ARGS['email']) && isset($_ARGS['password'])) {
      if(authenticate($_ARGS['email'], $_ARGS['password'])) {
        // DATABASE AND QUERY SETUP #1
        $db = connect();
        $query = $db->prepare("SELECT id FROM accounts WHERE `email`=:email LIMIT 1;");

        // PARAMETER SETUP
        $query->bindParam(':email', $email);
        $email = $_ARGS['email'];

        $query->execute();
        $dataset = $query->fetchAll();

        if(count($dataset) > 0) {
          // QUERY SETUP #2
          $query = $db->prepare("INSERT INTO sessions (account, `key`, time_created) VALUES (:account, :key, :time_created);");

          // PARAMETER SETUP
          $query->bindParam(':account', $account);
          $query->bindParam(':key', $key);
          $query->bindParam(':time_created', $time_created);
          $account = $dataset[0]['id'];
          $key = md5(mt_rand(1000, 999999999));
          $time_created = time();

          $query->execute();

          $response['key'] = $key;
          $response['error'] = false;
        } else {
          $response['error_msg'] = 'The table does not contain a row with that id.';
        }
      } else {
        $response['error_msg'] = 'You are not authenticated.';
      }
    } else {
      $response['error_msg'] = 'The email or password parameter(s) were not given.';
    }
  } else {
    $response['error_msg'] = 'This resource only allows for POST requests.';
  }

  echo json_encode($response);
?>
