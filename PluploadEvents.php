<?php

namespace davidxu\pluploader;

use Yii;
use yii\base\BaseObject;

/**
 * PluploadEvents
 *
 * @author David Xu <david.xu.uts@163.com>
 * @since 2.0
 */
class PluploadEvents extends BaseObject {

    const JQUERY = 'jQuery';

    public $previewContainer = 'previewContainer';
    public $errorContainer = 'errorContainer';
    public $uploadOptions;
    public $multiSelection;
    public $autoUpload;
    public $responseElement;
    
    private $appendHtmlType = 'html';
    private $activeUploadResponse = '';
    
//    private $jq = 'jQuery';

    public function getScripts($events) {
        $registerEvents = [];
        foreach ($events as $methodName) {
            $methodName = ucfirst($methodName);
            $method = 'bind' . $methodName;
            if (!method_exists($this, $method)) {
                continue;
            }
            $registerEvents[$methodName] = $this->$method();
        }

        return $registerEvents;
    }

    /**
     * Init
     */
    protected function bindInit() {
        $js = /** @lang JavaScript */ <<<JS_BIND
function(uploader) {
    // let container = uploader.settings.container
    let params = uploader.getOption('multipart_params')
    let elementBrowse = $(uploader.settings.container)
    if (params.max_file_nums !== undefined && params.max_file_nums > 0) {
        let uploaded_nums = $('#{$this->previewContainer}').children().length
        if (uploaded_nums >= params.max_file_nums) {
            elementBrowse.hide();
        }
    }
}
JS_BIND;
        return $js;
    }

    /**
     * PostInit
     */
    protected function bindPostInit() {
        if (!$this->autoUpload) {
            $this->activeUploadResponse = /** @lang JavaScript */<<<ACTIVE_RESPONSE
$("#{$this->uploadOptions['id']}").on('click', function() {
    uploader.start()
    return false
})
ACTIVE_RESPONSE;
        }
        $js = /** @lang JavaScript */ <<<JS_BIND
function(uploader) {
    $(document).on('click', '.plupload_file_action', function () {
        $(this).parent().remove()
        uploader.refresh()
    })
    $('#{$this->errorContainer}').hide()
    {$this->activeUploadResponse}
}
JS_BIND;
        
        return $js;
    }

    /**
     * Browse
     */
    protected function bindBrowse($up) {
        return;
    }

    /**
     * Refresh
     */
    protected function bindRefresh() {
        $js = /** @lang JavaScript */ <<<JS_BIND
function(uploader) {
    let params = uploader.getOption('multipart_params')
    // let container = uploader.settings.container
    let elementBrowse = $(uploader.settings.container)
    if (params.max_file_nums !== undefined && params.max_file_nums > 0) {
        let uploaded_nums = $('#{$this->previewContainer}').children().length;
        if (uploaded_nums < params.max_file_nums) {
            elementBrowse.show();
        } else {
            elementBrowse.hide();
        }
    }
}
JS_BIND;

        return $js;
    }

    /**
     * StateChanged
     */
    protected function bindStateChanged($up) {
        return;
    }

    /**
     * QueueChanged
     */
    protected function bindQueueChanged($up) {
        return;
    }

    /**
     * OptionChanged
     */
    protected function bindOptionChanged($up, $name, $value, $oldValue) {
        return;
    }

    /**
     * BeforeUpload
     */
    protected function bindBeforeUpload() {
        $js = /** @lang JavaScript */ <<<JS_BIND
function (uploader, file) {
    $('#' + file.id).find('.plupload_file_mark').addClass('plupload_file_uploading').html('正在上传')
}
JS_BIND;
        
        return $js;
    }

    /**
     * UploadProgress
     */
    protected function bindUploadProgress() {
        $js = /** @lang JavaScript */ <<<JS_BIND
function (uploader, file) {
    let percent = file.percent + '%'
    let elementFile = $('#' + file.id)
    elementFile.find('.plupload_file_percent').html(percent);
    elementFile.find('.progress-bar').width(percent);
}
JS_BIND;
        
        return $js;
    }

    /**
     * FileFiltered
     */
    protected function bindFileFiltered($up, $file) {
        return;
    }

    /**
     * FilesAdded
     */
    protected function bindFilesAdded() {
        $disableBrowse = 'true';
        if ($this->multiSelection) {
            $this->appendHtmlType = 'append';
            $disableBrowse = 'false';
        }
        $script = 'uploader.disableBrowse(' . $disableBrowse . ')';
        $scriptUpload = '';
        if ($this->autoUpload) {
            $scriptUpload = 'uploader.start()';
        }
        
        $scriptString = /** @lang JavaScript */ <<<SCRIPT_STRING
{$script}
    {$scriptUpload}
SCRIPT_STRING;
        
        $js = /** @lang JavaScript */ <<<JS_BIND
function (uploader, files) {
    $('#{$this->errorContainer}').hide()
    
    let upfiles = ''
    plupload.each(files, function (file) {
        upfiles += PluploadCustom.tplUploadItem(uploader, file)
    })
    $('#{$this->previewContainer}').{$this->appendHtmlType}(upfiles)
    uploader.refresh()
    {$scriptString}
}
JS_BIND;
        
        return $js;
    }

    /**
     * FilesRemoved
     */
    protected function bindFilesRemoved() {
        $js = /** @lang JavaScript */ <<<JS_BIND
function (uploader, files) {
    $.each(files, function(index, file) {
        console.log(file)
        $('#' + file.id).remove()
    })
}
JS_BIND;

        return $js;
    }

    /**
     * FileUploaded
     */
    protected function bindFileUploaded() {
        $responseElement = $this->multiSelection
            ? 'elementFile.find(".plupload_file_input")'
            : '$(\'#' . $this->responseElement . '\')';
        
        $js = /** @lang JavaScript */ <<<JS_BIND
function (uploader, file, res) {
    if (res !== 'undefined') {
        let response = JSON.parse(res.response)
        let url = response.url
        // console.log(response, url)
        // console.log(response.url_use_db_id, response.file_id)
        let elementFile = $('#' + file.id)
        let responseElement = {$responseElement}
        if (response.url_use_db_id === true) {
            responseElement.val(response.file_id)
        } else {
            responseElement.val(url)
        }
        elementFile.find('.plupload_file_thumb img').attr('src', url)
        elementFile.removeClass('plupload_file_loading');
        elementFile.find('.plupload_file_status').remove();
    }
}
JS_BIND;

        return $js;
    }

    /**
     * ChunkUploaded
     */
    protected function bindChunkUploaded($up, $file, $info) {
        return;
    }

    /**
     * UploadComplete
     */
    protected function bindUploadComplete() {
$js = /** @lang JavaScript */ <<<JS_BIND
function (uploader, files) {
    uploader.disableBrowse(false)
}
JS_BIND;

        return $js;
    }

    /**
     * Destroy
     */
    protected function bindDestroy($up) {
        return;
    }

    /**
     * Error
     */
    protected function bindError() {
$js = /** @lang JavaScript */ <<<JS_BIND
function (uploader, error) {
    let errorElement = $(uploader.settings.error_container);
    errorElement.html('Error #: ' + error.code + ' ' + error.message).show();
}
JS_BIND;
        
        return $js;
    }
}
