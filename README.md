PlUploader for Yii2
==========
File uploader for Yii2

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist davidxu/yii2-pluploader "*"
```

or add

```
"davidxu/yii2-pluploader": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

#### for Local upload

##### In View
```php
<?php
use davidxu\pluploader\PlUploader;

// without model
?>
<?= PlUploader::widget([
    'model' => $model,
    'attribute' => 'image_src',
    'uploadUrl' => '/upload/local',
    'fileSizeLimit' => "512k",
    'fileNumLimit' => 1,
    'fileExtLimit' => 'jpg,jpeg,png',
    'formData' => [
        'store_in_db' => true,
    ],
]); ?>
<?php
// with ActiveForm
echo $form->field($model, 'image_src')
    ->widget(Pluploader::class, [
        'uploadUrl' => '/upload/local',
        'fileSizeLimit' => "512k",
        'fileNumLimit' => 1,
        'fileExtLimit' => 'jpg,jpeg,png',
        'formData' => [
            'store_in_db' => true,
        ],  
    ])
?>

```

##### In Upload Controller: 
```php
use \davidxu\pluploader\PlUploaderAction;

public function actions()
{
    return [
        'local'=>[
            'class'=> PlUploaderAction::class,
            'uploaderDriver' => 'local', // local, qiniu, oss, steam
            'fileExtLimit' => 'jpg,jpeg,png',
            'fileSizeLimit' => 512 * 1024,
            'uploaderPath' => Yii::getAlias('@app/web/uploads'),
            'uploadUrl' => 'http://host.local/uploads',
            'allowAnony' => true,
            'renameFile' => true,
            'useDbIdForUrl' => true, // return file id in DB to image url instead of file url
        ],
    ];
}
```

#### for Qiniu upload

TODO

DO NOT USE THIS PART CURRENTLY

##### In View
```php
<?php
use davidxu\pluploader\PlUploader;

// without model
?>
<?= PlUploader::widget([
    'model' => $model,
    'attribute' => 'image_src',
    'tokenUrl' => '/upload/qiniu',
    'callbackUrl' => '/callback/qiniu',
    'fileSizeLimit' => "512k",
    'fileNumLimit' => 1,
    'fileExtLimit' => 'jpg,jpeg,png',
    'formData' => [
        'store_in_db' => true,
    ],
]); ?>
<?php
// with ActiveForm
echo $form->field($model, 'image_src')
    ->widget(Pluploader::class, [
        'tokenUrl' => '/upload/qiniu',
        'callbackUrl' => '/callback/qiniu',
        'fileSizeLimit' => "512k",
        'fileNumLimit' => 1,
        'fileExtLimit' => 'jpg,jpeg,png',
        'formData' => [
            'store_in_db' => true,
        ],  
    ])
?>

```

##### In Upload Controller: 
```php
<?php
use davidxu\pluploader\PlUploaderAction;

public function actions()
{
    return [
        // TODO, not completed yet
        'qiniu'=>[
            'class'=> PlUploaderAction::class,
            'uploaderDriver' => 'qiniu',
            'uploadDir' => '/',
            'uploadUrl' => 'http://n1.qiniudn.com/',
            'allowAnony' => true,
            'renameFile' => true,
            'callbackUrl' => '/callback/qiniu',
        ]
    ];
}
```