<?php
namespace app\assets;
use yii\web\AssetBundle;

class OpenSeadragonAsset extends AssetBundle
{
    // Путь к установленным исходникам библиотеки в vendor
    public $sourcePath = '@npm/openseadragon/build/openseadragon';

    public $js = [
        'openseadragon.min.js',
    ];

    public $publishOptions = [
        'only' => [
            'openseadragon.min.js',
            'images/*', // Важно: публикуем иконки кнопок (плюс, минус, полный экран)
        ],
    ];
}