<?php
/**
 * Project: fanli
 * User: liyifei
 * Date: 16/2/7
 * Time: 13:56
 */
namespace liyifei\pluploader;

use liyifei\pluploader\uploader\Uploader;
use Yii;
use yii\base\Action;
use yii\base\InvalidParamException;
use yii\web\Response;

class PlUploaderAction extends Action
{
    /**
     * @var Uploader
     */
    public $uploader;
    public $uploadUrl = '';
    public $prefix = "";
    public $fileExtLimit = 'jpg,jpeg,png,bmp,gif';
    public $fileSizeLimit = 1048576;
    public $allowAnony = false;
    public $renameFile = true;
    public $saveAs = '';

    public function run()
    {
        if ($this->uploader == null) {
            throw new InvalidParamException('Uploader Not Set');
        } else {
            if (!$this->uploader instanceof Uploader) {
                throw new InvalidParamException('Uplaoder should extends \liyifei\pluploader\Uploader');
            }
        }

        if (Yii::$app->request->getIsPost()) {
            if (Yii::$app->getUser()->getIsGuest() && !$this->allowAnony) {
                $result = array(
                    'jsonrpc' => '2.0',
                    'error' => array(
                        'code' => 101,
                        'message' => '未登陆用户',
                    ),
                );
            } else {
                $file = $_FILES['FileData'];
                if ($file['error'] == 0 && $file['size'] > 0) {
                    if ($file['size'] > $this->fileSizeLimit) {
                        $result = array(
                            'jsonrpc' => '2.0',
                            'error' => array(
                                'code' => 102,
                                'message' => '文件上传失败: [文件尺寸大于' . $this->fileSizeLimit . ']',
                            ),
                        );
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

                            list($errno, $error) = $this->uploader->save($file['tmp_name'], $date . '/' . $filename);
                            if ($errno) {
                                $result = array(
                                    'jsonrpc' => '2.0',
                                    'result' => array(
                                        'url' => rtrim($this->uploadUrl, '/') . '/' . $date . '/' . $filename,
                                        'params' => $_POST,
                                        'code' => 1,
                                    ),
                                );
                            } else {
                                $result = array(
                                    'jsonrpc' => '2.0',
                                    'error' => array(
                                        'code' => 103,
                                        'message' => '文件上传失败: [' . $error . ']',
                                    ),
                                );
                            }
                        } else {
                            $result = array(
                                'jsonrpc' => '2.0',
                                'error' => array(
                                    'code' => 104,
                                    'message' => '未被允许的上传类型[' . $ext . ']！',
                                ),
                            );
                        }
                    }
                } else {
                    $result = array(
                        'jsonrpc' => '2.0',
                        'error' => array(
                            'code' => 105,
                            'message' => '文件上传失败: ' . $file['error'],
                        ),
                    );
                }
            }
        } else {
            $result = array(
                'jsonrpc' => '2.0',
                'error' => array(
                    'code' => 106,
                    'message' => '请求错误',
                ),
            );
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $result;
    }

}
