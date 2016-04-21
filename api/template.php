<?php

  include('../backend.php');

  parse_str(file_get_contents('php://input'), $_ARGS);

  $response = array(
    'error' => true
  );

  if($_SERVER['REQUEST_METHOD'] == 'GET') {
    if(isset($_GET['id']) && isset($_GET['account']) && isset($_GET['key'])){
      if(valid_session($_GET['account'], $_GET['key'])) {
        // DATABASE AND QUERY SETUP
        $db = connect();
        $query = $db->prepare("SELECT label, account FROM templates WHERE id=:id LIMIT 1;");

        // PARAMETER SETUP
        $query->bindParam(":id", $id);
        $id = $_GET['id'];

        $query->execute();
        $dataset = $query->fetchAll();

        if(count($dataset) > 0) {
          $response['label'] = $dataset[0]['label'];
          $response['account'] = $dataset[0]['account'];
          $response['error'] = false;
        } else {
          $response["error_msg"] = 'The table does not contain a row with that id.';
        }
      } else {
        $response["error_msg"] = 'Your session is invalid.';
      }
    } else {
      $response['error_msg'] = 'The id parameter was not given.';
    }
  } else if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_ARGS['label']) && isset($_ARGS['account']) && isset($_ARGS['key'])) {
      if(valid_session($_ARGS['account'], $_ARGS['key'])) {
        // DATABASE AND QUERY SETUP
        $db = connect();
        $query = $db->prepare("INSERT INTO templates (label, account) VALUES (:label, :account);");

        // PARAMETER SETUP
        $query->bindParam(":label", $label);
        $query->bindParam(":account", $account);
        $label = $_POST['label'];
        $account = $_POST['account'];

        $query->execute();

        $response['id'] = $db->lastInsertId();
        $response['error'] = false;
      } else {
        $response["error_msg"] = 'Your session is invalid.';
      }
    } else {
      $response['error_msg'] = 'The label or account parameter(s) were not given.';
    }
  } else if($_SERVER['REQUEST_METHOD'] == 'PUT') {
    if(isset($_ARGS['id']) && isset($_ARGS['label']) && isset($_ARGS['account']) && isset($_ARGS['key'])) {
      if(valid_session($_ARGS['account'], $_ARGS['key'])) {
        // DATABASE AND QUERY SETUP
        $db = connect();
        $query = $db->prepare("UPDATE templates SET label=:label WHERE id=:id;");

        // PARAMETER SETUP
        $query->bindParam(":id", $id);
        $query->bindParam(":label", $label);
        $id = $_ARGS['id'];
        $label = $_ARGS['label'];

        $query->execute();
        $response['error'] = false;
      } else {
        $response["error_msg"] = 'Your session is invalid.';
      }
    } else {
      $response['error_msg'] = 'The id or label parameter(s) were not given.';
    }
  } else if($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if(isset($_ARGS['id']) && isset($_ARGS['account']) && isset($_ARGS['key'])) {
      if(valid_session($_ARGS['account'], $_ARGS['key'])) {
        // DATABASE AND QUERY SETUP
        $db = connect();
        $query = $db->prepare("DELETE FROM templates WHERE id=:id;");

        // PARAMETER SETUP
        $query->bindParam(":id", $id);
        $id = $_ARGS['id'];

        $query->execute();
        $response['error'] = false;
      } else {
        $response["error_msg"] = 'Your session is invalid.';
      }
    } else {
      $response['error_msg'] = 'The id parameter was not given.';
    }
  } else {
    $response['error_msg'] = 'This resource only allows for DELETE, GET, PUT and POST requests.';
  }

  echo json_encode($response);
?>
