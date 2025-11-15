<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class SiteConfigForm extends Model
{
    /** @var UploadedFile|null */
    public $logoFile;

    public function rules(): array
    {
        return [
            [
                ['logoFile'],
                'file',
                'extensions' => ['png', 'jpg', 'jpeg'],
                'maxSize' => 4 * 1024 * 1024,
                'skipOnEmpty' => false,
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'logoFile' => 'Logo principal (PNG o JPG)',
        ];
    }

    public function upload(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $targetDir = Yii::getAlias('@frontend/web/images');
        FileHelper::createDirectory($targetDir);

        $fileName = 'hero-logo.' . $this->logoFile->getExtension();
        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

        if (!$this->logoFile->saveAs($targetPath, false)) {
            $this->addError('logoFile', 'No se pudo guardar el archivo.');
            return false;
        }

        // Remove previous logos with other extensions
        foreach (['png', 'jpg', 'jpeg'] as $ext) {
            if ($ext === $this->logoFile->getExtension()) {
                continue;
            }
            $old = $targetDir . DIRECTORY_SEPARATOR . 'hero-logo.' . $ext;
            if (is_file($old)) {
                @unlink($old);
            }
        }

        return true;
    }

    public static function currentLogoPath(): ?string
    {
        $webRoot = Yii::getAlias('@frontend/web');
        foreach (['png', 'jpg', 'jpeg'] as $ext) {
            $path = $webRoot . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'hero-logo.' . $ext;
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }
}

