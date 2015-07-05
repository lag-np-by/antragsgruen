<?php

/**
 * @var Motion $motion
 */

use app\components\latex\Exporter;
use app\components\latex\Layout;
use app\models\db\Motion;
use app\models\settings\AntragsgruenApp;
use yii\helpers\Html;

$texTemplate = $motion->motionType->texTemplate;

$layout            = new Layout();
$layout->assetRoot = \yii::$app->basePath . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;
//$layout->templateFile = \yii::$app->basePath . DIRECTORY_SEPARATOR .
//    'assets' . DIRECTORY_SEPARATOR . 'motion_std.tex';
$layout->template = $texTemplate->texLayout;
$layout->author   = $motion->getInitiatorsStr();
$layout->title    = $motion->title;

$content = $motion->getTexContent();
/** @var AntragsgruenApp $params */
$params = \yii::$app->params;
try {
    echo Exporter::createPDF($layout, [$content], $params);
} catch (\Exception $e) {
    echo 'Ein Fehler trat auf: ' . Html::encode($e);
}
