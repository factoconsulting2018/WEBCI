<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\web\UploadedFile;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $summary
 * @property string|null $description
 * @property string|null $whatsapp
 * @property string|null $address
 * @property string $email
 * @property string|null $social_links
 * @property string|null $logo_path
 * @property bool $show_on_home
 * @property bool $available_in_search
 * @property bool $is_active
 * @property int|null $email_template_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property-read Category[] $categories
 * @property-read EmailTemplate|null $emailTemplate
 * @property-read ContactSubmission[] $contactSubmissions
 */
class Business extends ActiveRecord
{
    private array $socialLinksCache = [];
    public array $categoryIds = [];
    public string $socialLinksInput = '';
    /** @var UploadedFile|null */
    public $logoFile = null;

    public static function tableName(): string
    {
        return '{{%business}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['description', 'social_links'], 'string'],
            [['show_on_home', 'is_active', 'available_in_search'], 'boolean'],
            [['email_template_id'], 'integer'],
            [['email'], 'filter', 'filter' => 'trim'],
            [['email'], 'default', 'value' => null],
            [['email'], 'email'],
            [['name'], 'string', 'max' => 160],
            [['slug'], 'string', 'max' => 180],
            [['summary'], 'string', 'max' => 255],
            [['whatsapp'], 'string', 'max' => 32],
            [['address'], 'string', 'max' => 255],
            [['logo_path'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['slug'], 'unique'],
            [['slug'], 'default', 'value' => fn () => Inflector::slug($this->name)],
            [['social_links'], 'default', 'value' => '[]'],
            [['is_active'], 'default', 'value' => true],
            [['show_on_home'], 'default', 'value' => false],
            [['available_in_search'], 'default', 'value' => true],
            [['categoryIds'], 'each', 'rule' => ['integer']],
            [['socialLinksInput'], 'safe'],
            [
                ['logoFile'],
                'file',
                'extensions' => ['png', 'jpg', 'jpeg', 'webp', 'svg'],
                'maxSize' => 2 * 1024 * 1024,
                'skipOnEmpty' => true,
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Nombre del comercio',
            'slug' => 'Slug',
            'summary' => 'Descripción corta',
            'description' => 'Descripción',
            'whatsapp' => 'WhatsApp',
            'address' => 'Dirección física',
            'email' => 'Correo electrónico',
            'social_links' => 'Redes sociales',
            'logo_path' => 'Logotipo',
            'show_on_home' => 'Mostrar en portada',
            'available_in_search' => 'Disponible en el buscador',
            'is_active' => 'Activo',
            'email_template_id' => 'Plantilla de correo',
            'created_at' => 'Creado',
            'updated_at' => 'Actualizado',
            'categoryIds' => 'Categorías',
            'socialLinksInput' => 'Redes sociales',
            'logoFile' => 'Logotipo',
        ];
    }

    public function getCategories(): ActiveQuery
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])
            ->viaTable('{{%business_category}}', ['business_id' => 'id'])
            ->orderBy(['name' => SORT_ASC]);
    }

    public function getEmailTemplate(): ActiveQuery
    {
        return $this->hasOne(EmailTemplate::class, ['id' => 'email_template_id']);
    }

    public function getContactSubmissions(): ActiveQuery
    {
        return $this->hasMany(ContactSubmission::class, ['business_id' => 'id'])->orderBy(['created_at' => SORT_DESC]);
    }

    public function getSocialLinks(): array
    {
        if ($this->socialLinksCache === []) {
            $this->socialLinksCache = $this->social_links ? json_decode($this->social_links, true) ?: [] : [];
        }
        return $this->socialLinksCache;
    }

    public function setSocialLinks(array $links): void
    {
        $this->socialLinksCache = $links;
        $this->social_links = json_encode($links, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function afterFind(): void
    {
        parent::afterFind();
        $this->socialLinksCache = $this->social_links ? json_decode($this->social_links, true) ?: [] : [];
        $this->categoryIds = $this->getCategories()->select('id')->column();
        $this->socialLinksInput = $this->formatSocialLinksInput();
    }

    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        $this->processSocialLinksInput();
        $this->categoryIds = array_filter(array_map('intval', (array)$this->categoryIds));

        $this->name = $this->toUpper($this->name);
        $this->summary = $this->cleanText($this->summary);
        $this->description = $this->cleanText($this->description, false);
        $this->whatsapp = $this->toUpper($this->whatsapp);
        $this->address = $this->toUpper($this->address);
        $this->email = $this->toUpper($this->email);
        $this->slug = $this->toUpper($this->slug);

        return true;
    }

    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->slug = $this->slug ?: Inflector::slug($this->name);
        $this->slug = $this->toUpper($this->slug);
        $this->social_links = json_encode($this->socialLinksCache, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return true;
    }

    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);
        $this->syncCategories();

        if ($this->logoFile instanceof UploadedFile) {
            $this->storeLogoFile();
        }
    }

    public function afterDelete(): void
    {
        parent::afterDelete();
        $this->removeLogoFile();
        Yii::$app->db->createCommand()->delete('{{%business_category}}', ['business_id' => $this->id])->execute();
    }

    public function getShortDescription(): string
    {
        if (!empty($this->summary)) {
            return $this->summary;
        }
        if (!empty($this->description)) {
            return mb_strimwidth(strip_tags($this->description), 0, 120, '…');
        }
        return 'Sin descripción';
    }

    public function getPrimarySocialUrl(): ?string
    {
        $links = $this->getSocialLinks();
        if (empty($links)) {
            return null;
        }
        $first = reset($links);
        return is_array($first) ? ($first['url'] ?? null) : ($links['url'] ?? null);
    }

    public function getContactUrl(): string
    {
        return '/site/contact-business';
    }

    public function getSocialLinksString(): string
    {
        $links = $this->getSocialLinks();
        if (empty($links)) {
            return '';
        }
        $pairs = array_map(static function ($link) {
            $label = $link['label'] ?? '';
            $url = $link['url'] ?? '';
            if ($label && $url) {
                return $label . ' (' . $url . ')';
            }
            return $label ?: $url;
        }, $links);

        return implode(', ', array_filter($pairs));
    }

    public function getAvatarUrl(): string
    {
        if ($this->logo_path) {
            return $this->logo_path;
        }

        $initials = mb_substr($this->name, 0, 1);
        $size = 120;
        $image = imagecreatetruecolor($size, $size);
        imagesavealpha($image, true);
        $transparency = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefill($image, 0, 0, $transparency);

        $background = imagecolorallocate($image, 30, 64, 175);
        imagefilledellipse($image, $size / 2, $size / 2, $size, $size, $background);
        $textColor = imagecolorallocate($image, 224, 231, 255);

        $font = 5;
        $textWidth = imagefontwidth($font) * mb_strlen($initials);
        $textHeight = imagefontheight($font);
        imagestring(
            $image,
            $font,
            (int)round(($size - $textWidth) / 2),
            (int)round(($size - $textHeight) / 2),
            $initials,
            $textColor
        );

        ob_start();
        imagepng($image);
        $data = ob_get_clean() ?: '';
        imagedestroy($image);

        return 'data:image/png;base64,' . base64_encode($data);
    }

    public function getEmailImage(): string
    {
        $text = $this->email ?? '';
        $font = 5;
        $paddingX = 12;
        $paddingY = 10;

        $width = (int)max(1, imagefontwidth($font) * mb_strlen($text) + $paddingX * 2);
        $height = (int)max(1, imagefontheight($font) + $paddingY * 2);
        $image = imagecreatetruecolor($width, $height);
        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefill($image, 0, 0, $transparent);

        $background = imagecolorallocatealpha($image, 15, 23, 42, 80);
        imagefilledrectangle($image, 0, 0, $width, $height, $background);
        $border = imagecolorallocatealpha($image, 99, 102, 241, 60);
        imagerectangle($image, 0, 0, $width - 1, $height - 1, $border);

        $textColor = imagecolorallocate($image, 125, 211, 252);
        imagestring($image, $font, (int)$paddingX, (int)$paddingY, $text, $textColor);

        ob_start();
        imagepng($image);
        $data = ob_get_clean() ?: '';
        imagedestroy($image);

        return 'data:image/png;base64,' . base64_encode($data);
    }

    private function processSocialLinksInput(): void
    {
        if (trim($this->socialLinksInput) === '') {
            $this->socialLinksCache = [];
            return;
        }

        $lines = preg_split('/\r\n|\r|\n/', trim($this->socialLinksInput)) ?: [];
        $links = [];
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }
            $parts = explode('|', $line, 2);
            $label = $this->toUpper($parts[0]);
            $url = $this->toUpper($parts[1] ?? $parts[0]);

            if ($url === '') {
                continue;
            }

            $links[] = [
                'label' => $label ?: $url,
                'url' => $url,
            ];
        }

        $this->socialLinksCache = $links;
    }

    private function formatSocialLinksInput(): string
    {
        if (empty($this->socialLinksCache)) {
            return '';
        }

        $lines = array_map(static fn($item) => trim(($item['label'] ?? '') . '|' . ($item['url'] ?? '')), $this->socialLinksCache);
        return implode(PHP_EOL, $lines);
    }

    private function syncCategories(): void
    {
        $db = static::getDb();
        $db->createCommand()->delete('{{%business_category}}', ['business_id' => $this->id])->execute();

        if (empty($this->categoryIds)) {
            return;
        }

        $rows = [];
        foreach (ArrayHelper::toArray($this->categoryIds) as $categoryId) {
            $categoryId = (int)$categoryId;
            if ($categoryId > 0) {
                $rows[] = [$this->id, $categoryId];
            }
        }

        if ($rows !== []) {
            $db->createCommand()->batchInsert('{{%business_category}}', ['business_id', 'category_id'], $rows)->execute();
        }
    }

    private function storeLogoFile(): void
    {
        if (!$this->logoFile instanceof UploadedFile) {
            return;
        }

        $directory = Yii::getAlias('@uploads/business');
        FileHelper::createDirectory($directory);

        $base = $this->slug ?: Inflector::slug($this->name ?: 'logo');
        $filename = $base . '-' . time() . '.' . $this->logoFile->extension;
        $fullPath = $directory . DIRECTORY_SEPARATOR . $filename;

        if ($this->logoFile->saveAs($fullPath, false)) {
            $this->removeLogoFile();
            $this->updateAttributes([
                'logo_path' => Yii::getAlias('@uploadsUrl') . '/business/' . $filename,
                'updated_at' => time(),
            ]);
        }
    }

    private function removeLogoFile(): void
    {
        if (empty($this->logo_path)) {
            return;
        }
        $frontendWeb = Yii::getAlias('@frontend/web');
        $path = $frontendWeb . $this->logo_path;
        if (is_file($path)) {
            @unlink($path);
        }
    }

    private function toUpper($value, bool $trim = true): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = (string)$value;
        if ($trim) {
            $value = trim($value);
        }
        return $value === '' ? '' : mb_strtoupper($value);
    }

    private function cleanText($value, bool $trim = true): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = (string)$value;
        if ($trim) {
            $value = trim($value);
        }
        return $value;
    }
}
 