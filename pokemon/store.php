<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if(!isset($_SESSION['user'])) {
    header('Location:.');
    exit;
}

try {
    $connection = new \PDO(
      'mysql:host=localhost;dbname=pokemondatabase',
      'pokemonuser',
      'pokemonpassword',
      array(
        PDO::ATTR_PERSISTENT => true,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8')
    );
} catch(PDOException $e) {
    header('Location: create.php?op=errorconnection&result=0');
    exit;
}
 
$resultado = 0;
$url = 'create.php?op=insertpokemon&result=' . $resultado;

if(isset($_POST['name']) && isset($_POST['evolution']) ) {
    $name = $_POST['name'];
    $evolution = $_POST['evolution'];
    $ok = true;
    $name = trim($name);

    if(strlen($name) < 2 || strlen($name) > 100) {
        $ok = false;
    }
    if(!(is_numeric($evolution) && $evolution >= 0 && $evolution <= 1000000)) {
        $ok = false;
    }

    if($ok) {
        $sql = 'insert into pokemon (name, evolution) values (:name, :evolution)';
        $sentence = $connection->prepare($sql);
        $parameters = ['name' => $name, 'evolution' => $evolution];
        foreach($parameters as $nombreParametro => $valorParametro) {
            $sentence->bindValue($nombreParametro, $valorParametro);
        }

        try {
            $sentence->execute();
            $resultado = $connection->lastInsertId();
            $url = 'index.php?op=insertpokemon&result=' . $resultado;
        } catch(PDOException $e) {
        }
    }
}
if($resultado == 0) {
    $_SESSION['old']['name'] = $name;
    $_SESSION['old']['evolution'] = $evolution;
}

header('Location: ' . $url);