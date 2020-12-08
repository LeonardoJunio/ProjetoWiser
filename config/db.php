<?php

$db_name = 'SqLite.db';
$db_drvr = 'sqlite';

$create_usuarios = "CREATE TABLE IF NOT EXISTS Usuarios (
                        IdUsuario INTEGER PRIMARY KEY AUTOINCREMENT,
                        Usuario varchar(100) not null,
                        Senha varchar(100) not null,
                        DataCadastro text not null,
                        ClientId text null,
                        ClientSecret text null,
                        UserId text null,
                        Status int not null
                     )";

$create_arquivos = "CREATE TABLE IF NOT EXISTS Arquivos (
                        IdArquivo varchar(50) PRIMARY KEY,
                        Diretorio varchar(250) not null,
                        Nome varchar(100) not null,
                        Descricao text null,
                        DataCadastro text not null,
                        IdUsuario Integer not null,
                        FOREIGN KEY (IdUsuario) REFERENCES Usuarios(IdUsuario)
                    )";

try{
    $pdo = new PDO("$db_drvr:$db_name");

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $exc){
    trigger_error($exc->getMessage(), E_USER_ERROR);
}

$pdo->query( $create_usuarios );
$pdo->query( $create_arquivos );

return [
    'class' => 'yii\db\Connection',
    'dsn' => "$db_drvr:$db_name",
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
];

//return [
//    'class' => 'yii\db\Connection',
//    'dsn' => 'mysql:host=localhost;dbname=ycrud',
//    'username' => 'root',
//    'password' => '',
//    'charset' => 'utf8',
//];


    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',





