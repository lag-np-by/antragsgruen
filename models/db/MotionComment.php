<?php

namespace app\models\db;

use yii\db\Query;
use yii\helpers\Url;

/**
 * @package app\models\db
 *
 * @property int $id
 * @property int $userId
 * @property int $motionId
 * @property int $sectionId
 * @property int $paragraph
 * @property string $text
 * @property string $name
 * @property string $contactEmail
 * @property string $dateCreation
 * @property int $status
 * @property int $replyNotification
 *
 * @property User $user
 * @property Motion $motion
 * @property MotionCommentSupporter[] $supporters
 * @property MotionSection $section
 */
class MotionComment extends IComment
{
    const STATUS_VISIBLE = 0;
    const STATUS_DELETED = -1;
    const STATUS_SCREENING = 1;

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'motionComment';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMotion()
    {
        return $this->hasOne(Motion::className(), ['id' => 'motionId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSupporters()
    {
        return $this->hasMany(MotionCommentSupporter::className(), ['motionCommentId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSection()
    {
        return $this->hasOne(MotionSection::className(), ['motionId' => 'motionId', 'sectionId' => 'sectionId' ]);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['motionId', 'sectionId', 'paragraph', 'status', 'dateCreation'], 'required'],
            ['name', 'required', 'message' => 'Bitte gib deinen Namen an.'],
            ['text', 'required', 'message' => 'Bitte gib etwas Text ein.'],
            [['id', 'motionId', 'sectionId', 'paragraph', 'status'], 'number'],
            [['text', 'paragraph'], 'safe'],
        ];
    }

    /**
     * @param Consultation $consultation
     * @param int $limit
     * @return Amendment[]
     */
    public static function getNewestByConsultation(Consultation $consultation, $limit = 5)
    {
        $invisibleStati = array_map('IntVal', $consultation->getInvisibleMotionStati());

        $query = (new Query())->select('motionComment.*')->from('motionComment');
        $query->innerJoin('motion', 'motion.id = motionComment.motionId');
        $query->where('motionComment.status = ' . IntVal(static::STATUS_VISIBLE));
        $query->andWhere('motion.status NOT IN (' . implode(', ', $invisibleStati) . ')');
        $query->andWhere('motion.consultationId = ' . IntVal($consultation->id));
        $query->orderBy("dateCreation DESC");
        $query->offset(0)->limit($limit);

        return $query->all();
    }

    /**
     * @return Consultation
     */
    public function getConsultation()
    {
        return $this->motion->consultation;
    }

    /**
     * @return string
     */
    public function getMotionTitle()
    {
        return $this->motion->getTitleWithPrefix();
    }

    /**
     * @param bool $absolute
     * @return string
     */
    public function getLink($absolute = false)
    {
        $url = Url::toRoute(
            [
                'motion/view',
                'subdomain'        => $this->motion->consultation->site->subdomain,
                'consultationPath' => $this->motion->consultation->urlPath,
                'motionId'         => $this->motion->id,
                'commentId'        => $this->id,
                '#'                => 'comment' . $this->id
            ]
        );
        if ($absolute) {
            // @TODO Testen
            $url = \Yii::$app->basePath . $url;
        }
        return $url;
    }
}