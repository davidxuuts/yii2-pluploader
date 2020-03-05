<?php
/**
 * Project: fanli
 * User: davidxu
 * Date: 16/2/7
 * Time: 13:58
 */
namespace davidxu\pluploader\uploader;

abstract class Uploader
{
    /**
     * @param $src
     * @param $dest
     * @return array(bool,string)
     */
    abstract public function save($src, $dest);
}
