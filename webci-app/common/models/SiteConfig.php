<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * @property int $id
 * @property string|null $logo_path
 * @property int|null $logo_width
 * @property int|null $logo_height
 * @property int $updated_at
 */
class SiteConfig extends ActiveRecord
{
    /** @var UploadedFile|null */
    public $logoFile;

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
        $model = static::findOne(1);
        if (!$model) {
            $model = new static([
                'id' => 1,
            ]);
            $model->save(false);
        }
        return $model;
    }

    public function uploadLogo(): void
    {
        if (!$this->logoFile instanceof UploadedFile) {
            return;
        }

        $directory = Yii::getAlias('@uploads/site');
        FileHelper::createDirectory($directory);

        $filename = 'logo-' . time() . '.' . $this->logoFile->extension;
        $fullPath = $directory . DIRECTORY_SEPARATOR . $filename;

        if ($this->logoFile->saveAs($fullPath, false)) {
            $this->removePreviousLogo();
            $this->logo_path = Yii::getAlias('@uploadsUrl') . '/site/' . $filename;
        }
    }

    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        if ($this->logo_width === null && $this->logo_height !== null) {
            $this->logo_width = $this->logo_height;
        }

        if ($this->logo_height === null && $this->logo_width !== null) {
            $this->logo_height = $this->logo_width;
        }

        return true;
    }

    private function removePreviousLogo(): void
    {
        if (!$this->logo_path) {
            return;
        }
        $frontendWeb = Yii::getAlias('@frontend/web');
        $path = $frontendWeb . $this->logo_path;
        if (is_file($path)) {
            @unlink($path);
        }
    }
}

