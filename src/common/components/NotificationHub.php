<?php

namespace common\components;

use common\models\User;

/**
 * @property array $notifications;
 * @property int $notificationQuantity
 * @property User $user
 *
 */
class NotificationHub extends \yii\base\Model
{
    public $user = null;
    public $notifications = [];
    public $notificationQuantity = 0;
}