<?php

namespace davidxu\pluploader\uploader;

abstract class Uploader
{
    /**
     * @param string $src Uploaded file source path
     * @param string $dest Uploaded file destination path
     * @return array(bool,string)
     */
    abstract public function save($src, $dest);
}
