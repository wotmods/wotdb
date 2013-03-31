<?php

class WotPlayerTank extends CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public static function getPlayerTank($playerId,$tankId)
	{
		$playerTank=self::model()->findByAttributes(array('player_id'=>$playerId,'tank_id'=>$tankId));
		if(empty($playerTank)){
			$playerTank=new WotPlayerTank();
			$playerTank->player_id=$playerId;
			$playerTank->tank_id=$tankId;
		}
		return $playerTank;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wot_player_tank';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'player' => array(self::BELONGS_TO, 'WotPlayer', 'player_id'),
			'tank' => array(self::BELONGS_TO, 'WotTank', 'tank_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
		);
	}
}