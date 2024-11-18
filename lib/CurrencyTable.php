<?php
namespace TTMedia;

use \Bitrix\Main\Entity;
use \Bitrix\Main\Type;

class CurrencyTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'b_ttmedia_entity_currency';
    }

    public static function getMap()
    {
        return [
            'ID' => new Entity\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            'CODE' => new Entity\StringField('CODE', [
                'validation' => array(__CLASS__, 'validateCodeLength'),
                'required' => true
            ]),
            'DATE' => new Entity\DatetimeField('DATE', [
                'default_value' => new Type\DateTime,
                'required' => true
            ]),
            'COURSE' => new Entity\FloatField('COURSE', [
                'required' => true
            ]),
        ];
    }

    public static function validateCodeLength()
    {
        return array(
            new Entity\Validator\Length(null, 5),
        );
    }  
}
