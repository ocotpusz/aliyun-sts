<?php

namespace Octopusz\AliyunSts;

use Octopusz\Sts\Core\Autoloader;
use Octopusz\Sts\Core\DefaultAcsClient;
use Octopusz\Sts\Core\Profile\DefaultProfile;
use Octopusz\Sts\Request\V20150401\AssumeRoleRequest;

/**
 * Class AliyunSts
 * @package Octopusz\AliyunSts
 */
class AliSts
{
    private $_accessKey = '';
    private $_accessSecret = '';
    private $_client = '';


    /**
     * AliyunSts constructor.
     * @param $accessKey
     * @param $accessSecret
     */
    public function __construct($accessKey, $accessSecret)
    {
        Autoloader::config();
        $this->_accessKey = $accessKey;
        $this->_accessSecret = $accessSecret;
        $iClientProfile = DefaultProfile::getProfile("cn-shanghai", $this->_accessKey, $this->_accessSecret);
        $this->_client = new DefaultAcsClient($iClientProfile);
    }

    public function assumeRole($durationSeconds, $policy, $roleArn, $roleSessionName)
    {
        header("Content-Type:text/html;charset=utf-8");
        $request = new AssumeRoleRequest();
        $request->setDurationSeconds($durationSeconds);
        $request->setPolicy($policy);
        $request->setRoleArn($roleArn);
        $request->setRoleSessionName($roleSessionName);
        $response = $this->_client->doAction($request);
        $rows = array();
        $body = $response->getBody();
        $content = json_decode($body);
        if ($response->getStatus() == 200) {
            $rows['status_code'] = 200;
            $rows['msg'] = '成功';
            $rows['data']['AccessKeyId'] = $content->Credentials->AccessKeyId;
            $rows['data']['AccessKeySecret'] = $content->Credentials->AccessKeySecret;
            $rows['data']['Expiration'] = $content->Credentials->Expiration;
            $rows['data']['SecurityToken'] = $content->Credentials->SecurityToken;
        } else {
            $rows['status_code'] = 400;
            $rows['msg'] = '失败';
            $rows['ErrorCode'] = $content->Code;
            $rows['ErrorMessage'] = $content->Message;
        }
        return $rows;
    }
}
