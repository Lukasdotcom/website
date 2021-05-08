<?php
function sanitize($value)
{
  $validChars = "QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm1234567890";
  $validChars = str_split($validChars);
  $valueSplit = str_split($value);
  $value = "";
  foreach ($valueSplit as $char) {
    if (array_search($char, $validChars) !== false) {
      $value .= $char;
    }
  }
  return $value;
}
function dbConnect()
{ // Is used to connect to the database
  $SERVERNAME = "localhost";
  $DATA_USERNAME = "website";
  $KEY = "q1azcp2!las";
  $connection = mysqli_connect($SERVERNAME, $DATA_USERNAME, $KEY, $DATA_USERNAME);
  return $connection;
}
function dbRequest($result, $table, $searchCat, $searchCriteria, $Type)
{ //Sends a request to the database for a search
  $connection = dbConnect();
  if ($Type == 1) {
    $response = mysqli_query($connection, "SELECT $result FROM $table WHERE $searchCat < $searchCriteria");
  } elseif ($Type == 0) {
    $response = mysqli_query($connection, "SELECT $result FROM $table WHERE $searchCat='$searchCriteria'");
  } else {
    $response = mysqli_query($connection, "SELECT $result FROM $table");
  }
  mysqli_close($connection);
  if (mysqli_num_rows($response) > 0) {
    $data = [];
    while ($row = mysqli_fetch_assoc($response)) {
      array_push($data, $row[$result]);
    }
    return $data;
  } else {
    return False;
  }
}
function dbRemove($table, $searchCat, $searchCriteria, $Type)
{ // Deletes from the database a value
  $connection = dbConnect();
  if ($Type == 1) {
    mysqli_query($connection, "DELETE FROM $table WHERE $searchCat < $searchCriteria");
  } else {
    mysqli_query($connection, "DELETE FROM $table WHERE $searchCat='$searchCriteria'");
  }
  mysqli_close($connection);
}
function dbEdit($table, $replace, $search, $type)
{ // Edits values in the database
  $connection = dbConnect();
  if ($type == 0) {
    $command = "UPDATE $table SET ";
    foreach ($replace as $replacers) {
      $command .= "$replacers[0] = '$replacers[1]', ";
    }
    $command = substr($command, 0, -2);
    $command .= " WHERE $search[0] = '$search[1]'";
    $result = mysqli_query($connection, $command);
  }
  mysqli_close($connection);
}
function dbAdd($Term, $table)
{ // Adds to the database a new term
  $connection = dbConnect();
  $values = "";
  foreach ($Term as $data) {
    $values .= "'";
    $values .= $data;
    $values .= "'";
    $values .= ', ';
  }
  $values = substr($values, 0, -2);
  $result = mysqli_query($connection, "INSERT INTO $table VALUES ($values)");
  mysqli_close($connection);
}
function root($user) {
  $connection = dbConnect();
  if (mysqli_num_rows(mysqli_query($connection, "SELECT * FROM privileges WHERE username='$user' AND privilege='root'")) > 0) {
    return True;
  } else {
    return False;
  }
  mysqli_close($connection);
}
// Creates a way to see uncleaned user input if neccessary
$OGPOST = $_POST;
$OGGET = $_GET;
$OGCOOKIE = $_COOKIE;
// cleans all data
foreach ($_POST as $pointer=>$value) {
  $_POST[$pointer] = sanitize($value);
}
foreach ($_GET as $pointer=>$value) {
  $_GET[$pointer] = sanitize($value);
}
foreach ($_COOKIE as $pointer=>$value) {
  $_COOKIE[$pointer] = sanitize($value);
}
// Contains the favicon and the css stylesheet
echo '<link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="manifest" href="/favicon/site.webmanifest">
    <link rel="stylesheet" type="text/css" href="/css/website.css" />';
// Removes all expired cookies from the database
$Time = mkTime();
dbRemove("cookies", "expire", $Time, 1);
$PRIVILEGELIST = ["root", "editUser", "deleteUser", "deleteElectricity", "deleteLog", "viewLog", "changeCredintials"];
// Checks the cookie value and sees if the database contains that value
$COOKIEID = $_COOKIE["user"];
if ($COOKIEID) {
  $USERNAME = dbRequest("username", "cookies", "cookie", $COOKIEID, 0);
  if ($USERNAME) {
    setcookie("user", $COOKIEID, time() + 600, "/");
    $USERNAME = $USERNAME[0];
    $PRIVILEGES = dbRequest("privilege", "privileges", "username", $USERNAME, 0);
    if (!$PRIVILEGES) {
      $PRIVILEGES = ["sajdhsakjdjksshsadksagd"];
    }
    foreach ($PRIVILEGELIST as $option) {
      if (array_search($option, $PRIVILEGES) !== false) {
        $PRIVILEGE[$option] = True;
      } else {
        $PRIVILEGE[$option] = false;
      }
    }
    $ROOTUSERS = dbRequest("username", "privileges", "privilege", "root", 0);
    if ($PRIVILEGE["root"]) {
      foreach ($PRIVILEGELIST as $type) {
        if (!$PRIVILEGE[$type]) {
          dbAdd([$USERNAME, $type], "privileges");
        }
      }
    }
  }
}