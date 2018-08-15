<?php

namespace Modules\Api\Http\Controllers;


use Illuminate\Http\Request;
use Modules\Api\Http\Controllers\BaseController;

/**
 * 对外公开型 API 服务接口
 * 仅供项目演示或测试使用
 * 
 * @author raoyc
 */
class HomeController extends BaseController
{

    /**
     * 身份证归属地查询服务
     * 由内建的第三方composer包 `douyasi/identity-card` 提供 
     */
    public function getIdentityCard(Request $request)
    {
        $pid = $request->input('pid');
        if ($pid) {
            $ID = app('Douyasi\IdentityCard\ID') ;
            $is_pass = $ID->validateIDCard($pid);  // 校验身份证证号是否合法
            if ($is_pass) {
                $area = $ID->getArea($pid);  // 获取身份证所在地信息 遵循GB/T 2260-2007中华人民共和国行政区划代码 标准
                $gender = $ID->getGender($pid);  // 获取性别 'f'表示女，'m'表示男，校验失败返回false
                $birthday = $ID->getBirth($pid);  // 获取出生日期，失败则返回false
                $result = compact('is_pass', 'area', 'gender', 'birthday');
                return $this->api($result);
            }
        } else {
            return $this->error('invaild identity-card number');
        }
    }


    /**
     * IP归属地查询服务
     * 由第三方在线API (http://ip.taobao.com/) 提供
     */
    public function getIP(Request $request)
    {
        $ip = $request->input('ip', $request->ip());
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            $urlTaobao = 'http://ip.taobao.com/service/getIpInfo.php?ip='.$ip;
            $json = @file_get_contents($urlTaobao);
            if (isset($json) && $json) {
                $jsonDecode = json_decode($json);
                if ($jsonDecode->code == 0) {
                    $data = [
                        'ip' => $ip,
                        'country' => $jsonDecode->data->country,
                        'country_id' => $jsonDecode->data->country_id,
                        'province' => $jsonDecode->data->region,
                        'city' => $jsonDecode->data->city,
                        'isp' => $jsonDecode->data->isp,
                    ];
                    return $this->api($data);
                }
            }
            return $this->error('check your network and try later', 50001);
        }
        return $this->error('invaild ipv4 address');
    }
}