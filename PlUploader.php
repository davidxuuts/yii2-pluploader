<?php
namespace davidxu\pluploader;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Request;
use yii\web\View;
use yii\widgets\InputWidget;

class PlUploader extends InputWidget
{
    const SIZE_UNIT = 'mb';
    
    public $uploadUrl = '';
//    public $storeInDB = false;
    
    public $fileSizeLimit = '10mb';
    public $fileNumLimit = 1;
    public $fileExtLimit = 'jpg,jpeg,png,bmp,gif';
    public $fileType = 'image';
    
    public $htmlOptions = ['class' => 'plupload_wrapper'];
    
    public $previewContainer;
    public $previewOptions = ['class' => 'plupload_preview'];
    public $containerOptions = ['class' => 'plupload_container'];
    
    public $browseIcon = 'ionicons';
    public $browseLabel = '<span class="add-file glyphicon glyphicon-plus"></span>';
    public $browseOptions;
    
    public $uploadLabel = 'Upload Files';
    public $uploadOptions = [];
    
    public $errorContainer;
//    public $errorImageUrl = 'error.png';
    
    public $options = [];
    public $autoUpload = true;
    public $showUploadProgress = true;
    public $multiSelection = false;
    
    public $showUploadFiles = [];
    public $customOptions = [];
    
    public $events = [];
    
    public $chunkSize = '4';
    
    public $responseElement;
    public $hint = '';
    public $formData;
    public $callback;
    
    public $dynamicModel = false;
    public $dynamicIdxId = '#dynamic_index';
    
    public $containerSize = 100;
    
    public $template = 'default';
    
    protected $asset;
    
    public $id;
    
    public function init()
    {
        parent::init();
        
        if (!$this->id || $this->id === '') {
            $this->id = $this->getId();
        }
        // Make sure the upload URL is provided
        if (!$this->uploadUrl || $this->uploadUrl === '') {
            throw new Exception(Yii::t('yii', '{class} must specify "url" property value.', [
                '{class}' => get_class($this)
            ]));
        }
        
        // Set id of this widget
        if (!isset($this->htmlOptions['id'])) {
//            $this->htmlOptions['id'] = $this->getId();
            $this->htmlOptions['id'] = $this->id;
        }
//        $id = $this->htmlOptions['id'];
        $this->id = $this->htmlOptions['id'];
        
        // Set respone element of this widget.
        if ($this->hasModel()) {
            if (!preg_match(Html::$attributeRegex, $this->attribute, $matches)) {
                throw new InvalidParamException('Attribute name must contain word characters only.');
            }
//            if (! $this->dynamicModel) {
                $this->attribute = $matches[2];
//            }
//            var_dump($model);
            
            $model = $this->model;
            $attribute = $this->attribute;
            $this->name = Html::getInputName($this->model, $this->attribute);
            $value = $model->$attribute;
            if ($value && $this->multiSelection && !is_array($value)) {
                $model->$attribute = [$value];
            }
            $this->responseElement = Html::getInputId($this->model, $this->attribute);
            $this->options['input_name'] = $this->name;
        } else {
            if (!$this->responseElement) {
//                $this->responseElement = $id . "_input";
                $this->responseElement = $this->id . "_input";
            }
        }
        
        // Set select button
        if (!isset($this->browseOptions['id'])) {
//            $this->browseOptions['id'] = $id . "_browse";
            $this->browseOptions['id'] = $this->id . "_browse";
        }
        if (!isset($this->browseOptions['class'])) {
            $this->browseOptions['class'] = "plupload-btn-browse";
        }
//        if ($this->errorImageUrl !== false) {
//            $this->options['error_image_url'] = $this->errorImageUrl;
//        }
        if ($this->multiSelection) {
            Html::addCssClass($this->browseOptions, 'btn btn-success');
            Html::addCssClass($this->htmlOptions, 'plupload_many_thumb');
        } else {
            Html::addCssClass($this->htmlOptions, 'plupload_one');
            $this->setWrapperStyle();
            $this->fileNumLimit = 1;
        }
        
        // for preview
        if (!isset($this->previewOptions['id'])) {
//            $this->previewOptions['id'] = $id . "_preview";
            $this->previewOptions['id'] = $this->id . "_preview";
        }
//        $this->previewContainer = $id . "_preview";
        $this->previewContainer = $this->id . "_preview";
        // 设置选取按钮
        if (!isset($this->containerOptions['id'])) {
//            $this->containerOptions['id'] = $id . "_container";
            $this->containerOptions['id'] = $this->id . "_container";
        }
    
        if (!$this->autoUpload) {
            if (!isset($this->uploadOptions['id'])) {
//                $this->uploadOptions['id'] = $id . "_upload";
                $this->uploadOptions['id'] = $this->id . "_upload";
            }
            if (!isset($this->uploadOptions['class'])) {
                $this->uploadOptions['class'] = "plupload-btn-upload";
            }
        }
    
        if (!isset($this->errorContainer)) {
//            $this->errorContainer = $id . "_error";
            $this->errorContainer = $this->id . "_error";
        }
        
        if (!isset($this->options['multipart_params'])) {
            $this->options['multipart_params'] = [];
        }
    
        $this->options['multipart_params'][Yii::$app->request->csrfParam] = Yii::$app->request->csrfToken;
        if ($this->fileNumLimit) {
            $this->options['multipart_params']['max_file_nums'] = $this->fileNumLimit;
        }
    
        $request = Yii::$app->getRequest();
        if ($request instanceof Request && $request->enableCsrfValidation) {
            $this->formData[$request->csrfParam] = $request->getCsrfToken();
        }
        if ($this->callback) {
            $this->formData['callback'] = $this->callback;
        }
        
        $this->registerAssets();
    }
    
    public function run()
    {
        parent::run();
        echo $this->renderPlupload();
    }
    
    protected function renderPlupload()
    {
        $options = [
            'allow_max_nums' => $this->fileNumLimit,
            'containerOptions' => $this->containerOptions,
            'previewOptions' => $this->previewOptions,
            'errorContainer' => $this->errorContainer,
            'browseLabel' => $this->browseLabel,
            'browseOptions' => $this->browseOptions,
            'autoUpload' => $this->autoUpload,
            'uploadLabel' => $this->uploadLabel,
            'uploadOptions' => $this->uploadOptions,
            'htmlOptions' => $this->htmlOptions,
            'multiSelection' => $this->multiSelection,
//            'dynamicModel' => $this->dynamicModel,
//            'id' => $this->id,
        ];
        if ($this->hasModel()) {
            $options['model'] = $this->model;
            $options['attribute'] = $this->attribute;
        }
        return $this->render($this->template, $options);
    }
    
    public function registerAssets()
    {
        $bundle = $this->registerAssetBundle();
        $view = $this->getView();
    
        $defaultOptions = [
            'runtimes' => 'html5,flash,silverlight,html4',
            'container' => $this->containerOptions['id'],
            'browse_button' => $this->browseOptions['id'],
            'url' => $this->uploadUrl,
//            'max_file_size' => $this->fileSizeLimit,
//            'max_file_size' => $this->getUploadMaxSize(),
            'chunk_size' => $this->getChunkSize(),
            'error_container' => "#{$this->errorContainer}",
            'multi_selection' => $this->multiSelection,
            'flash_swf_url' => $bundle->baseUrl . "/Moxie.swf",
            'silverlight_xap_url' => $bundle->baseUrl . "/Moxie.xap",
            'multipart' => true,
            'multipart_params'=> $this->formData,
            'file_data_name' => 'FileData',
            'multi_selection' => $this->multiSelection && ($this->fileNumLimit > 1 ? true : false),
            'views' => [
                'list' => true,
                'thumbs' => true,
                'active' => 'thumbs',
            ],
            'filters' => [
                'max_file_size' => $this->fileSizeLimit,
//                'mime_types' => [
//                    [
//                        'title' => "Image files",
//                        'extensions' => "jpg,jpeg,gif,png,bmp"
//                    ],
//                ],
                'prevent_duplicates' => true,
                'max_file_count' => $this->fileNumLimit,
            ],
        ];
        $this->options['error_image_url'] = $bundle->baseUrl . '/images/error.png';
        $options = Json::encode(ArrayHelper::merge($defaultOptions, $this->options));
        
        $scripts = /** @lang JavaScript */ <<<JS
let uploader_{$this->id} = new plupload.Uploader({$options})
uploader_{$this->id}.init()
{$this->buildCallbackEvent()}
JS;
        
        $view->registerJs($scripts);
    }

    protected function buildCallBackEvent()
    {
        $events = ArrayHelper::merge(self::buildEvents(), $this->events);
        if (empty($events)) {
            return;
        }
        $script = '';
        foreach ($events as $event => $callback) {
            $script .= 'uploader_' . $this->id . ".bind('$event', $callback);\n";
        }
        return $script;
    }
    
    protected function buildEvents() {
        $registerEvents = [
            'Init',
            'PostInit',
            'FilesAdded',
//            'FilesRemoved',
            'BeforeUpload',
            'FileUploaded',
            'UploadComplete',
            'Refresh',
            'Error'
        ];
        //是否显示上传进度
        if ($this->showUploadProgress) {
            $registerEvents[] = 'UploadProgress';
        }
        //register script of plupload evnets
        $configs = [
            'errorContainer' => $this->errorContainer,
            'previewContainer' => $this->previewContainer,
            'uploadOptions' => $this->uploadOptions,
            'multiSelection' => $this->multiSelection,
            'autoUpload' => $this->autoUpload,
            'responseElement' => $this->responseElement,
        ];
        $event = new PluploadEvents($configs);
        return $event->getScripts($registerEvents);
    }
    
    public function registerAssetBundle()
    {
        return PlUploaderAsset::register($this->view);
    }
    
    protected function setWrapperStyle()
    {
        if (isset($this->htmlOptions['style'])) {
            return;
        }
        $this->htmlOptions['style'] = "width: {$this->containerSize}px; height: {$this->containerSize}px;";
    }
    
//    /**
//     * @return int the max upload size in MB
//     */
//    protected function getUploadMaxSize() {
//        $upload_max_filesize = (int) (ini_get('upload_max_filesize'));
//        $post_max_size = (int) (ini_get('post_max_size'));
//        $memory_limit = (int) (ini_get('memory_limit'));
//        return min($upload_max_filesize, $post_max_size, $memory_limit) . 'mb';
//    }
    
    /**
     * 分块大小
     */
    protected function getChunkSize() {
        $chunksize = (int) $this->chunkSize;
        if ($chunksize) {
            return $chunksize . self::SIZE_UNIT;
        }
        return $chunksize;
    }
}
