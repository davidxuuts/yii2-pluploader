<?php
/**
 * Project: fanli
 * User: davidxu
 * Date: 16/2/7
 * Time: 14:00
 */
namespace davidxu\pluploader;

use yii\web\AssetBundle;

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
        'davidxu\adminlte\bundles\JqueryAsset',
        'davidxu\adminlte\bundles\IEAsset',
    ];
}
