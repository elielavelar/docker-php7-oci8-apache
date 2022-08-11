<?php
use yii\helpers\Html;

/* @var $this yii\web\View */

?>
<style>
    body {margin: 0; padding: 0; min-width: 100%!important;}
    .content {width: 100%; max-width: 600px;}  
        
    table {
        background-color: #ffffff;
        font-family: 'Roboto','Helvetica','Arial';
        padding: 0;
        border-spacing: 0;
        border-radius: 4px;
    }
    
    tbody td {
        border: 1px solid #aaa;
    }
    
    ul li {
        line-height: 14pt;
    }
</style>
<table style="width: 100%; padding: 0; border: none" align="center" cellspacing="0">
    <tbody>
        <tr>
            <td>
                <table class="content" style="padding: 0; border: none" align="center" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="background-color: #046296; color: #fff; font-weight: bold; border: none; vertical-align: middle; padding: 2px;">
                                <span>
                                    <img src="<?= Yii::$app->params["mail"]["image"]?>" alt="Citas">
                                </span>
                                <h2><?=$data['title']?></h2>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?=$data['body']?>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>
                                <?=$data['footer']?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
    </tbody>
</table>
