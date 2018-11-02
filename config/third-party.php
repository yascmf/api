<?php

/**
 * 第三方服务配置
 */
return [

    /*----- 短信服务相关配置 START -----*/
    'sms' => [

        // 短信有效时间，单位： 秒
        'ttl' => 600,  // 10分钟

        'max_times' => 5,  // 一日之内同一手机号最大接受次数，避免被恶意刷

        // 是否禁用，true 表示禁用，禁用情况下使用下面 `default_code` 默认验证码
        'disable' => env('SMS_DISABLE', true),

        // 禁用短信通道默认验证码
        'default_code' => '888888',  // testing 环境下使用的默认验证码

        // 短信内容模板，变量使用 `#var#` 标示
        'templates' => [
            'common' => '您的验证码是#code#，如非本人操作，请忽略本短信。',  // 通用模板 并不存在
            'register' => '验证码#code#，您正在注册成为新用户，感谢您的支持！',  // 注册
            'login' => '验证码#code#，您正在登录，若非本人操作，请勿泄露。',  // 登录
            'reset' => '验证码#code#，您正在尝试修改登录密码，请妥善保管账户信息。',  // 重置密码
            'change' => '验证码#code#，您正在尝试变更重要信息，请妥善保管账户信息。',  // 变更资料
            // ----- 以下为非验证码短信 通知类短信 START -----
            'service_ordered' => '<#store#>在#time#时接到一笔养车服务订单，请安排工作人员尽快处理。',
            'order_notify2' => '尊敬的会员：您的尾号为#num#养车服务订单需耗费#taking#分钟，预计在#time#完成，请做好取车等时间上安排。详询4001510660。',
            'taking_notify' => '尊敬的会员：尾号为#num#的养车服务订单已完成，可凭会员手机号和取件码#code#从终端机上取回储物柜中的车钥匙，或者联系该门店工作人员处理。详询4001510660。',
            'storing_notify' => '尊敬的会员：尾号为#num#的养车服务订单，可凭会员手机号和存件码#code#将车钥匙存入储物柜中，或者联系该门店工作人员处理。请勿将钥匙交付给无关人员，也勿将贵重物品遗留在服务车辆内，以免造成经济损失。详询4001510660。',
            'order_finished' => '尊敬的会员：您的尾号为#num#养车服务订单已完成服务，请尽快前往门店<#store#>取车。详询4001510660。',
            'order_canceled' => '尊敬的会员：您的尾号为#num#养车服务订单已被取消，相应的积分会在30分钟内退回。详询4001510660。',
            'order_notify1' => '尊敬的会员：您的尾号为#num#养车服务订单已开始服务，根据服务项目不同，所花费时间不定，请耐心等待后续服务通知。详询4001510660。',
            'ordering_services' => '尊敬的会员：您在#time#时提交了尾号为#num#养车服务订单，服务项目为#products#，总共花费#cost#积分，请尽快驾驶当前服务订单车牌车辆前往<#store#>，门店地址为：#address#。详询4001510660。',
            // ----- 以下为非验证码短信 通知类短信 END -----
        ],
        // 接入阿里云短信服务 模板id
        'aliyun-template-ids' => [
            'common' => 'SMS_404',  // 通用模板 并不存在
            'register' => 'SMS_121130102',
            'login' => 'SMS_121130104',
            'reset' => 'SMS_121130101',
            'change' => 'SMS_121130100',
            // ----- 以下为非验证码短信 通知类短信 START -----
            'service_ordered' => 'SMS_138076242',
            'order_notify2' => 'SMS_138076228',
            'taking_notify' => 'SMS_138061306',
            'storing_notify' => 'SMS_138061303',
            'order_finished' => 'SMS_138071210',
            'order_canceled' => 'SMS_138076194',
            'order_notify1' => 'SMS_138076192',
            'ordering_services' => 'SMS_138061281',
            // ----- 以下为非验证码短信 通知类短信 END -----
        ],

        // easysms 短信网关配置 目前只使用 `云片`
        'easysms' => [
            'default' => [
                'gateways' => [
                    'aliyun',
                    // 'yunpian', // 配置你的网站到可用的网关列表
                ],
            ],
            'gateways' => [
                /*
                'yunpian' => [
                    'api_key' => env('YUNPAIN_API_KEY', null),
                ],
                */
                'aliyun' => [
                    'access_key_id' => env('ALIYUN_AK_ID', null),
                    'access_key_secret' => env('ALIYUN_AK_SECRET', null),
                    'sign_name' => env('ALIYUN_SIGN_NAME', null),
                ],
            ],
        ],

    ],
    /*----- 短信服务相关配置 END -----*/

    /*---- API 相关配置 START -----*/
    'api' => [
        'login_token' => [
            'prefix' => 'api_token:',
            'ttl' => 7200,  // 2 hours
            'refresh_ttl' => 600,  // before expired time 10 minutes
        ]
    ],
];
