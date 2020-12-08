<?php


namespace app\models;

use DateTime;
use Yii;

class Procedures extends \yii\db\ActiveRecord
{

    //insert($table, $columns)
    //update($table, $columns, $condition = '', $params = [])
    //delete($table, $condition = '', $params = [])

    public static function insertSQL(string $tabela, array $valores)
    {
        $connection = Yii::$app->getDb();
        return $connection->createCommand()->insert($tabela, $valores, true)->queryOne();
    }

    public static function updateSQL(string $tabela, array $valores, array $filtro)
    {
        $connection = Yii::$app->getDb();
        return $connection->createCommand()->update($tabela, $valores, $filtro)->queryOne();
    }

    public static function deleteSQL(string $tabela, array $filtro)
    {
        $connection = Yii::$app->getDb();
        return $connection->createCommand()->delete($tabela, $filtro)->queryOne();
    }


}