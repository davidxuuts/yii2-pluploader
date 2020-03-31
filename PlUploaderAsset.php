<?php
/**
 * Project: fanli
 * User: davidxu
 * Date: 16/2/7
 * Time: 14:00
 */
namespace davidxu\pluploader;

use davidxu\pluploader\assets\CompatibilityIEAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

class PlUploaderAsset extends AssetBundle
{
    public $sourcePath = "@davidxu/pluploader/assets";

    public $js = [
        'plupload.full.min.js',
        'i18n/zh_CN.js',
        'plcommon.js',
    ];

    public $css = [
        'plcommon.css',
    ];

    public $depends = [
        YiiAsset::class,
        CompatibilityIEAsset::class,
    ];
}
