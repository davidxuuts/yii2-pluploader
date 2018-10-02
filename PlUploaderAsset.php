<?php
/**
 * Project: fanli
 * User: liyifei
 * Date: 16/2/7
 * Time: 14:00
 */
namespace liyifei\pluploader;

use yii\web\AssetBundle;

class PlUploaderAsset extends AssetBundle
{
    public $sourcePath = "@liyifei/pluploader/assets";

    public $js = [
        'plupload.full.min.js',
        'i18n/zh_CN.js',
        'plcommon.js',
    ];

    public $css = [
        'plcommon.css',
    ];

    public $depends = [
        'liyifei\adminlte\bundles\JqueryAsset',
        'liyifei\adminlte\bundles\IEAsset',
    ];
}
