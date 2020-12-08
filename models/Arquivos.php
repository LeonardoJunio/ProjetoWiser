<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Arquivos".
 *
 * @property string $IdArquivo
 * @property string $Diretorio
 * @property string $Descricao
 * @property string $Nome
 * @property string $DataCadastro
 * @property int $IdUsuario
 *
 * @property Usuarios $usuario
 */
class Arquivos extends \yii\db\ActiveRecord
{
    public $extensao;
    public $ArquivoUploaded;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Arquivos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Diretorio', 'Nome', 'DataCadastro', 'IdUsuario'], 'required'],
            [['DataCadastro', 'IdArquivo', 'Descricao'], 'string'],
            [['IdUsuario'], 'integer'],
            [['Diretorio'], 'string', 'max' => 200],
            [['Nome'], 'string', 'max' => 100],
            [['IdUsuario'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['IdUsuario' => 'IdUsuario']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdArquivo' => 'Id Arquivo',
            'Diretorio' => 'Diretorio',
            'Descricao' => 'Descricao',
            'Nome' => 'Nome',
            'DataCadastro' => 'Data Cadastro',
            'IdUsuario' => 'Id Usuario',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuarios::className(), ['IdUsuario' => 'IdUsuario']);
    }
}
