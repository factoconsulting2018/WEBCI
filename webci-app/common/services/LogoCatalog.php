<?php

namespace common\services;

use Yii;

final class LogoCatalog
{
    private const ITEMS = [
        'finanzas' => [
            'label' => 'Finanzas y crédito',
            'path' => 'images/logos/benefits-finanzas.svg',
        ],
        'impuestos' => [
            'label' => 'Tributario y contable',
            'path' => 'images/logos/benefits-tributario.svg',
        ],
        'microcreditos' => [
            'label' => 'Microcréditos',
            'path' => 'images/logos/benefits-microcreditos.svg',
        ],
        'crecimiento' => [
            'label' => 'Crecimiento empresarial',
            'path' => 'images/logos/benefits-crecimiento.svg',
        ],
    ];

    public static function options(): array
    {
        $options = [];
        foreach (self::ITEMS as $key => $item) {
            $options[$key] = $item['label'];
        }

        return $options;
    }

    public static function keys(): array
    {
        return array_keys(self::ITEMS);
    }

    public static function getRelativePath(?string $key): ?string
    {
        if ($key === null) {
            return null;
        }

        $path = self::ITEMS[$key]['path'] ?? null;
        if ($path === null) {
            return null;
        }

        return '/' . ltrim($path, '/');
    }

    public static function getLabel(?string $key): ?string
    {
        if ($key === null) {
            return null;
        }

        return self::ITEMS[$key]['label'] ?? null;
    }

    public static function getUrl(?string $key): ?string
    {
        $path = self::getRelativePath($key);
        if ($path === null) {
            return null;
        }

        $base = Yii::getAlias('@web', false);
        if ($base === false || $base === '' || $base === '/') {
            return $path;
        }

        return rtrim($base, '/') . $path;
    }
}

