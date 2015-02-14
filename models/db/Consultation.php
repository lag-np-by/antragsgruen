<?php

namespace app\models\db;

use app\components\MotionSorter;
use app\models\ConsultationSettings;
use app\models\exceptions\DB;
use app\models\forms\SiteCreateForm;
use app\models\initiatorViews\DefaultForm;
use app\models\policies\IPolicy;
use app\models\wording\Wording;
use yii\db\ActiveRecord;

/**
 * @package app\models\db
 *
 * @property int $id
 * @property int $siteId
 * @property int $type
 *
 * @property string $urlPath
 * @property string $title
 * @property string $titleShort
 * @property string $eventDateFrom
 * @property string $eventDateTo
 * @property string $deadlineMotions
 * @property string $deadlineAmendments
 * @property string $policyMotions
 * @property string $policyAmendments
 * @property string $policyComments
 * @property string $policySupport
 * @property string $adminEmail
 * @property string $settings
 *
 * @property Site $site
 * @property Motion[] $motions
 * @property ConsultationText[] $texts
 * @property User[] $admins
 * @property ConsultationOdtTemplate[] $odtTemplates
 * @property ConsultationSubscription[] $subscriptions
 * @property ConsultationSettingsTag[] $tags
 */
class Consultation extends ActiveRecord
{

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'consultation';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSite()
    {
        return $this->hasOne(Site::className(), ['id' => 'siteId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMotions()
    {
        return $this->hasMany(Motion::className(), ['consultationId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTexts()
    {
        return $this->hasMany(ConsultationText::className(), ['consultationId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdmins()
    {
        return $this->hasMany(User::className(), ['id' => 'userId'])
            ->viaTable('consultationAdmin', ['consultationId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOdtTemplates()
    {
        return $this->hasMany(ConsultationOdtTemplate::className(), ['id' => 'consultationId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriptions()
    {
        return $this->hasMany(ConsultationSubscription::className(), ['id' => 'consultationId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(ConsultationSettingsTag::className(), ['id' => 'consultationId']);
    }


    /** @var null|ConsultationSettings */
    private $settingsObject = null;

    /**
     * @return ConsultationSettings
     */
    public function getSettings()
    {
        if (!is_object($this->settingsObject)) {
            $this->settingsObject = new ConsultationSettings($this->settings);
        }
        return $this->settingsObject;
    }

    /**
     * @param ConsultationSettings $settings
     */
    public function setSettings($settings)
    {
        $this->settingsObject = $settings;
        $this->settings       = $settings->toJSON();
    }


    /**
     * @param SiteCreateForm $form
     * @param Site $site
     * @param User $currentUser
     * @return Consultation
     * @throws DB
     */
    public static function createFromForm(SiteCreateForm $form, Site $site, User $currentUser)
    {
        $con             = new Consultation();
        $con->siteId     = $site->id;
        $con->title      = $form->title;
        $con->type       = $form->preset;
        $con->urlPath    = $form->subdomain;
        $con->adminEmail = $currentUser->email;

        $settings                   = $con->getSettings();
        $settings->maintainanceMode = !$form->openNow;
        $con->setSettings($settings);

        if (!$con->save()) {
            throw new DB($con->getErrors());
        }
        return $con;
    }

    /**
     * @param User $person
     * @return bool
     */
    public function isAdmin($person)
    {
        foreach ($this->admins as $e) {
            if ($e->id == $person->id) {
                return true;
            }
        }
        return $this->site->isAdmin($person);
    }

    /**
     * @return bool
     */
    public function isAdminCurUser()
    {
        $user = \Yii::$app->user;
        if ($user->isGuest) {
            return false;
        }
        $myself = User::findOne(["auth" => $user->id]);
        /** @var User $myself */
        if ($myself == null) {
            return false;
        }
        return $this->isAdmin($myself);
    }

    /**
     * @return array|array[]
     */
    public function getSortedMotions()
    {
        return MotionSorter::getSortedMotions($this->motions, $this->getSettings()->amendNumberingByLine);
    }

    /**
     * @return Wording
     */
    public function getWording()
    {
        // @TODO
        return new Wording();
    }

    /**
     * @return IPolicy
     */
    public function getMotionPolicy()
    {
        return IPolicy::getInstanceByID($this->policyMotions, $this);
    }

    /**
     * @return IPolicy
     */
    public function getAmendmentPolicy()
    {
        return IPolicy::getInstanceByID($this->policyAmendments, $this);
    }

    /**
     * @return DefaultForm
     */
    public function getMotionInitiatorFormClass()
    {
        return new DefaultForm($this);
    }

    /**
     * @return DefaultForm
     */
    public function getAmendmentInitiatorFormClass()
    {
        return new DefaultForm($this);
    }
}
