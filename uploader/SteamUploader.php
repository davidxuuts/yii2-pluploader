<?php
/**
 * Created by PhpStorm.
 * User: davidxu
 * Date: 2018/2/27
 * Time: 下午3:17
 */

namespace davidxu\pluploader\uploader;

use yii;

class SteamUploader extends Uploader
{
    public $uploadDir;

    public $stream;

    function __construct($uploadDir)
    {
        $this->uploadDir = Yii::getAlias($uploadDir);
        if (!file_exists($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0777, true)) {
                throw new yii\base\InvalidArgumentException($this->uploadDir . ' can not be created');
            }
        } else {
            if (!is_dir($this->uploadDir)) {
                throw new yii\base\InvalidArgumentException($this->uploadDir . ' is not a dir');
            } else {
                if (!is_writable($this->uploadDir)) {
                    throw new yii\base\InvalidArgumentException($this->uploadDir . ' is not writable');
                }
            }
        }
    }

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
        if(!file_put_contents($path, base64_decode($this->stream))){
            return [false, "", '文件保存出错'];
        } else {
            return [true, $dest, '文件保存成功'];
        }
    }
}
