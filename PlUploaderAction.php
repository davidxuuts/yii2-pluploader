<?php

namespace davidxu\pluploader;

use common\models\common\Attachment;
use davidxu\pluploader\uploader\LocalUploader;
use davidxu\pluploader\uploader\OssUploader;
use davidxu\pluploader\uploader\QiniuUploader;
use davidxu\pluploader\uploader\SteamUploader;
use davidxu\pluploader\uploader\Uploader;
use Qiniu\Etag;
use Yii;
use yii\base\Action;
use yii\base\InvalidParamException;
use yii\helpers\Json;
use yii\web\Response;

class PlUploaderAction extends Action
{
    /**
     * @var Uploader
     */
    public $uploaderDriver = 'local';
    public $uploaderPath = '';
    public $uploadUrl = '';
    public $prefix = '';
    public $fileExtLimit = 'jpg,jpeg,png,bmp,gif';
    public $fileSizeLimit = 10485760;
    public $allowAnony = false;
    public $renameFile = true;
    public $saveAs = '';
    public $useDbIdForUrl = false;
    
    public function run()
    {
        $uploaderDriver = strtolower($this->uploaderDriver);
        
        if (!in_array($uploaderDriver, ['local', 'qiniu', 'steam', 'oss'])) {
            throw new InvalidParamException('Uploader should be local, qiniu, steam or oss');
        }

        if ($uploaderDriver === 'local') {
            $uploader = new LocalUploader($this->uploaderPath);
            if (!$this->uploaderPath || $this->uploaderPath === '') {
                $this->uploaderPath = Yii::getAlias('@app/web/uploads');
            }
        }
    
        if (strtolower($uploaderDriver) === 'qiniu') {
            //TODO
            $uploader = new QiniuUploader();
        }
        if (strtolower($uploaderDriver) === 'oss') {
            //TODO
            $uploader = new OssUploader();
        }
        if (strtolower($uploaderDriver) === 'stream') {
            //TODO
            $uploader = new SteamUploader();
        }
        if (Yii::$app->request->getIsPost()) {
            if (Yii::$app->getUser()->getIsGuest() && !$this->allowAnony) {
                $result = [
                    'error' => [
                        'code' => 101,
                        'message' => '未登陆用户',
                    ],
                ];
            } else {
                $file = $_FILES['FileData'];
                if ($file['error'] === 0 && $file['size'] > 0) {
                    if ($file['size'] > $this->fileSizeLimit) {
                        $result = [
                            'error' => [
                                'code' => 102,
                                'message' => '文件上传失败: [文件尺寸大于' . $this->fileSizeLimit . ']',
                            ],
                        ];
                    } else {
                        $ext = substr(strtolower(strrchr($file['name'], '.')), 1);
                        if (empty($this->fileExtLimit) || in_array($ext, explode(",", $this->fileExtLimit))
                        ) {
                            if ($this->prefix) {
                                $date = rtrim($this->prefix) . '/' . date('Ymd');
                            } else {
                                $date = date('Ymd');
                            }

                            if ($this->renameFile) {
                                $filename = date('His') . '_' . uniqid() . '.' . $ext;
                            } else {
                                if($this->saveAs) {
                                    $date = rtrim($this->prefix);
                                    $filename = $this->saveAs;
                                } else {
                                    $filename = $file['name'];
                                }
                            }

                            list($errno, $error) = $uploader->save($file['tmp_name'], $date . '/' . $filename);
                            if ($errno) {
                                $result = [
                                    'url' => rtrim($this->uploadUrl, '/') . '/' . $date . '/' . $filename,
                                    'url_use_db_id' => $this->useDbIdForUrl,
                                    'code' => 1,
                                ];
                                if (Yii::$app->request->post('store_in_db')) {
                                    $model = new Attachment();
                                    $model->member_id = Yii::$app->getUser()->getIsGuest() ? 0 : Yii::$app->user->id;
                                    $model->drive = $uploaderDriver;
                                    $model->mime_type = $file['type'];
                                    $model->name = $file['name'];
                                    $model->size = $file['size'];
                                    $model->url = rtrim($this->uploadUrl, '/') . '/' . $date . '/' . $filename;
                                    list($etag, $err) = Etag::sum(
                                        Yii::getAlias($this->uploaderPath . '/' . $date . '/' . $filename)
                                    );
                                    if ($err === null) {
                                        $model->hash = $etag;
//                                    } else {
//                                        $model->hash = '';
                                    }
                                    $model->save(false);
                                    $result['file_id'] = $model->id;
                                }
                            } else {
                                $result = [
                                    'error' => [
                                        'code' => 103,
                                        'message' => '文件上传失败: [' . $error . ']',
                                    ],
                                ];
                            }
                        } else {
                            $result = [
                                'error' => [
                                    'code' => 104,
                                    'message' => '未被允许的上传类型[' . $ext . ']！',
                                ],
                            ];
                        }
                    }
                } else {
                    $result = [
                        'error' => [
                            'code' => 105,
                            'message' => '文件上传失败: ' . $file['error'],
                        ],
                    ];
                }
            }
        } else {
            $result = [
                'error' => [
                    'code' => 106,
                    'message' => '请求错误',
                ],
            ];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $result;
    }

}
