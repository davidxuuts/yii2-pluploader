<?php

namespace davidxu\pluploader;

use davidxu\pluploader\assets\CompatibilityIEAsset;
use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

class PlUploaderAsset extends AssetBundle
{
    public $sourcePath = "@davidxu/pluploader/assets";

    public $js = [
        'plupload.full.min.js',
        'plupload.custom.js',
        'i18n/zh_CN.js',
//        'plcommon.js',
    ];

    public $css = [
        'css/plcommon.css',
    ];

    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
        CompatibilityIEAsset::class,
    ];
}
