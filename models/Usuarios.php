<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "Usuarios".
 *
 * @property int $IdUsuario
 * @property string $Usuario
 * @property string $Senha
 * @property string $DataCadastro
 * @property string $ClientId
 * @property string $ClientSecret
 * @property string $UserId
 * @property int $Status
 */
class Usuarios extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    public $password;
    public $passwordConfirm;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Usuarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Usuario', 'Senha', 'DataCadastro', 'Status'], 'required'],
            [['DataCadastro', 'ClientId', 'ClientSecret', 'UserId'], 'safe'],
            [['Status'], 'integer'],
            [['Usuario', 'Senha'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdUsuario' => 'Id Usuario',
            'Usuario' => 'Usuario',
            'Senha' => 'Senha',
            'DataCadastro' => 'Data Cadastro',
            'ClientId' => 'Client ID',
            'ClientSecret' => 'Client Secret',
            'UserId' => 'User ID',
            'Status' => 'Status',
        ];
    }

    public static function findByUsername($username){
        $permissionUsername = self::findOne(['Usuario' => $username]);

        return $permissionUsername;
    }

    public function validatePassword($password){
        return $this->Senha === md5($password);
    }

    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new \yii\base\NotSupportedException();
    }

    public function getId()
    {
        return $this->IdUsuario;
    }

    public function getAuthKey()
    {
        return $this->Usuario;
    }

    public function validateAuthKey($authKey)
    {
        return $this->Usuario === $authKey;
    }

    public function search($params)
    {
        $query = Usuarios::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'IdUsuario' => $this->IdUsuario,
            'Usuario' => $this->Usuario,
            'Senha' => $this->Senha,
            'DataCadastro' => $this->DataCadastro,
            'Status' => $this->Status,
        ]);

        return $dataProvider;
    }
}
