<?php use yii\helpers\Json; ?>
var uploader_<?= $id ?> = new plupload.Uploader({
    runtimes: 'html5,flash,silverlight,html4',
    browse_button: '<?= $id ?>_pickfiles',
    container: document.getElementById('<?= $id ?>_container'),

    url: '<?php echo $uploadurl ?>',

    filters: {
        max_file_size: '<?php echo $fileSizeLimit ?>',
        mime_types: [
            {title: "Files", extensions: "<?php echo $fileExtLimit ?>"}
        ],
        prevent_duplicates: true,
        max_file_count: <?php echo $fileNumLimit ?>
    },
    multipart: true,
    multipart_params: $.parseJSON('<?php echo Json::encode($formData) ?>'),
    file_data_name: 'FileData',
<?php if ($fileNumLimit > 1): ?>
    multi_selection: true,
<?php else: ?>
    multi_selection: false,
<?php endif; ?>

    flash_swf_url: '<?php echo $asseturl ?>/Moxie.swf',

    silverlight_xap_url: '<?php echo $asseturl ?>/Moxie.xap',

    init: {
        PostInit: function () {
            document.getElementById('<?= $id ?>_filelist').innerHTML = '';

            document.getElementById('<?= $id ?>_uploadfiles').onclick = function () {
                uploader_<?= $id ?>.start();
                return false;
            };
        },

        FilesAdded: function (up, files) {
            plupload.each(files, function (file, index) {
                document.getElementById('<?= $id ?>_filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <a href="javascript:;" class="filelist_delete" data-id="'+file.id+'">删除</a><b></b></div>';
                if (up.files.length > up.settings.filters.max_file_count)
                    up.removeFile(up.files[0]);
            });
        },

        FilesRemoved: FilesRemoved,

        FileUploaded:function (up, file, info) {
            document.getElementById(file.id).getElementsByTagName('a')[0].remove();
            info = $.parseJSON(info.response);
            if (info != undefined) {
                if (info.error != undefined) {
                    alert("[" + info.error.code + "]" + info.error.message)
                }
                else {
                    var url = info.result.url;
                    <?php if($fileNumLimit>1): ?>
                        <?php if($filetype=='image'): ?>
                            document.getElementById('<?= $id ?>_imglist').innerHTML += '<div><a href="'+url+'" class="imglist_large" target="_blank"><img src="'+url+'"/></a><input type="hidden" name="<?= $name ?>[]" value="'+url+'"/><a href="javascript:;" class="imglist_delete">删除</a></div>';
                            if(document.getElementById('<?= $id ?>_imglist').getElementsByTagName('div').length><?= $fileNumLimit ?>){
                                document.getElementById('<?= $id ?>_imglist').getElementsByTagName('div')[0].remove();
                            }
                        <?php else: ?>
                            document.getElementById('<?= $id ?>_imglist').innerHTML += '<div><a href="'+url+'" class="imglist_large" target="_blank">'+url+'</a><input type="hidden" name="<?= $name ?>[]" value="'+url+'"/><a href="javascript:;" class="imglist_delete">删除</a></div>';
                            if(document.getElementById('<?= $id ?>_imglist').getElementsByTagName('div').length><?= $fileNumLimit ?>){
                                document.getElementById('<?= $id ?>_imglist').getElementsByTagName('div')[0].remove();
                            }
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if($filetype=='image'): ?>
                            document.getElementById('<?= $id ?>_imglist').innerHTML = '<div><a href="'+url+'" class="imglist_large" target="_blank"><img src="'+url+'"/></a><input type="hidden" name="<?= $name ?>" value="'+url+'"/><a href="javascript:;" class="imglist_delete">删除</a></div>';
                        <?php else: ?>
                            document.getElementById('<?= $id ?>_imglist').innerHTML = '<div><a href="'+url+'" class="imglist_large" target="_blank">'+url+'</a><input type="hidden" name="<?= $name ?>" value="'+url+'"/><a href="javascript:;" class="imglist_delete">删除</a></div>';
                        <?php endif; ?>
                    <?php endif; ?>
                    var callback = info.result.params.callback;
                    if (callback != undefined && callback.length > 0) {
                        eval(callback + "(file, info)");
                    }
                }
            }
        },

        UploadProgress: UploadProgress,

        Error: UploadError,
    }
});

uploader_<?= $id ?>.init();

$(document).on('click','#<?= $id ?>_filelist .filelist_delete',function(){
    var id=$(this).attr("data-id");
    uploader_<?= $id ?>.removeFile(id);
});
