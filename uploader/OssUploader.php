<?php

namespace davidxu\pluploader\uploader;

use OSS\Core\OssException;
use OSS\OssClient;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;

class OssUploader extends Uploader
{
    public $accesskeyid;
    public $accesskeysecret;
    public $endpoint;
    public $bucket;

    protected $client;

    function __construct($accekeyid, $accesskeysecret, $endpoint, $bucket)
    {
        $this->accesskeyid = $accekeyid;
        $this->accesskeysecret = $accesskeysecret;
        $this->endpoint = $endpoint;
        $this->bucket = $bucket;

        if (empty($this->accesskeyid) || empty($this->accesskeysecret) || empty($this->endpoint) || empty($this->bucket)) {
            throw new InvalidArgumentException('Invalid configuration');
        }
        try {
            $this->client = new OssClient($this->accesskeyid, $this->accesskeysecret, $this->endpoint, false);
        } catch (OssException $e) {
            throw new InvalidConfigException("creating OssClient instance: FAILED: " . $e->getMessage());
        }
        if (!$this->client->doesBucketExist($this->bucket)) {
            throw new InvalidConfigException("bucket not exist");
        }
    }

    /**
     * @param $src
     * @param $dest
     * @return array(bool,string)
     */
    public function save($src, $dest)
    {
        try {
            $this->client->uploadFile($this->bucket, $dest, $src);

            return [true, '文件保存成功'];
        } catch (OssException $e) {
            return [false, $e->getMessage()];
        }
    }
}
