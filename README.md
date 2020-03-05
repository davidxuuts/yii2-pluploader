PlUploader
==========
File uploader

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

#### In View
```php
<?=\davidxu\pluploader\PlUploaderWidget::widget([
                                                    'uploadto' => '/weixin/upload',
                                                    'fileSizeLimit' => "512k",
                                                    'fileNumLimit' => 1,
                                                    'fileExtLimit' => 'jpg,jpeg,png',
                                                    'formData' => array('a' => 'b', 'c' => 'd'),
                                                    'callback' => 'uploadsingle'
                                                  ]); ?>

<script type="text/javascript">
    function uploadsingle(file, response) {
        var url = response.result.url;
        alert(url);
    }
</script>
```

#### In Controller: 
```php
public function actions()
{
    return [
        'uploadlocal'=>[
            'class'=> PlUploaderAction::className(),
            'fileExtLimit' => 'jpg,jpeg,png',
            'fileSizeLimit' => 512 * 1024,
            'uploader'=>new LocalUploader(),
            'uploadDir' => Yii::getAlias('@storage') . '/upload',
            'uploadUrl' => 'http://fanlis.localhost.com/upload',
            'allowAnony' => true,
            'renameFile' => true
        ],
        'uploadqiniu'=>[
            'class'=> PlUploaderAction::className(),
            'fileExtLimit' => 'jpg,jpeg,png',
            'fileSizeLimit' => 512 * 1024,
            'uploader'=>new QiniuUploader(['bucket'=>'','accesskey'=>'','secretkey'=>'']),
            'uploadDir' => '/',
            'uploadUrl' => 'http://asdf.qiniudn.com/',
            'allowAnony' => true,
            'renameFile' => true
        ]
    ];
}

```
