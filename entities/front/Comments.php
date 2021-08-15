<?php

namespace afzalroq\cms\entities\front;

use Yii;
use afzalroq\cms\entities\Entities;
use afzalroq\cms\entities\ItemComments;
use afzalroq\cms\entities\Items;
use yii\base\Model;

class Comments extends \afzalroq\cms\entities\ItemComments
{
    protected $entity;
    protected $item;

    public function __construct(Entities $entity, Items $item, ItemComments $comment = null, $config = [])
    {
        $this->entity = $entity;
        $this->item = $item;
        $this->item_id = $item->id;

        if($comment){
//            $this->load($comment->attributes, '');
            if ($comment->level < $entity->max_level) {
                $this->level = $comment->level + 1;
                $this->parent_id = $comment->id;
            } else {
                $this->level = $comment->level;
                $this->parent_id = $comment->parent_id;
            }
        }else{
            $this->item_id = $item->id;
            $this->status = $entity->use_moderation ? ItemComments::STATUS_DRAFT : ItemComments::STATUS_CHECKED;
        }
    }
    
    public function rules()
    {
        $entity = $this->entity;
        return [
            [['vote', 'parent_id','item_id', 'user_id', 'level', 'status'], 'integer'],
            [['status'],'default','value' => ItemComments::STATUS_DRAFT],
            [['level'], 'default', 'value' => 0],

            [['text'], 'string'],
            [['text'], 'required',
                'when' => function ($model) use ($entity) {
                    return $entity->use_comments == Entities::COMMENT_ON_REQUIRED;
                },
                'enableClientValidation' => false
            ],

            [['vote'], 'required',
                'when' => function ($model) use ($entity) {
                    return $entity->use_votes == Entities::COMMENT_ON_REQUIRED;
                },
                'enableClientValidation' => false
            ],

            [['username'], 'required', 'when' => function ($model) use ($entity) {
                return $entity->comment_without_login && Yii::$app->user->isGuest;
            }],

            [['user_id'], 'required',
                'when' => function ($model) use ($entity) {
                    return !$entity->comment_without_login;
                },
                'message' => \Yii::t('cms', 'To write comment you should log in first')
            ]
        ];
    }

}