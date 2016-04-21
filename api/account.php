<?php

  include('../backend.php');

  parse_str(file_get_contents('php://input'), $_ARGS);

  $response = array(
    'error' => true
  );

  if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_ARGS['email']) && isset($_ARGS['password'])) {
      // DATABASE AND QUERY SETUP
      $db = connect();
      $query = $db->prepare("INSERT INTO accounts (email, password, salt) VALUES (:email, :password, :salt);");

      // PARAMETER SETUP
      $query->bindParam(":email", $email);
      $query->bindParam(":password", $password);
      $query->bindParam(":salt", $salt);
      $email = $_ARGS['email'];
      $salt = md5(mt_rand(1000, 999999999));
      $password = md5($salt . $_ARGS['password']);


      $query->execute();

      $response['id'] = $db->lastInsertId();
      $response['error'] = false;
    } else {
      $response['error_msg'] = 'The email or password parameter(s) were not given.';
    }
  } else {
    $response['error_msg'] = 'This resource only allows for POST requests.';
  }

  echo json_encode($response);
?>
