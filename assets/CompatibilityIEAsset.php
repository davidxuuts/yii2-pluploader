<?php

namespace davidxu\pluploader\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * Class CompatibilityIEAsset
 * @package davidxu\pluploader\assets
 * @author David Xu <david.xu.uts@163.com>
 */
class CompatibilityIEAsset extends AssetBundle
{
    public $sourcePath = "@davidxu/pluploader/assets";
    
    public $js = [
        'js/html5shiv.min.js',
        'js/respond.min.js',
    ];

    public $jsOptions = [
        'condition' => 'lt IE 9',
        'position' => View::POS_HEAD
    ];

    // public function init()
    // {
    //     $this->sourcePath = __DIR__ . '/resources';
    //     parent::init();
    // }
}
