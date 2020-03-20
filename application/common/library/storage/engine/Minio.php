<?php

namespace app\common\library\storage\engine;

use Aws\S3\S3Client;

/**
 * 本地文件驱动
 * Class Local
 * @package app\common\library\storage\drivers
 */
class Minio extends Server
{
    private $config;
    const ACL_PUBLIC_READ = 'public-read';
    private $response;
    private $s3UploadClient;
    private $bucketName;

    /**
     * 构造方法
     * Qiniu constructor.
     * @param $config
     * @throws \think\Exception
     */
    public function __construct($config)
    {
        parent::__construct();
        $this->config         = $config;
        $this->s3UploadClient = new S3Client([
            'version'                 => 'latest',
            'region'                  => 'us-east-1',
            'endpoint'                => $this->config['domain'],//env('S3_ENDPOINT', 'http://192.168.1.71:9000'),
            'use_path_style_endpoint' => true,//env('S3_USE_PATH_STYLE_ENDPOINT', true),
            'credentials'             => [
                'key'    => $this->config['access_key'],//env('S3_CREDENTIALS_KEY', 'minioerp'),
                'secret' => $this->config['secret_key'],//env('S3_CREDENTIALS_SECRET', 'sdqMmoRCDTBXgplZ'),
            ],
        ]);
        $this->bucketName     = $this->config['bucket'];//env('S3_BUCKET_NAME', $this->bucketName);
        $this->response       = new \stdClass();
    }

    /**
     * 执行上传
     * @return bool|mixed
     * @throws \Exception
     */
//    public function upload()
//    {
//        // 要上传图片的本地路径
//        $realPath = $this->file->getRealPath();
//
//        // 构建鉴权对象
//        $auth = new Auth($this->config['access_key'], $this->config['secret_key']);
//
//        // 要上传的空间
//        $token = $auth->uploadToken($this->config['bucket']);
//
//        // 初始化 UploadManager 对象并进行文件的上传
//        $uploadMgr = new UploadManager();
//
//        // 调用 UploadManager 的 putFile 方法进行文件的上传
//        list($result, $error) = $uploadMgr->putFile($token, $this->fileName, $realPath);
//
//        if ($error !== null) {
//            $this->error = $error->message();
//            return false;
//        }
//        return true;
//    }

    public function upload()
    {

        try {
            //$module, $sourceFilePath, $originName, $acl = self::ACL_PUBLIC_READ
            // 要上传图片的本地路径
            $realPath = $this->file->getRealPath();
            $this->fileName;
            $originName = $this->fileInfo['name'];
            $uploadFilePath             = date('Y-m') . '/' .  $originName ;
            $result                     = $this->s3UploadClient->putObject([
                'Bucket'      => $this->bucketName,
                'Key'         => $uploadFilePath,
                'SourceFile'  => $realPath,
                'ACL'         => self::ACL_PUBLIC_READ,
                'ContentType' => $this->fileInfo['type']
            ]);
            $this->fileName = $this->bucketName . '/' . $uploadFilePath;
            return true;
        } catch (\Exception $e) {
        }
        return false;
    }


    public function getEndpoint()
    {
        return rtrim($this->s3UploadClient->getEndpoint(), '/') . '/';
    }

    public function objectExist($path)
    {
        return $this->s3UploadClient->doesObjectExist($this->bucketName, trim($path, $this->bucketName));
    }

    public function getObjectUrl($path)
    {
        return $this->s3UploadClient->getObjectUrl($this->bucketName, trim($path, $this->bucketName));
    }

    /**
     * 返回文件路径
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }

}
