<?php
require_once 'config.inc.php';
global $db_config;

// initial database connection
$connection = mysql_connect($db_config['host'], $db_config['username'], $db_config['password']) or fail(mysql_error());
mysql_select_db($db_config['database']) or fail(mysql_error());

// start code
if (empty($_REQUEST['action'])) {
  fail();
}

$action = $_REQUEST['action'];
if (!function_exists($action)) {
  fail();
}

$action();
// end code

function create() {
  $fullname = $_REQUEST['fullname'];
  $address = $_REQUEST['address'];
  $phone = $_REQUEST['phone'];
  $email = $_REQUEST['email'];

  if (empty($_REQUEST['fullname']) || empty($_REQUEST['address']) || empty($_REQUEST['phone']) || empty($_REQUEST['email'])) {
    fail('All field required');
  }

  $query = "INSERT INTO customers SET fullname='{$fullname}', address='{$address}', phone='{$phone}', email='{$email}'";

  mysql_query($query) or fail(mysql_error());
  $last_id = mysql_insert_id();
  success('create successful', compact('last_id'));
}

function update() {
  $fullname = $_REQUEST['fullname'];
  $address = $_REQUEST['address'];
  $phone = $_REQUEST['phone'];
  $email = $_REQUEST['email'];
  $id = $_REQUEST['id'];

  if (empty($_REQUEST['fullname']) || empty($_REQUEST['address']) || empty($_REQUEST['phone']) || empty($_REQUEST['email']) || empty($_REQUEST['id'])) {
    fail('All fields required');
  }

  $query = "UPDATE customers SET fullname='{$fullname}', address='{$address}', phone='{$phone}', email='{$email}' WHERE id={$id}";

  mysql_query($query) or fail(mysql_error());
  success('update successful');
}

function delete() {
  $id = $_REQUEST['id'];
  $query = "DELETE FROM customers WHERE id={$id}";

  mysql_query($query) or fail(mysql_error());
  success('delete successful');
}

function success($description='success', $data=array()) {
  $rt = array(
    'status' => 'success',
    'data' => $data,
    'description' => $description,
  );
  echo json_encode($rt);
  exit;
}

function fail($description='unknown error', $data=array()) {
  $rt = array(
    'status' => 'failed',
    'data' => $data,
    'description' => $description
  );
  echo json_encode($rt);
  exit;
}
?>