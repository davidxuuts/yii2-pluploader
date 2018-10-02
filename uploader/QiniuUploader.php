<?php
/**
 * Project: fanli
 * User: liyifei
 * Date: 16/2/7
 * Time: 13:58
 */
namespace liyifei\pluploader\uploader;

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use yii\base\InvalidArgumentException;
use yii\base\InvalidParamException;

class QiniuUploader extends Uploader
{

    public $bucket;
    public $accesskey;
    public $secretkey;

    protected $auth;
    protected $uploadmgr;
    protected $policy = array(
        'returnBody' => '{"name": $(fname),"size": $(fsize),"type": $(mimeType),"w": $(imageInfo.width),"h": $(imageInfo.height),"hash": $(etag)}',
    );

    function __construct($bucket, $accesskey, $secretkey)
    {
        $this->bucket = $bucket;
        $this->accesskey = $accesskey;
        $this->secretkey = $secretkey;

        if (empty($this->bucket) || empty($this->accesskey) || empty($this->secretkey)) {
            throw new InvalidArgumentException('Invalid configuration');
        }
        $this->auth = new Auth($this->accesskey, $this->secretkey);
        $this->uploadmgr = new UploadManager();
    }

    /**
     * @param $src
     * @param $dest
     * @return array(bool,string)
     */
    function save($src, $dest)
    {
        $token = $this->auth->uploadToken($this->bucket, null, 3600, $this->policy);
        $res = $this->uploadmgr->putFile($token, $dest, $src);
        if (!empty($res) && $res['0'] != null && $res[1] == null) {
            return [true, '文件保存成功'];
        } else {
            if (isset($res[1])) {
                return [false, $res[1]->code() . ':' . $res[1]->message()];
            } else {
                return [false, 'null response'];
            }
        }
    }
}
