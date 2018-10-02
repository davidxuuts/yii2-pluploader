<?php
/**
 * Project: fanli
 * User: liyifei
 * Date: 16/2/7
 * Time: 13:58
 */
namespace liyifei\pluploader\uploader;

abstract class Uploader
{
    /**
     * @param $src
     * @param $dest
     * @return array(bool,string)
     */
    abstract public function save($src, $dest);
}
