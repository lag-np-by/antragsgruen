<?php

namespace app\controllers\admin;

use app\components\Tools;
use app\components\UrlHelper;
use app\models\db\ConsultationSettingsMotionSection;
use app\models\db\ConsultationMotionType;
use app\models\db\Motion;
use app\models\exceptions\FormError;

class MotionController extends AdminBase
{
    /**
     * @param ConsultationMotionType $motionType
     * @throws FormError
     */
    private function sectionsSave(ConsultationMotionType $motionType)
    {
        $position = 0;
        foreach ($_POST['sections'] as $sectionId => $data) {
            if (preg_match('/^new[0-9]+$/', $sectionId)) {
                $section               = new ConsultationSettingsMotionSection();
                $section->motionTypeId = $motionType->id;
                $section->type         = $data['type'];
                $section->status       = ConsultationSettingsMotionSection::STATUS_VISIBLE;
            } else {
                /** @var ConsultationSettingsMotionSection $section */
                $section = $motionType->getMotionSections()->andWhere('id = ' . IntVal($sectionId))->one();
                if (!$section) {
                    throw new FormError("Section not found: " . $sectionId);
                }
            }
            $section->setAdminAttributes($data);
            $section->position = $position;

            $section->save();

            $position++;
        }
    }

    /**
     * @param ConsultationMotionType $motionType
     * @throws FormError
     */
    private function sectionsDelete(ConsultationMotionType $motionType)
    {
        if (!isset($_POST['sectionsTodelete'])) {
            return;
        }
        foreach ($_POST['sectionsTodelete'] as $sectionId) {
            if ($sectionId > 0) {
                $sectionId = IntVal($sectionId);
                /** @var ConsultationSettingsMotionSection $section */
                $section = $motionType->getMotionSections()->andWhere('id = ' . $sectionId)->one();
                if (!$section) {
                    throw new FormError("Section not found: " . $sectionId);
                }
                $section->status = ConsultationSettingsMotionSection::STATUS_DELETED;
                $section->save();
            }
        }
    }

    /**
     * @param int $motionTypeId
     * @return string
     * @throws FormError
     */
    public function actionSections($motionTypeId)
    {
        $motionType = $this->consultation->getMotionType($motionTypeId);
        if (isset($_POST['save'])) {
            $this->sectionsSave($motionType);
            $this->sectionsDelete($motionType);

            \yii::$app->session->setFlash('success', 'Gespeichert.');
        }

        return $this->render('sections', ['motionType' => $motionType]);
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $motions = $this->consultation->motions;
        return $this->render('index', ['motions' => $motions]);
    }

    /**
     * @param int $motionId
     * @return string
     */
    public function actionUpdate($motionId)
    {
        $motionId = IntVal($motionId);

        /** @var Motion $motion */
        $motion = Motion::findOne($motionId);
        if (!$motion) {
            $this->redirect(UrlHelper::createUrl("admin/motion/index"));
        }

        $this->checkConsistency($motion);

        if (isset($_POST['screen']) && $motion->status == Motion::STATUS_SUBMITTED_UNSCREENED) {
            $found = false;
            foreach ($this->consultation->motions as $motion) {
                if ($motion->titlePrefix == $_POST['titlePrefix'] && $motion->status != Motion::STATUS_DELETED) {
                    $found = true;
                }
            }
            if ($found) {
                \yii::$app->session->setFlash('error', 'Inzwischen gibt es einen anderen Antrag mit diesem Kürzel.');
            } else {
                $motion->status      = Motion::STATUS_SUBMITTED_SCREENED;
                $motion->titlePrefix = $_POST['titlePrefix'];
                $motion->flushCaches();
                $motion->save();
                $motion->onFirstPublish();
                \yii::$app->session->setFlash('success', 'Der Antrag wurde freigeschaltet.');
            }
        }

        if (isset($_POST['save'])) {
            $motion->title          = $_POST['motion']['title'];
            $motion->statusString   = $_POST['motion']['statusString'];
            $motion->dateCreation   = Tools::dateBootstraptime2sql($_POST['motion']['dateCreation']);
            $motion->noteInternal   = $_POST['motion']['noteInternal'];
            $motion->status         = $_POST['motion']['status'];
            $motion->dateResolution = '';
            if ($_POST['motion']['dateResolution'] != '') {
                $motion->dateResolution = Tools::dateBootstraptime2sql($_POST['motion']['dateCreation']);
            }
            $foundPrefix = false;
            foreach ($this->consultation->motions as $mot) {
                if ($mot->titlePrefix != '' && $mot->id != $motion->id &&
                    $mot->titlePrefix == $_POST['motion']['titlePrefix'] && $mot->status != Motion::STATUS_DELETED
                ) {
                    $foundPrefix = true;
                }
            }
            if ($foundPrefix) {
                $msg = "Das angegebene Antragskürzel wird bereits von einem anderen Antrag verwendet.";
                \yii::$app->session->setFlash('error', $msg);
            } else {
                $motion->titlePrefix = $_POST['motion']['titlePrefix'];
            }
            $motion->save();
            $motion->flushCaches();
            \yii::$app->session->setFlash('success', 'Gespeichert.');
        }

        return $this->render('update', ['motion' => $motion]);
    }
}