<?php

use app\components\latex\Exporter;
use app\components\latex\Layout;
use app\models\db\Amendment;
use app\models\settings\AntragsgruenApp;
use yii\helpers\Html;

/**
 * @var Amendment $amendment
 */

$texTemplate = $amendment->motion->motionType->texTemplate;


$layout            = new Layout();
$layout->assetRoot = \yii::$app->basePath . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;
//$layout->templateFile = \yii::$app->basePath . DIRECTORY_SEPARATOR .
//    'assets' . DIRECTORY_SEPARATOR . 'motion_std.tex';
$layout->template = $texTemplate->texLayout;
$layout->author   = $amendment->getInitiatorsStr();
$layout->title    = $amendment->getTitle();

/** @var AntragsgruenApp $params */
$params = \yii::$app->params;
try {
    $content = $amendment->getTexContent();
    echo Exporter::createPDF($layout, [$content], $params);
} catch (\Exception $e) {
    echo 'Ein Fehler trat auf: ' . Html::encode($e);
    die();
}
