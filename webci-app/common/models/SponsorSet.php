<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * @property int $id
 * @property string|null $title
 * @property string|null $image_one
 * @property string|null $image_two
 * @property string|null $image_three
 * @property string|null $image_four
 * @property int $updated_at
 */
class SponsorSet extends ActiveRecord
{
    public ?UploadedFile $imageUploadOne = null;
    public ?UploadedFile $imageUploadTwo = null;
    public ?UploadedFile $imageUploadThree = null;
    public ?UploadedFile $imageUploadFour = null;

    public static function tableName(): string
    {
        return '{{%sponsor_set}}';
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
            [['title'], 'string', 'max' => 120],
            [['image_one', 'image_two', 'image_three', 'image_four'], 'string', 'max' => 255],
            [['title'], 'filter', 'filter' => fn($value) => $this->toUpper($value)],
            [
                ['imageUploadOne', 'imageUploadTwo', 'imageUploadThree', 'imageUploadFour'],
                'file',
                'extensions' => ['png', 'jpg', 'jpeg', 'webp'],
                'maxSize' => 2 * 1024 * 1024,
                'skipOnEmpty' => true,
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Título',
            'image_one' => 'Imagen 1',
            'image_two' => 'Imagen 2',
            'image_three' => 'Imagen 3',
            'image_four' => 'Imagen 4',
            'imageUploadOne' => 'Imagen 1',
            'imageUploadTwo' => 'Imagen 2',
            'imageUploadThree' => 'Imagen 3',
            'imageUploadFour' => 'Imagen 4',
            'updated_at' => 'Actualizado',
        ];
    }

    public function uploadImages(): void
    {
        $uploads = [
            'imageUploadOne' => 'image_one',
            'imageUploadTwo' => 'image_two',
            'imageUploadThree' => 'image_three',
            'imageUploadFour' => 'image_four',
        ];

        $directory = Yii::getAlias('@uploads/sponsors');
        FileHelper::createDirectory($directory);

        foreach ($uploads as $uploadProperty => $attribute) {
            /** @var UploadedFile|null $file */
            $file = $this->{$uploadProperty};
            if (!$file instanceof UploadedFile) {
                continue;
            }

            $filename = 'sponsor-' . $attribute . '-' . time() . '.' . $file->extension;
            $fullPath = $directory . DIRECTORY_SEPARATOR . $filename;

            if ($file->saveAs($fullPath, false)) {
                $this->removeImageFile($this->{$attribute});
                $relative = Yii::getAlias('@uploadsUrl') . '/sponsors/' . $filename;
                $this->updateAttributes([
                    $attribute => $relative,
                    'updated_at' => time(),
                ]);
            }
        }
    }

    public function getImages(): array
    {
        return array_filter([
            $this->image_one,
            $this->image_two,
            $this->image_three,
            $this->image_four,
        ]);
    }

    private function removeImageFile(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }
        $frontendWeb = Yii::getAlias('@frontend/web');
        $path = $frontendWeb . $relativePath;
        if (is_file($path)) {
            @unlink($path);
        }
    }

    private function toUpper($value)
    {
        if ($value === null) {
            return null;
        }
        $value = trim((string)$value);
        return $value === '' ? '' : mb_strtoupper($value);
    }
}
 
