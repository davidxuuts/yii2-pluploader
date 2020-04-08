<?php

use yii\helpers\Html;
?>
<?= Html::beginTag('div', $htmlOptions);

$data = $model->$attribute;
$html = '';
if (is_array($data)) {
    foreach ($data as $key => $item) {
        $html .= '<li class="plupload_file" id="ppi_' . $key . '">';
        $html .= Html::activeHiddenInput($model, $name . '[' . $key . ']', [
            'class' => 'plupload_file_input',
        ]);
        $html .= '<div class="plupload_file_thumb"><img src="' . $item . '"></div>
            <div class="plupload_file_name"><span>' . basename($item) . '</span></div>
            <div class="plupload_file_size"></div>
            <div class="plupload_file_action">
                <span class="plupload_action_icon">移除</span>
            </div>
         </li>';
    }
    if ($allow_max_nums && (count($data) > $allow_max_nums)) {
        $containerOptions['style'] = 'display:none;';
    }
} else {
    if ($multiSelection) {
        $attribute .= '[0]';
    }
    echo Html::activeHiddenInput($model, $attribute);
    if ($data) {
        $html .= '<li class="plupload_file" id="ppi_0">';
        $html .= '<div class="plupload_file_thumb"><img src="' . $data . '"></div>
            <div class="plupload_file_name"><span>' . basename($data) . '</span></div>
            <div class="plupload_file_size"></div>
            <div class="plupload_file_action">
                <span class="plupload_action_icon">移除</span>
            </div>
         </li>';
        $containerOptions['style'] = 'display:none;';
    }
}
?>

<?= Html::tag('ul', $html, $previewOptions) ?>
<?= Html::beginTag('div', $containerOptions) ?>

<?php if ($autoUpload): ?>
    <?= Html::a('<span>' . $browseLabel . '<b>上传图片</b></span>', 'javascript:;', $browseOptions) ?>
<?php else: ?>
    <?= Html::a($browseLabel, 'javascript:;', $browseOptions) ?>
    <?= Html::a($uploadLabel, 'javascript:;', $uploadOptions) ?>
<?php endif; ?>
<?= Html::endTag('div') ?>

<div id="<?= $errorContainer ?>" class="plupload-console"></div>
<?= Html::endTag('div') ?>
