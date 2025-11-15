<?php

namespace common\\models;

use Yii;
use yii\\behaviors\\TimestampBehavior;
use yii\\db\\ActiveRecord;
use yii\\helpers\\FileHelper;
use yii\\web\\UploadedFile;

/**
 * @property int 
 * @property string|null 
 * @property int|null 
 * @property int|null 
 * @property int 
 */
class SiteConfig extends ActiveRecord
{
    /** @var UploadedFile|null */
    public ;

    public static function tableName(): string
    {
        return '{{%site_config}}';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => false,
                'updatedAtAttribute' => 'updated_at',
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['logo_width', 'logo_height'], 'integer', 'min' => 20, 'max' => 2000],
            [['logo_width', 'logo_height'], 'default', 'value' => null],
            [['logo_path'], 'string', 'max' => 255],
            [
                ['logoFile'],
                'file',
                'extensions' => ['png', 'jpg', 'jpeg'],
                'maxSize' => 2 * 1024 * 1024,
                'skipOnEmpty' => true,
            ],
        ];
    }

    public static function getCurrent(): self
    {
         = static::findOne(1);
        if (!) {
             = new static([
                'id' => 1,
            ]);
            ->save(false);
        }
        return ;
    }

    public function uploadLogo(): void
    {
        if (!->logoFile instanceof UploadedFile) {
            return;
        }

         = Yii::getAlias('@uploads/site');
        FileHelper::createDirectory();

         = 'logo-' . time() . '.' . ->logoFile->extension;
         =  . DIRECTORY_SEPARATOR . ;

        if (->logoFile->saveAs(, false)) {
            ->removePreviousLogo();
            ->logo_path = Yii::getAlias('@uploadsUrl') . '/site/' . ;
        }
    }

    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        if (->logo_width === null && ->logo_height !== null) {
            ->logo_width = ->logo_height;
        }

        if (->logo_height === null && ->logo_width !== null) {
            ->logo_height = ->logo_width;
        }

        return true;
    }

    private function removePreviousLogo(): void
    {
        if (!->logo_path) {
            return;
        }
         = Yii::getAlias('@frontend/web');
         =  . ->logo_path;
        if (is_file()) {
            @unlink();
        }
    }
}
