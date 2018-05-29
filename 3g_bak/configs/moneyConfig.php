<?php

return array(
    'money_recharge_config' => array(
        'test'    => array(
            'create_order_url' => 'http://test3.gionee.com/pay/order/wap/create',
            'pay_order_url'    => 'http://test3.gionee.com/pay/order/wap/pay',
            'api_key'          => 'B0150BC0871A44D7A876ED5869CF2B68',
            'private_key'      => 'MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBANX9Zzgvem7VfTCNGvKkaqsKQbEp+aNzKfVkju9norLjuXxJCs7tmL26Vqj64EXwXzGpNsj9B+vkqtYRQB02Ia4Y+5NXpoBuiq7ldSjg/XrFX0rF4EJMB2ctE5ncQ0wZiisacTyNv4srGGw9K4oHbjaU4bOKtMauGbS19BoPteQfAgMBAAECgYBCEHaygO3yk9SPjbC5IouP/J2lXYklriNREFeUj9FKG0YjmcVNd0sFoCarCrD8xqYNBenVCVFNwY4Agtjha9nffljbEe4e7rWrLFn6wfqsUPd7Gf3EqcsxIsFIye8s1DYxYoFK346AUnAhQiZLErufjRzMsU+qBoQxnHt0KyqviQJBAPJ6e9N3OjM45jFkPS+M8mJYZzNWEJpb4Ps1m5ga9++DmBMKOgBenXSawQ5bUR76UqTnnNM907Z7O2skbtQ0vwUCQQDh7DqRFoCWb5UUnbrkgADOX8XIQHJNwRkHYxd3qq3HEcboi/nf3msmxuffs80/Ki92z6rDZ3iAIAjnP0GtNxfTAkEAxHeDzzKioJAwy8JJZTbRyeeRLuJEL53UbBbijFsFmzbHyF2X42bGM42UcTqCPxRaVC0qP2qwGQI+gbCScDSQtQJBALtS5+zNP6l8s5v4fLJiAEkd2ByGc0BNT7d0Tk0G1DgvYUIIbhNVlHr+idxOdWxeSiS1Y93JuXxjE37KUGaTO8kCQBvkMyRsqjX3nO/pFHf901A9a4YdNFICo94+OjyaVBmEA/8r392IApvhbZJZMbX2vMGIZcHkZ1xQu0AxHXgkylo=',
            'public_key'       => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCWuZydfER7UcmXYhqCnpZyItmz/96zodJ4vock0zQcscJSIq3ADI6g4o4Aua+G4d8fM7VRHRbP1b7/e6m12kmieerpQIt0B4REmm2uH81xtFxldW18FR8tpM4o3VvcyH8RJz/N/VLAssbh6AODjgiYNc+BAQH6h/mJ0Cn2j/wLPwIDAQAB',
        ),
        'product' => array(
            'create_order_url' => 'http://pay.gionee.com/order/wap/create',
            'pay_order_url'    => 'http://pay.gionee.com/order/wap/easy/pay',
            'api_key'          => '123E578C1C0F46C398969EF6AEACE148',
            'private_key'      => 'MIICdQIBADANBgkqhkiG9w0BAQEFAASCAl8wggJbAgEAAoGBAJkEyoUObFHAWQlbuwmvBhUmaHu6ezJWQj/YB7122t/a9nkCfX7O4cFHFmkaj/lzg/XvEU0e+mZQ9A+msfsU95rxdvX2+sBWX9rwV2geu6XggkeF7fmHbSMSR2/EPTliFAg4byxQ1bJ9C4yeTD+KFO38Vk0Va2qZR4xeHvxTv9czAgMBAAECgYAryHMqHzZfLepMAzNBUhMrehHrVTBq9sN+ARI15Aw8gSqE9XFzFz8BTpXa/P61IZhghkctCfAb1o3+7HOApD80Y8r24bEuX021ttqS/1B1Gs02kjCEOVo4GCvEofkYxljhCYvUsUj92xh6VNa9NZr28azXqDFL+kEi0eCVRvC+AQJBAOaqV8Zzf+cui1PkWH8frHILpirYNj1Ys3u2Az3ng902yeIpF8EDL0V1FOsZXzlLxIFhPCuKzt+D7etAtO41zPcCQQCp0z5YLCpdxY506OD2f0y30wHo98AYvGOKuFkdhPYuj9GGluxo+S+4l+wo6vwbKKm+nnC8Ulp5zbTn5ntHwCSlAkBX1QdFVteeNRfyouGbznjAmHT2nYvRwkPd2InVeaG5i1JGXvVflpHadeG4P9oFnDdAOMFLpzhs3fSrfuEmT7AzAkAayJYskcqcv2uYRKI2Ph17YGwMsgY54HxShICJh07MSSJid6sRYzuDgXJdgbIBFoiu544gJVzfmPHPISqQRl2BAkAaJ04kt+5hf4XVR/A79dE80TREIBq+0Bhwr/9gP1OGNtRPzsXPpX3J6nQ3T+gCLZYC6TkdbUVx/+5PT123WDFw',
            'public_key'       => '',
        	'order_query_url'		=>'http://pay.gionee.com/order/query'
        ),
    ),
		
		'orderStatus' => array(
				'-1'		=>'订单已取消',
				'0'		=>'待处理',
				'1'		=>'已完成',
				'2'		=>'用户已付款,处理中'
		),
		 
		'payStatus' => array(
				'-1'		=>'支付失败',
				'0'		=>'待支付',
				'1'		=>'支付成功',
				'2'		=>'支付中'
		),
		 
		'orderType' =>array(
				'1'		=>'普通充值',
				'2'		=>'畅聊VIP'
		),
		 
		'payChannel' => array(
				'101'			=>'中国银联',
				'102'			=>'支付宝',
				'103'			=>'财付通'
		),
		
		'apiTypes'		=>array(
				'1'		=>'提交订单接口',
				'2'		=>'前台callback接口',
				'3'		=>'服务器notify接口',
				'4'		=>'订单查询接口'
				),
);