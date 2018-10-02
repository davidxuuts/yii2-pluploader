<?php
/**
 * Project: fanli
 * User: liyifei
 * Date: 16/2/14
 * Time: 22:49
 */

?>
<div id="<?= $inputid ?>">
    <div id="<?= $id ?>_container">
        <a id="<?= $id ?>_pickfiles" class="btn btn-primary" href="javascript:;">选择文件</a>
        <a id="<?= $id ?>_uploadfiles" class="btn btn-primary" href="javascript:;">上传文件</a>
        <label for="">最多允许上传<?= $fileNumLimit ?>个文件。<?= $hint ?></label>
        <div id="<?= $id ?>_filelist" class="filelist">您的浏览器不支持HTML5或Flash</div>
        <div id="<?= $id ?>_imglist" class="imglist">
            <?php if ($fileNumLimit > 1) {
                $name = $name . '[]';
            } ?>
            <?php foreach ($imgs as $img): ?>
                <div>
                    <a href="<?= $img ?>" target="_blank" class="imglist_large">
                        <?php if ($filetype == "image"): ?>
                            <img src="<?= $img ?>" alt="">
                        <?php else: ?>
                            <?= $img ?>
                        <?php endif; ?>
                        <input type="hidden" name="<?= $name ?>" value="<?= $img ?>"/>
                    </a>
                    <a href="javascript:;" class="imglist_delete">删除</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
