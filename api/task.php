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
        $query = $db->prepare("SELECT id, label, template FROM tasks WHERE id=:id LIMIT 1;");

        // PARAMETER SETUP
        $query->bindParam(":id", $id);
        $id = $_GET['id'];

        $query->execute();
        $dataset = $query->fetchAll();

        if(count($dataset) > 0) {
          $response['id'] = $dataset[0]['id'];
          $response['label'] = $dataset[0]['label'];
          $response['template'] = $dataset[0]['template'];
          $response['error'] = false;
        } else {
          $response["error_msg"] = 'The table does not contain a row with that id.';
        }
      } else {
        $response["error_msg"] = 'Your session is invalid.';
      }
    } else if(isset($_GET['template']) && isset($_GET['account']) && isset($_GET['key'])) {
      if(valid_session($_GET['account'], $_GET['key'])) {
        // DATABASE AND QUERY SETUP
        $db = connect();
        $query = $db->prepare("SELECT tasks.id, tasks.label, template FROM tasks INNER JOIN templates ON tasks.template=templates.id WHERE templates.id=:template;");

        // PARAMETER SETUP
        $query->bindParam(':template', $template);
        $template = $_GET['template'];

        $query->execute();
        $dataset = $query->fetchAll();

        if(count($dataset) > 0) {
          $response['tasks'] = array();

          for($i = 0; $i < count($dataset); $i++) {
            $response['tasks'][$i] = array(
              'id' => $dataset[$i]['id'],
              'label' => $dataset[$i]['label'],
              'template' => $dataset[$i]['template'],
            );
          }

          $response['error'] = false;
        } else {
          $response["error_msg"] = 'The table does not contain any rows with that template id.';
        }
      } else {
        $response["error_msg"] = 'Your session is invalid.';
      }
    } else {
      $response['error_msg'] = 'The id or template parameter(s) were not given.';
    }
  } else if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_ARGS['label']) && isset($_ARGS['template']) && isset($_ARGS['account']) && isset($_ARGS['key'])) {
      if(valid_session($_ARGS['account'], $_ARGS['key'])) {
        // DATABASE AND QUERY SETUP
        $db = connect();
        $query = $db->prepare("INSERT INTO tasks (label, template) VALUES (:label, :template);");

        // PARAMETER SETUP
        $query->bindParam(":label", $label);
        $query->bindParam(":template", $template);
        $label = $_POST['label'];
        $template = $_POST['template'];

        $query->execute();

        $response['id'] = $db->lastInsertId();
        $response['error'] = false;
      } else {
        $response["error_msg"] = 'Your session is invalid.';
      }
    } else {
      $response['error_msg'] = 'The label or templates parameter(s) were not given.';
    }
  } else if($_SERVER['REQUEST_METHOD'] == 'PUT') {
    if(isset($_ARGS['id']) && isset($_ARGS['label']) && isset($_ARGS['account']) && isset($_ARGS['key'])) {
      if(valid_session($_ARGS['account'], $_ARGS['key'])) {
        // DATABASE AND QUERY SETUP
        $db = connect();
        $query = $db->prepare("UPDATE tasks SET label=:label WHERE id=:id;");

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
        $query = $db->prepare("DELETE FROM tasks WHERE id=:id;");

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
