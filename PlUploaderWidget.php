<?php
/**
 * Project: fanli
 * User: liyifei
 * Date: 16/2/14
 * Time: 22:46
 */
namespace liyifei\pluploader;

use Yii;
use yii\helpers\Html;
use yii\web\Request;
use yii\web\View;
use yii\widgets\InputWidget;

class PlUploaderWidget extends InputWidget
{
    public $uploadto = '';
    public $fileSizeLimit = '512kb';
    public $fileNumLimit = 1;
    public $fileExtLimit = 'jpg,jpeg,png,bmp,gif';
    public $fileType = 'image';
    public $hint = '';
    public $formData;
    public $callback;

    protected $asset;

    public function init()
    {
        parent::init();
        if ($this->hasModel()) {
            $this->name = Html::getInputName($this->model, $this->attribute);
            $this->value = Html::getAttributeValue($this->model, $this->attribute);
        }
        $this->asset = PlUploaderAsset::register($this->getView());
        if (!is_array($this->value)) {
            if (empty($this->value)) {
                $this->value = [];
            } else {
                $this->value = [$this->value];
            }
        }
    }

    public function run()
    {
        $request = Yii::$app->getRequest();
        if ($request instanceof Request && $request->enableCsrfValidation) {
            $this->formData[$request->csrfParam] = $request->getCsrfToken();
        }
        if ($this->callback) {
            $this->formData['callback'] = $this->callback;
        }

        $js = $this->renderFile($this->getViewPath() . '/js.php', [
            'id'=>$this->getId(),
            'inputid' => $this->options['id'],
            'asseturl' => $this->asset->baseUrl,
            'uploadurl' => $this->uploadto,
            'formData' => $this->formData,
            'fileSizeLimit' => $this->fileSizeLimit,
            'fileNumLimit' => $this->fileNumLimit,
            'fileExtLimit' => $this->fileExtLimit,
            'filetype'=>$this->fileType,
            'name' => $this->name,
        ]);
        $this->getView()->registerJs($js, View::POS_READY);

        return $this->renderFile($this->getViewPath() . '/form-control.php', [
            'id' => $this->getId(),
            'inputid' => $this->options['id'],
            'imgs' => $this->value,
            'name' => $this->name,
            'fileNumLimit' => $this->fileNumLimit,
            'filetype'=>$this->fileType,
            'hint'=>$this->hint,
        ]);
    }
}
