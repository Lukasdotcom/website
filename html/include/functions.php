<?php
require __DIR__ . '/../vendor/autoload.php'; # Loads all composer files

$jsonInfo = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/config.json");
$jsonData = json_decode($jsonInfo, true);
$developer = $jsonData["developer"];
if ($developer) {
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL); 
}
require_once("ip.php");
function delete_folder($path){ #Used to delete a folder
    if (is_dir($path) === true){
        $files = array_diff(scandir($path), array('.', '..'));
        foreach ($files as $file){
          delete_folder(realpath($path) . '/' . $file);
        }
        return rmdir($path);
      }
    else if (is_file($path) === true){
        return unlink($path);
    }
    return false;
}
/** 
 * Is like the array_key_exists function but returns if the key in the array is equal to the compare value.
 * 
 * @param string $key The key for the area.
 * @param array $array The array to be searched. 
 * @param string $compare The string it should be compared with
 * @return bool False if the key does not exist in array or does not equal compare. True otherwise
 */
function array_key_value(string $key, array $array, string $compare) {
  if (array_key_exists($key, $array)) {
    if ($array[$key] == $compare) {
      return true;
    } else {
      return false;
    }
  } else {
    return false;
  }
}
function sanitize($value) # Used to sanitize a value very strictly
{
  $validChars = "QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm1234567890-#:. ";
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
  $SERVERLOCATION = "localhost";
  if (!file_exists($_SERVER["DOCUMENT_ROOT"] . "/config.json")) {
    exit();
  }
  $jsonInfo = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/config.json");
  $jsonData = json_decode($jsonInfo, true);
  $DATA_USERNAME = $jsonData["database"]["username"];
  $DATABASENAME = $jsonData["database"]["name"];
  $PASSWORD = $jsonData["database"]["password"];
  $connection = mysqli_connect($SERVERLOCATION, $DATA_USERNAME, $PASSWORD, $DATABASENAME);
  return $connection;
}
/**
 * Can send any command to the database that is put into this function
 * prepare is used for prepared statementes
 */
function dbCommand($command, $prepare=[]) {
  $connection = dbConnect();
  $length = count($prepare);
  if ($length == 0) {
    mysqli_query($connection, $command);
  } elseif ($length == 1) {
    $parameter1 = $prepare[0];
    $stmt = mysqli_prepare($connection, $command);
    mysqli_stmt_bind_param($stmt, "s", $parameter1);
    mysqli_stmt_execute($stmt);
  } elseif ($length == 3) {
    $parameter1 = $prepare[0];
    $parameter2 = $prepare[1];
    $parameter3 = $prepare[2];
    $stmt = mysqli_prepare($connection, $command);
    mysqli_stmt_bind_param($stmt, "sss", $parameter1, $parameter2, $parameter3);
    mysqli_stmt_execute($stmt);
  }
  mysqli_close($connection);
}
/** 
 * Sends a request to the database for a search
 * 
 * @param string $result the category you want to search
 * @param string $table what table to search
 * @param mixed $searchCat what category to send a search in: needed only for 0 and 1
 * @param mixed $searchCriteria what the criteria to search is: same as above
 * @param int $Type what type of search 1 is unsupported 0 is when $searchCat and $searchCriteria are equal and 2 is select all
 * @return array|false its false if there is nothing selected otherwise a list that contains all results 
 */
function dbRequest($result, $table, $searchCat, $searchCriteria, $Type)
{
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
      if ($result == "*") {
        array_push($data, $row);
      } else {
        array_push($data, $row[$result]);
      }
    }
    return $data;
  } else {
    return [];
  }
}
/**
 * Improved version of dbRequest() will eventually replace dbRequest
 * 
 * @param string $command the entire sql command
 * @param string $result the column to search will return all if left empty
 * @param array $prepare is a list of all the prepared statemends
 */
function dbRequest2($command, $result="*", $prepare=[])
{
  $connection = dbConnect();
  $length = count($prepare);
  if ($length == 0) {
    $response = mysqli_query($connection, $command);
  } else if ($length == 1) {
    $stmt = mysqli_prepare($connection, $command);
    $parameter1 = $prepare[0];
    echo $parameter1;
    mysqli_stmt_bind_param($stmt, "s", $parameter1);
    mysqli_stmt_execute($stmt);
    $response = mysqli_stmt_get_result($stmt);
  } else if ($length == 3) {
    $stmt = mysqli_prepare($connection, $command);
    $parameter1 = $prepare[0];
    $parameter2 = $prepare[1];
    $parameter3 = $prepare[2];
    mysqli_stmt_bind_param($stmt, "sss", $parameter1, $parameter2, $parameter3);
    mysqli_stmt_execute($stmt);
    $response = mysqli_stmt_get_result($stmt);
  }
  mysqli_close($connection);
  if ($response) {
    if (mysqli_num_rows($response) > 0) {
      $data = [];
      while ($row = mysqli_fetch_assoc($response)) {
        if ($result == "*") {
          array_push($data, $row);
        } else {
          array_push($data, $row[$result]);
        }
      }
      return $data;
    } else {
      return [];
    }
  } else {
    return [];
  }
}
/**
 * deletes values from the database
 * 
 * @param string $table is the table to delete from
 * @param string|array $searchCat the categpry to search in
 * @param string|array $searchCriteria the criteria to search by
 * @param int $Type is for backwards compatabilty put a 0 in here
 */
function dbRemove($table, $searchCat, $searchCriteria, $Type)
{
  $connection = dbConnect();
  if ($Type == 1) {
    mysqli_query($connection, "DELETE FROM $table WHERE $searchCat < $searchCriteria");
  } else {
    if (gettype($searchCat) == "array") {
      $command = "DELETE FROM $table WHERE ";
      $length = count($searchCat);
      for ($i = 0; $i < $length; $i++) {
        $category = $searchCat[$i];
        $criteria = $searchCriteria[$i];
        $command .= "$category='$criteria' and ";
      }
      $command = substr($command, 0, -5);
      mysqli_query($connection, $command);
    } else {
      mysqli_query($connection, "DELETE FROM $table WHERE $searchCat='$searchCriteria'");
    }
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
/**
 * Adds terms to the database
 * 
 * @param array $Term list of all terms to be added
 * @param string $table the table which you want to add to
 */
function dbAdd($Term, $table)
{
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
function root($user)
{
  $connection = dbConnect();
  if (mysqli_num_rows(mysqli_query($connection, "SELECT * FROM privileges WHERE username='$user' AND privilege='root'")) > 0) {
    return True;
  } else {
    return False;
  }
  mysqli_close($connection);
}
/**
 * Writes a log message to the log
 * @param string $message the message to log
 * @param int $type the type of log to see all options look in the database
 */
function writeLog($type, $message)
{
  $time = time();
  dbCommand("INSERT INTO log VALUES ($type, ?, $time)", [$message]);
}
// Creates a way to see uncleaned user input if neccessary
$OGPOST = $_POST;
$OGGET = $_GET;
$OGCOOKIE = $_COOKIE;
// cleans all data
foreach ($_POST as $pointer => $value) {
  $_POST[$pointer] = sanitize($value);
}
foreach ($_GET as $pointer => $value) {
  $_GET[$pointer] = sanitize($value);
}
foreach ($_COOKIE as $pointer => $value) {
  $_COOKIE[$pointer] = sanitize($value);
}
// Removes all expired cookies from the database
$Time = time();
dbCommand("DELETE FROM cookies WHERE expire < $Time and expire != 0");
$PRIVILEGELIST = ["root", "internet", "editUser", "deleteUser", "deleteElectricity", "deleteLog", "viewLog", "changeCredintials", "deleteElectricity", "deleteError", "restartServer", "updateServer", "serverStatus", "viewBackup", "restore", "docker", "dockerAdmin", "mail"]; // A List of all possible privileges
function noUser() { # Used to set everything up as if no yser is logged in
  global $USERNAME, $PRIVILEGE, $PRIVILEGELIST;
  $USERNAME = "";
  foreach ($PRIVILEGELIST as $option) {
    $PRIVILEGE[$option] = false;
  }
}
// Checks the cookie value and sees if the database contains that value
if (array_key_exists("user", $_COOKIE)) {
  $COOKIEID = $_COOKIE["user"];
  $USERNAME = dbRequest("username", "cookies", "cookie", $COOKIEID, 0);
  if ($USERNAME) {
    // Makes sure that the database knows who last accessed that session
    dbCommand("UPDATE cookies SET lastIP='$address' WHERE cookie='$COOKIEID'");
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
          $PRIVILEGE[$type] = True;
        }
      }
    }
  } else {
    noUser();
  }
} else {
  noUser();
}
