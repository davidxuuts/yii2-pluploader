<?php

namespace davidxu\pluploader\uploader;

use Yii;
use yii\base\InvalidArgumentException;
use yii\base\InvalidParamException;

class LocalUploader extends Uploader
{
    public $uploadDir;

    function __construct($uploadDir)
    {
        $this->uploadDir = Yii::getAlias($uploadDir);
        if (!file_exists($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true)) {
                throw new InvalidArgumentException($this->uploadDir . ' can not be created');
            }
        } else {
            if (!is_dir($this->uploadDir)) {
                throw new InvalidArgumentException($this->uploadDir . ' is not a dir');
            } else {
                if (!is_writable($this->uploadDir)) {
                    throw new InvalidArgumentException($this->uploadDir . ' is not writable');
                }
            }
        }
    }

    /**
     * @param string $src Uploaded file source path
     * @param string $dest Uploaded file destination path
     * @return array(bool,string,string)
     */
    public function save($src, $dest)
    {
        if (strpos($dest, '/') > 0) {
            $dir = rtrim($this->uploadDir, '/') . '/' . ltrim(substr($dest, 0, strrpos($dest, '/')),
                    '/');
            if (file_exists($dir)) {
                if (!is_dir($dir)) {
                    return [false, '', $dir . '文件已存在,创建附件保存目录失败'];
                }
            } else {
                @mkdir($dir, 0777, true);
            }
        }
        $path = rtrim($this->uploadDir, '/') . '/' . ltrim($dest, '/');
        if (!(move_uploaded_file($src, $path) || !file_exists($path))) {
            return [false, "", '文件保存出错'];
        } else {
            return [true, $dest, '文件保存成功'];
        }
    }
}
