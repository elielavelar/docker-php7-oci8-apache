<?php

namespace common\models;

use common\models\interfaces\ResourceInterface;
use Yii;
use common\models\Resource;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

/**
 *
 * @property-read array $resourceTypes
 */
class Equipment extends Resource implements ResourceInterface
{
    const TYPE_CODE = 'EQPT';

    /** @return array */
    public function getResourceTypes(): array
    {
        $types = Resourcetype::find()
            ->select([
                Resourcetype::tableName().'.Id',
                Resourcetype::tableName().'.Name',
            ])
            ->innerJoin( Type::tableName().' a'
                , Resourcetype::tableName().'.IdType = a.Id')
            ->innerJoin( State::tableName().' b'
                , Resourcetype::tableName().'.IdState = b.Id')
            ->where([
                'a.Value' =>    StringHelper::basename(self::class ),
                'b.Code' =>     Resourcetype::STATUS_ACTIVE,
            ])->asArray()->all();
        return ArrayHelper::map($types, 'Id', 'Name');
    }
}
