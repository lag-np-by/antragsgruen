<?php

use app\components\HTMLTools;
use app\components\Tools;
use app\components\UrlHelper;
use app\models\db\Amendment;
use app\models\db\AmendmentSection;
use app\models\db\Consultation;
use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var Consultation $consultation
 * @var Amendment $amendment
 * @var \app\models\forms\AmendmentEditForm $form
 */

/** @var \app\controllers\Base $controller */
$controller = $this->context;
$layout     = $controller->layoutParams;

$this->title = \Yii::t('admin', 'amend_edit_title') . ': ' . $amendment->getTitle();
$layout->addBreadcrumb(\Yii::t('admin', 'bread_list'), UrlHelper::createUrl('admin/motion-list/index'));
$layout->addBreadcrumb(\Yii::t('admin', 'bread_amend'));

$layout->addCSS('css/backend.css');
$layout->loadSortable();
$layout->loadDatepicker();
$layout->loadCKEditor();
$layout->loadFuelux();
$layout->addJS('npm/clipboard.min.js');

$html = '<ul class="sidebarActions">';
$html .= '<li><a href="' . Html::encode(UrlHelper::createAmendmentUrl($amendment)) . '" class="view">';
$html .= '<span class="icon glyphicon glyphicon-file"></span>' . \Yii::t('admin', 'amend_show') . '</a></li>';

$cloneUrl = Html::encode(UrlHelper::createUrl([
    'amendment/create',
    'motionSlug' => $amendment->getMyMotion()->getMotionSlug(),
    'cloneFrom'  => $amendment->id
]));
$html     .= '<li><a href="' . $cloneUrl . '" class="clone">';
$html     .= '<span class="icon glyphicon glyphicon-duplicate"></span>' .
    \Yii::t('admin', 'list_template_amendment') . '</a></li>';

$html .= '<li>' . Html::beginForm('', 'post', ['class' => 'amendmentDeleteForm']);
$html .= '<input type="hidden" name="delete" value="1">';
$html .= '<button type="submit" class="link"><span class="icon glyphicon glyphicon-trash"></span>'
    . \Yii::t('admin', 'amend_del') . '</button>';
$html .= Html::endForm() . '</li>';

$html                .= '</ul>';
$layout->menusHtml[] = $html;


echo '<h1>' . Html::encode($amendment->getTitle()) . '</h1>';

echo $controller->showErrors();


if ($amendment->isInScreeningProcess()) {
    echo Html::beginForm('', 'post', ['class' => 'content', 'id' => 'amendmentScreenForm']);
    $newRev = $amendment->titlePrefix;
    if ($newRev == '') {
        $numbering = $amendment->getMyConsultation()->getAmendmentNumbering();
        $newRev    = $numbering->getAmendmentNumber($amendment, $amendment->getMyMotion());
    }

    echo '<input type="hidden" name="titlePrefix" value="' . Html::encode($newRev) . '">';

    echo '<div style="text-align: center;"><button type="submit" class="btn btn-primary" name="screen">';
    echo Html::encode(str_replace('%PREFIX%', $newRev, \Yii::t('admin', 'amend_screen_as_x')));
    echo '</button></div>';

    echo Html::endForm();

    echo "<br>";
}


echo Html::beginForm('', 'post', [
    'id'                       => 'amendmentUpdateForm',
    'class'                    => 'motionEditForm',
    'data-antragsgruen-widget' => 'backend/AmendmentEdit',
]);


echo '<div class="content form-horizontal fuelux">';

?>
<div class="form-group">
    <label class="col-md-3 control-label" for="amendmentStatus">
        <?= \Yii::t('admin', 'motion_status') ?>:
    </label>
    <div class="col-md-4">
        <?php
        $options  = ['id' => 'amendmentStatus'];
        $statuses = Amendment::getStatusNamesVisibleForAdmins();
        echo HTMLTools::fueluxSelectbox('amendment[status]', $statuses, $amendment->status, $options, true);
        ?>
    </div>
    <div class="col-md-5">
        <?php
        $options = ['class' => 'form-control', 'id' => 'amendmentStatusString', 'placeholder' => '...'];
        echo Html::textInput('amendment[statusString]', $amendment->statusString, $options);
        ?>
    </div>
</div>

<div class="form-group">
    <label class="col-md-3 control-label" for="amendmentTitlePrefix">
        <?= \Yii::t('amend', 'prefix') ?>:
    </label>
    <div class="col-md-4">
        <?php
        $options = [
            'class'       => 'form-control',
            'id'          => 'amendmentTitlePrefix',
            'placeholder' => \Yii::t('admin', 'amend_prefix_placeholder'),
        ];
        echo Html::textInput('amendment[titlePrefix]', $amendment->titlePrefix, $options);
        ?>
        <small><?= \Yii::t('admin', 'amend_prefix_unique') ?></small>
    </div>
</div>

    <div class="form-group">
    <label class="col-md-3 control-label" for="amendmentDateCreation">
        <?= \Yii::t('admin', 'amend_created_at') ?>:
    </label>
    <div class="col-md-4">
        <div class="input-group date" id="amendmentDateCreationHolder">
            <?php
            $locale = Tools::getCurrentDateLocale();
            $date = Tools::dateSql2bootstraptime($amendment->dateCreation);
            ?>
            <input type="text" class="form-control" name="amendment[dateCreation]" id="amendmentDateCreation"
                   value="<?= Html::encode($date) ?>" data-locale="<?= Html::encode($locale) ?>">
            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-md-3 control-label" for="amendmentDateResolution">
        <?= \Yii::t('admin', 'amend_resoluted_on') ?>:
    </label>
    <div class="col-md-4">
        <div class="input-group date" id="amendmentDateResolutionHolder">
            <?php
            $date = Tools::dateSql2bootstraptime($amendment->dateResolution);
            ?>
            <input type="text" class="form-control" name="amendment[dateResolution]" id="amendmentDateResolution"
                   value="<?= Html::encode($date) ?>" data-locale="<?= Html::encode($locale) ?>">
            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-md-3 control-label" for="globalAlternative">
        <?= \Yii::t('admin', 'amend_globalalt') ?>:
    </label>
    <div class="col-md-4">
        <?= Html::checkbox(
            'amendment[globalAlternative]',
            $amendment->globalAlternative,
            ['id' => 'globalAlternative']
        ) ?>
    </div>
</div>

<div class="form-group">
    <label class="col-md-3 control-label" for="amendmentNoteInternal">
        <?= \Yii::t('admin', 'internal_note') ?>:
    </label>
    <div class="col-md-9">
        <?php
        $options = ['class' => 'form-control', 'id' => 'amendmentNoteInternal'];
        echo Html::textarea('amendment[noteInternal]', $amendment->noteInternal, $options);
        ?>
    </div>
</div>

<?php

echo '</div>';


/** @var AmendmentSection[] $sections */
$sections = $amendment->getSortedSections(false);
foreach ($sections as $section) {
    echo $section->getSectionType()->getAmendmentFormatted();
}

if ($amendment->changeEditorial !== '') {
    ?>
    <section id="amendmentEditorialHint" class="motionTextHolder">
        <h3 class="green"><?= \Yii::t('amend', 'editorial_hint') ?></h3>
        <div class="paragraph">
            <div class="text motionTextFormattings">
                <?= $amendment->changeEditorial ?>
            </div>
        </div>
    </section>
    <?php
}

if ($amendment->changeExplanation !== '') {
    ?>
    <section id="amendmentExplanation" class="motionTextHolder">
        <h3 class="green"><?= \Yii::t('amend', 'reason') ?></h3>
        <div class="paragraph">
            <div class="text motionTextFormattings">
                <?= $amendment->changeExplanation ?>
            </div>
        </div>
    </section>
    <?php
}

$multipleParagraphs = $form->motion->motionType->amendmentMultipleParagraphs;

if (!$amendment->textFixed) {
    echo '<h2 class="green">' . \Yii::t('admin', 'amend_edit_text_title') . '</h2>
<div class="content" id="amendmentTextEditCaller">
    <button type="button" class="btn btn-default">' . \Yii::t('admin', 'amend_edit_text') . '</button>
</div>
<div class="content hidden" id="amendmentTextEditHolder"
     data-multiple-paragraphs="' . ($multipleParagraphs ? 1 : 0) . '">';

    foreach ($form->sections as $section) {
        echo $section->getSectionType()->getAmendmentFormField();
    }

    echo '<section class="editorialChange">
    <div class="form-group wysiwyg-textarea" id="sectionHolderEditorial" data-full-html="0" data-max-len="0">
        <label for="sections_editorial">' . \Yii::t('amend', 'editorial_hint') . '</label>
        <textarea name="amendmentEditorial" id="amendmentEditorial" class="raw">' .
        Html::encode($form->editorial) . '</textarea>
        <div class="texteditor motionTextFormattings boxed" id="amendmentEditorial_wysiwyg">';
    echo $form->editorial;
    echo '</div></section>';

    if (!$multipleParagraphs) {
        echo '<input type="hidden" name="modifiedSectionId" value="">';
        echo '<input type="hidden" name="modifiedParagraphNo" value="">';
    }


    echo '<div class="form-group wysiwyg-textarea" data-maxLen="0" data-fullHtml="0" id="amendmentReasonHolder">';
    echo '<label for="amendmentReason">' . Yii::t('amend', 'reason') . '</label>';

    echo '<textarea name="amendmentReason"  id="amendmentReason" class="raw">';
    echo Html::encode($form->reason) . '</textarea>';
    echo '<div class="texteditor motionTextFormattings boxed" id="amendmentReason_wysiwyg">';
    echo $form->reason;
    echo '</div>';
    echo '</div>';


    echo '</div>';
}


$initiatorClass = $form->motion->motionType->getAmendmentSupportTypeClass();
$initiatorClass->setAdminMode(true);
echo $initiatorClass->getAmendmentForm($form->motion->motionType, $form, $controller);

echo $this->render('../motion/_update_supporter', [
    'supporters'  => $amendment->getSupporters(),
    'newTemplate' => new \app\models\db\AmendmentSupporter(),
    'settings'    => $initiatorClass->getSettingsObj(),
]);


echo '<div class="saveholder">
<button type="submit" name="save" class="btn btn-primary">' . \Yii::t('base', 'save') . '</button>
</div>';

echo Html::endForm();
