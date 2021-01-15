<?php

namespace afzalroq\cms\entities;

use afzalroq\cms\components\FileType;
use afzalroq\cms\components\Image;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;
use yiidreamteam\upload\ImageUploadBehavior;
use yii\web\UploadedFile;


/**
 * This is the model class for table "cms_items".
 *
 * @property int $id
 * @property int $entity_id
 * @property string $slug
 * @property string|null $text_1_0
 * @property string|null $text_1_1
 * @property string|null $text_1_2
 * @property string|null $text_1_3
 * @property string|null $text_1_4
 * @property string|null $text_2_0
 * @property string|null $text_2_1
 * @property string|null $text_2_2
 * @property string|null $text_2_3
 * @property string|null $text_2_4
 * @property string|null $text_3_0
 * @property string|null $text_3_1
 * @property string|null $text_3_2
 * @property string|null $text_3_3
 * @property string|null $text_3_4
 * @property string|null $text_4_0
 * @property string|null $text_4_1
 * @property string|null $text_4_2
 * @property string|null $text_4_3
 * @property string|null $text_4_4
 * @property string|null $text_5_0
 * @property string|null $text_5_1
 * @property string|null $text_5_2
 * @property string|null $text_5_3
 * @property string|null $text_5_4
 * @property string|null $text_6_0
 * @property string|null $text_6_1
 * @property string|null $text_6_2
 * @property string|null $text_6_3
 * @property string|null $text_6_4
 * @property string|null $text_7_0
 * @property string|null $text_7_1
 * @property string|null $text_7_2
 * @property string|null $text_7_3
 * @property string|null $text_7_4
 * @property string|null $file_1_0
 * @property string|null $file_1_1
 * @property string|null $file_1_2
 * @property string|null $file_1_3
 * @property string|null $file_1_4
 * @property string|null $file_2_0
 * @property string|null $file_2_1
 * @property string|null $file_2_2
 * @property string|null $file_2_3
 * @property string|null $file_2_4
 * @property string|null $file_3_0
 * @property string|null $file_3_1
 * @property string|null $file_3_2
 * @property string|null $file_3_3
 * @property string|null $file_3_4
 * @property string|null $seo_values
 * @property int|null $date
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 *  @property ItemPhotos[] $photos
 * @property Entities $entity
 */
class Items extends ActiveRecord
{
    const STATUS_DRAFT = 0;
    const STATUS_ACTIVE = 1;
    /**
     * @var mixed|null
     */
    public $options;
    public $files;
    public $meta_title_0;
    public $meta_title_1;
    public $meta_title_2;
    public $meta_title_3;
    public $meta_title_4;
    public $meta_des_0;
    public $meta_des_1;
    public $meta_des_2;
    public $meta_des_3;
    public $meta_des_4;
    public $meta_keyword_0;
    public $meta_keyword_1;
    public $meta_keyword_2;
    public $meta_keyword_3;
    public $meta_keyword_4;
    public $dependEntity;

    public function __construct($slug = null)
    {
        if ($slug)
            $this->dependEntity = Entities::findOne(['slug' => $slug]);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cms_items';
    }

    public function getOptionValue(CaE $cae)
    {
        return (isset($this->options[$cae->collection->slug]))
            ? $this->options[$cae->collection->slug]
            : (($cae->collection->optionDefault) ? $cae->collection->optionDefault->id : null);
    }

    public function getImageUrl($attr, $width, $height, $resizeType = null)
    {
        return Image::get($this, $attr, $width, $height, $resizeType);
    }

    public function getPhotos()
    {
        return $this->hasMany(ItemPhotos::class, ['cms_item_id' => 'id'])->orderBy('sort');
    }

    public function getMainPhoto()
    {
        return $this->hasOne(ItemPhotos::class, ['id' => 'main_photo_id']);
    }

    public function beforeSave($insert)
    {
        if ($this->entity->use_seo)
            $this->seo_values = [
                'meta_title_0' => $this->meta_title_0 ?? null,
                'meta_title_1' => $this->meta_title_1 ?? null,
                'meta_title_2' => $this->meta_title_2 ?? null,
                'meta_title_3' => $this->meta_title_3 ?? null,
                'meta_title_4' => $this->meta_title_4 ?? null,

                'meta_des_0' => $this->meta_des_0 ?? null,
                'meta_des_1' => $this->meta_des_1 ?? null,
                'meta_des_2' => $this->meta_des_2 ?? null,
                'meta_des_3' => $this->meta_des_3 ?? null,
                'meta_des_4' => $this->meta_des_4 ?? null,

                'meta_keyword_0' => $this->meta_keyword_0 ?? null,
                'meta_keyword_1' => $this->meta_keyword_1 ?? null,
                'meta_keyword_2' => $this->meta_keyword_2 ?? null,
                'meta_keyword_3' => $this->meta_keyword_3 ?? null,
                'meta_keyword_4' => $this->meta_keyword_4 ?? null

            ];
        $entity = Entities::findOne($this->entity_id);
        if ($insert)
            if ($entity->use_status != null)
                $this->status = self::STATUS_DRAFT;

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        OaI::deleteAll(['item_id' => $this->id]);

        if ($this->options)
            foreach ($this->options as $collectionSlug => $optionIds)
                if (is_array($optionIds))
                    foreach ($optionIds as $optionId) {
                        $model = new OaI();
                        $model->option_id = $optionId;
                        $model->item_id = $this->id;
                        $model->save();
                    }
                else {
                    $model = new OaI();
                    $model->option_id = $optionIds;
                    $model->item_id = $this->id;
                    $model->save();
                }

        return true;
    }

    public function afterFind()
    {
        parent::afterFind();

        foreach (OaI::findAll(['item_id' => $this->id]) as $oai)
            foreach ($this->entity->caes as $cae)
                foreach ($cae->collection->options as $option)
                    if ($option->id === $oai->option_id)
                        switch ($cae->type) {
                            case CaE::TYPE_CHECKBOX:
                                $this->options[$cae->collection->slug][] = $oai->option_id;
                                break;
                            case CaE::TYPE_SELECT:
                            case CaE::TYPE_RADIO:
                                $this->options[$cae->collection->slug] = $oai->option_id;
                                break;
                        }
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            'slug' => [
                'class' => 'Zelenin\yii\behaviors\Slug',
                'slugAttribute' => 'slug',
                'attribute' => 'text_1_0',
                // optional params
                'ensureUnique' => true,
                'replacement' => '-',
                'lowercase' => true,
                // false = changes after every change for $attribute
                'immutable' => false,
                // If intl extension is enabled, see http://userguide.icu-project.org/transforms/general.
                'transliterateOptions' => 'Russian-Latin/BGN; Any-Latin; Latin-ASCII; NFD; [:Nonspacing Mark:] Remove; NFC;'
            ],
            $this->getImageUploadBehaviorConfig('file_1_0'),
            $this->getImageUploadBehaviorConfig('file_1_1'),
            $this->getImageUploadBehaviorConfig('file_1_2'),
            $this->getImageUploadBehaviorConfig('file_1_3'),
            $this->getImageUploadBehaviorConfig('file_1_4'),
            $this->getImageUploadBehaviorConfig('file_2_0'),
            $this->getImageUploadBehaviorConfig('file_2_1'),
            $this->getImageUploadBehaviorConfig('file_2_2'),
            $this->getImageUploadBehaviorConfig('file_2_3'),
            $this->getImageUploadBehaviorConfig('file_2_4'),
            $this->getImageUploadBehaviorConfig('file_3_0'),
            $this->getImageUploadBehaviorConfig('file_3_1'),
            $this->getImageUploadBehaviorConfig('file_3_2'),
            $this->getImageUploadBehaviorConfig('file_3_3'),
            $this->getImageUploadBehaviorConfig('file_3_4'),
            [
                'class' => SaveRelationsBehavior::class,
                'relations' => ['photos'],
            ],
        ];
    }

    private function getImageUploadBehaviorConfig($attribute)
    {
        $module = Yii::$app->getModule('slider');

        return [
            'class' => ImageUploadBehavior::class,
            'attribute' => $attribute,
            'filePath' => $module->storageRoot . '/data/items/[[attribute_id]]/[[filename]].[[extension]]',
            'fileUrl' => $module->storageHost . '/data/items/[[attribute_id]]/[[filename]].[[extension]]',
        ];
    }

    public function getCurrentAttrs($entityAttr)
    {
        $attrs = [];
        foreach (Yii::$app->params['cms']['languages2'] as $key => $language)
            $attrs[] = $entityAttr . '_' . $key;
        return $attrs;
    }

    public function fileValidator($entityAttr)
    {
        return [$this->getCurrentAttrs($entityAttr),
            'file',
            'extensions' => FileType::fileExtensions($this->dependEntity[$entityAttr . '_mimeType']),
            'maxSize' => $this->dependEntity[$entityAttr . '_maxSize'] * 1024 * 1024
        ];
    }

    public function requiredValidator($entityAttr)
    {
        return [$this->getCurrentAttrs($entityAttr), 'required', 'when' => function ($model) use ($entityAttr) {
            return $model->requireValidator($model->dependEntity->{$entityAttr});
        }];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        if (empty($this->dependEntity))
            $this->dependEntity = $this->entity;
        $rules = [
            $this->fileValidator('file_1'),
            $this->fileValidator('file_2'),
            $this->fileValidator('file_3'),

            $this->requiredValidator('text_1'),
            $this->requiredValidator('text_2'),
            $this->requiredValidator('text_3'),
            $this->requiredValidator('text_4'),
            $this->requiredValidator('text_5'),
            $this->requiredValidator('text_6'),
            $this->requiredValidator('text_7'),

            ['options', 'safe'],
            [['entity_id'], 'required'],
            [['entity_id', 'date', 'status'], 'integer'],
            [['text_1_0', 'text_1_1', 'text_1_2', 'text_1_3', 'text_1_4',
                'text_2_0', 'text_2_1', 'text_2_2', 'text_2_3', 'text_2_4',
                'text_3_0', 'text_3_1', 'text_3_2', 'text_3_3', 'text_3_4',
                'text_4_0', 'text_4_1', 'text_4_2', 'text_4_3', 'text_4_4',
                'text_5_0', 'text_5_1', 'text_5_2', 'text_5_3', 'text_5_4',
                'text_6_0', 'text_6_1', 'text_6_2', 'text_6_3', 'text_6_4',
                'text_7_0', 'text_7_1', 'text_7_2', 'text_7_3', 'text_7_4',
                'meta_title_0', 'meta_des_0', 'meta_keyword_0',
                'meta_title_1', 'meta_keyword_1', 'meta_des_1',
                'meta_title_2', 'meta_des_2', 'meta_keyword_2',
                'meta_title_3', 'meta_des_3', 'meta_keyword_3',
                'meta_title_4', 'meta_des_4', 'meta_keyword_4'
            ], 'string'],
            ['slug', 'string', 'max' => 255],
            [['slug'], 'unique'],
            ['files', 'each', 'rule' => ['image']],
            [['entity_id'], 'exist', 'skipOnError' => true, 'targetClass' => Entities::class, 'targetAttribute' => ['entity_id' => 'id']],
            [['main_photo_id'], 'exist', 'skipOnError' => true, 'targetClass' => ItemPhotos::class, 'targetAttribute' => ['main_photo_id' => 'id']],
        ];

        return $rules;
    }

    public function requireValidator($type)
    {
        switch ($type) {
            case Entities::TEXT_COMMON_INPUT_STRING_REQUIRED:
            case Entities::TEXT_COMMON_INPUT_INT_REQUIRED:
            case Entities::TEXT_COMMON_INPUT_URL_REQUIRED:
            case Entities::TEXT_TRANSLATABLE_INPUT_STRING_REQUIRED:
            case Entities::TEXT_TRANSLATABLE_INPUT_INT_REQUIRED:
            case Entities::TEXT_TRANSLATABLE_INPUT_URL_REQUIRED:
                return true;
            default:
                return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $language0 = isset(Yii::$app->params['cms']['languages2'][0]) ? Yii::$app->params['cms']['languages2'][0] : '';
        $language1 = isset(Yii::$app->params['cms']['languages2'][1]) ? Yii::$app->params['cms']['languages2'][1] : '';
        $language2 = isset(Yii::$app->params['cms']['languages2'][2]) ? Yii::$app->params['cms']['languages2'][2] : '';
        $language3 = isset(Yii::$app->params['cms']['languages2'][3]) ? Yii::$app->params['cms']['languages2'][3] : '';
        $language4 = isset(Yii::$app->params['cms']['languages2'][4]) ? Yii::$app->params['cms']['languages2'][4] : '';

        return [
            'id' => Yii::t('cms', 'ID'),
            'entity_id' => Yii::t('cms', 'Entity ID'),
            'slug' => Yii::t('cms', 'Slug'),
            'text_1_0' => Yii::t('cms', 'Text 1') . '(' . $language0 . ')',
            'text_1_1' => Yii::t('cms', 'Text 1') . '(' . $language1 . ')',
            'text_1_2' => Yii::t('cms', 'Text 1') . '(' . $language2 . ')',
            'text_1_3' => Yii::t('cms', 'Text 1') . '(' . $language3 . ')',
            'text_1_4' => Yii::t('cms', 'Text 1') . '(' . $language4 . ')',
            'text_2_0' => Yii::t('cms', 'Text 2') . '(' . $language0 . ')',
            'text_2_1' => Yii::t('cms', 'Text 2') . '(' . $language1 . ')',
            'text_2_2' => Yii::t('cms', 'Text 2') . '(' . $language2 . ')',
            'text_2_3' => Yii::t('cms', 'Text 2') . '(' . $language3 . ')',
            'text_2_4' => Yii::t('cms', 'Text 2') . '(' . $language4 . ')',
            'text_3_0' => Yii::t('cms', 'Text 3') . '(' . $language0 . ')',
            'text_3_1' => Yii::t('cms', 'Text 3') . '(' . $language1 . ')',
            'text_3_2' => Yii::t('cms', 'Text 3') . '(' . $language2 . ')',
            'text_3_3' => Yii::t('cms', 'Text 3') . '(' . $language3 . ')',
            'text_3_4' => Yii::t('cms', 'Text 3') . '(' . $language4 . ')',
            'text_4_0' => Yii::t('cms', 'Text 4') . '(' . $language0 . ')',
            'text_4_1' => Yii::t('cms', 'Text 4') . '(' . $language1 . ')',
            'text_4_2' => Yii::t('cms', 'Text 4') . '(' . $language2 . ')',
            'text_4_3' => Yii::t('cms', 'Text 4') . '(' . $language3 . ')',
            'text_4_4' => Yii::t('cms', 'Text 4') . '(' . $language4 . ')',
            'text_5_0' => Yii::t('cms', 'Text 5') . '(' . $language0 . ')',
            'text_5_1' => Yii::t('cms', 'Text 5') . '(' . $language1 . ')',
            'text_5_2' => Yii::t('cms', 'Text 5') . '(' . $language2 . ')',
            'text_5_3' => Yii::t('cms', 'Text 5') . '(' . $language3 . ')',
            'text_5_4' => Yii::t('cms', 'Text 5') . '(' . $language4 . ')',
            'text_6_0' => Yii::t('cms', 'Text 6') . '(' . $language0 . ')',
            'text_6_1' => Yii::t('cms', 'Text 6') . '(' . $language1 . ')',
            'text_6_2' => Yii::t('cms', 'Text 6') . '(' . $language2 . ')',
            'text_6_3' => Yii::t('cms', 'Text 6') . '(' . $language3 . ')',
            'text_6_4' => Yii::t('cms', 'Text 6') . '(' . $language4 . ')',
            'text_7_0' => Yii::t('cms', 'Text 7') . '(' . $language0 . ')',
            'text_7_1' => Yii::t('cms', 'Text 7') . '(' . $language1 . ')',
            'text_7_2' => Yii::t('cms', 'Text 7') . '(' . $language2 . ')',
            'text_7_3' => Yii::t('cms', 'Text 7') . '(' . $language3 . ')',
            'text_7_4' => Yii::t('cms', 'Text 7') . '(' . $language4 . ')',
            'file_1_0' => Yii::t('cms', 'File 1') . '(' . $language0 . ')',
            'file_1_1' => Yii::t('cms', 'File 1') . '(' . $language1 . ')',
            'file_1_2' => Yii::t('cms', 'File 1') . '(' . $language2 . ')',
            'file_1_3' => Yii::t('cms', 'File 1') . '(' . $language3 . ')',
            'file_1_4' => Yii::t('cms', 'File 1') . '(' . $language4 . ')',
            'file_2_0' => Yii::t('cms', 'File 2') . '(' . $language0 . ')',
            'file_2_1' => Yii::t('cms', 'File 2') . '(' . $language1 . ')',
            'file_2_2' => Yii::t('cms', 'File 2') . '(' . $language2 . ')',
            'file_2_3' => Yii::t('cms', 'File 2') . '(' . $language3 . ')',
            'file_2_4' => Yii::t('cms', 'File 2') . '(' . $language4 . ')',
            'file_3_0' => Yii::t('cms', 'File 3') . '(' . $language0 . ')',
            'file_3_1' => Yii::t('cms', 'File 3') . '(' . $language1 . ')',
            'file_3_2' => Yii::t('cms', 'File 3') . '(' . $language2 . ')',
            'file_3_3' => Yii::t('cms', 'File 3') . '(' . $language3 . ')',
            'file_3_4' => Yii::t('cms', 'File 3') . '(' . $language4 . ')',
            'meta_title_0' => Yii::t('cms', 'Seo Title') . '(' . $language0 . ')',
            'meta_des_0' => Yii::t('cms', 'Seo Description') . '(' . $language0 . ')',
            'meta_keyword_0' => Yii::t('cms', 'Seo Keywords') . '(' . $language0 . ')',
            'meta_title_1' => Yii::t('cms', 'Seo Title') . '(' . $language1 . ')',
            'meta_des_1' => Yii::t('cms', 'Seo Description') . '(' . $language1 . ')',
            'meta_keyword_1' => Yii::t('cms', 'Seo Keywords') . '(' . $language1 . ')',
            'meta_title_2' => Yii::t('cms', 'Seo Title') . '(' . $language2 . ')',
            'meta_des_2' => Yii::t('cms', 'Seo Description') . '(' . $language2 . ')',
            'meta_keyword_2' => Yii::t('cms', 'Seo Keywords') . '(' . $language2 . ')',
            'meta_title_3' => Yii::t('cms', 'Seo Title') . '(' . $language3 . ')',
            'meta_des_3' => Yii::t('cms', 'Seo Description') . '(' . $language3 . ')',
            'meta_keyword_3' => Yii::t('cms', 'Seo Keywords') . '(' . $language3 . ')',
            'meta_title_4' => Yii::t('cms', 'Seo Title') . '(' . $language4 . ')',
            'meta_des_4' => Yii::t('cms', 'Seo Description') . '(' . $language4 . ')',
            'meta_keyword_4' => Yii::t('cms', 'Seo Keywords') . '(' . $language4 . ')',
            'date' => Yii::t('cms', 'Date'),
            'status' => Yii::t('cms', 'Status'),
            'created_at' => Yii::t('cms', 'Created At'),
            'updated_at' => Yii::t('cms', 'Updated At'),
            'files' => Yii::t('cms', 'Pictures')
        ];
    }


    /**
     * Gets query for [[Entity]].
     *
     * @return ActiveQuery
     */
    public function getEntity()
    {
        return $this->hasOne(Entities::class, ['id' => 'entity_id']);
    }

    public function addPhoto(UploadedFile $file): void
    {
        $photos = $this->photos;
        $photos[] = ItemPhotos::create($file,$this->id);
        $this->updatePhotos($photos);
    }
    public function setMainPhoto($photos){
        if (!empty($photos)){
            $model  = self::findOne($this->id);
            $model->main_photo_id = $this->photos[0]['id'];
            $model->save(false);
        }
    }
    public function removePhotos(): void
    {
        $this->updatePhotos([]);
    }

    private function updatePhotos(array $photos): void
    {
        foreach ($photos as $i => $photo) {
            $photo->setSort($i);
            $photo->save();
        }
        $this->photos = $photos;
        $this->setMainPhoto($photos);
    }

    public function movePhotoUp($id): void
    {
        $photos = $this->photos;
        foreach ($photos as $i => $photo) {
            if ($photo->isIdEqualTo($id)) {
                if ($prev = $photos[$i - 1] ?? null) {
                    $photos[$i - 1] = $photo;
                    $photos[$i] = $prev;
                    $this->updatePhotos($photos);
                }
                return;
            }
        }
        throw new \DomainException(\Yii::t('app', 'Photo is not found.'));
    }

    public function movePhotoDown($id): void
    {
        $photos = $this->photos;
        foreach ($photos as $i => $photo) {
            if ($photo->isIdEqualTo($id)) {
                if ($next = $photos[$i + 1] ?? null) {
                    $photos[$i] = $next;
                    $photos[$i + 1] = $photo;
                    $this->updatePhotos($photos);
                }
                return;
            }
        }
        throw new \DomainException(\Yii::t('app', 'Photo is not found.'));
    }

    public function removePhoto($id): void
    {
        $photos = $this->photos;
        foreach ($photos as $i => $photo) {
            if ($photo->isIdEqualTo($id)) {
                $photo->delete();
                unset($photos[$i]);
                $this->updatePhotos($photos);
                return;
            }
        }
        throw new \DomainException(\Yii::t('app', 'Photo is not found.'));
    }

    public function beforeValidate(): bool
    {
        if (parent::beforeValidate()) {
            $this->files = UploadedFile::getInstances($this, 'files');
            return true;
        }
        return false;
    }
}
