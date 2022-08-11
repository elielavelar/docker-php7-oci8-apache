<?php

/* @var $this yii\web\View */
/* @var $user \frontend\models\Citizen */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->PasswordResetToken]);
?>
Hello <?= $user->username ?>,

Follow the link below to reset your password:

<?= $resetLink ?>
