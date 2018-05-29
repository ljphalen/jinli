<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Config_Vip{
    const VIP_1 = 1;
    const VIP_2 = 2;
    const VIP_3 = 3;
    const VIP_4 = 4;
    const VIP_5 = 5;
    const VIP_6 = 6;
    const VIP_7 = 7;
    const VIP_8 = 8;
    const VIP_9 = 9;
    const VIP_10 = 10;
    const VIP_11 = 11;
    const VIP_12 = 12;
    const VIP_13 = 13;
    const VIP_14 = 14;
    const VIP_15 = 15;

    const GIFT_1 = 1;
    const GIFT_2 = 2;
    const GIFT_3 = 3;
    const GIFT_4 = 4;
    const GIFT_5 = 5;
    
    const MAX_RANK = 10000;
    
    public static $vipExpr = array(
        User_Config_Vip::VIP_1 => 0,
        User_Config_Vip::VIP_2 => 1,
        User_Config_Vip::VIP_3 => 100,
        User_Config_Vip::VIP_4 => 200,
        User_Config_Vip::VIP_5 => 500,
        User_Config_Vip::VIP_6 => 1000,
        User_Config_Vip::VIP_7 => 2000,
        User_Config_Vip::VIP_8 => 5000,
        User_Config_Vip::VIP_9 => 10000,
        User_Config_Vip::VIP_10 => 20000,
        User_Config_Vip::VIP_11 => 50000,
        User_Config_Vip::VIP_12 => 100000,
        User_Config_Vip::VIP_13 => 200000,
        User_Config_Vip::VIP_14 => 500000,
        User_Config_Vip::VIP_15 => 1000000,
    );

    
    public static $giftName = array(
        User_Config_Vip::GIFT_1 => "新手礼包",
        User_Config_Vip::GIFT_2 => "初级特权礼包",
        User_Config_Vip::GIFT_3 => "中级特权礼包",
        User_Config_Vip::GIFT_4 => "高级特权礼包",
        User_Config_Vip::GIFT_5 => "限量特权礼包",
    );
    
    public static $vipName = array(
            User_Config_Vip::GIFT_1 => "VIP1+",
            User_Config_Vip::GIFT_2 => "VIP2+",
            User_Config_Vip::GIFT_3 => "VIP4+",
            User_Config_Vip::GIFT_4 => "VIP7+",
            User_Config_Vip::GIFT_5 => "VIP11+",
    );

    public static $vipGift = array(
        User_Config_Vip::VIP_1 => User_Config_Vip::GIFT_1,
        User_Config_Vip::VIP_2 => User_Config_Vip::GIFT_2,
        User_Config_Vip::VIP_3 => User_Config_Vip::GIFT_2,
        User_Config_Vip::VIP_4 => User_Config_Vip::GIFT_3,
        User_Config_Vip::VIP_5 => User_Config_Vip::GIFT_3,
        User_Config_Vip::VIP_6 => User_Config_Vip::GIFT_3,
        User_Config_Vip::VIP_7 => User_Config_Vip::GIFT_4,
        User_Config_Vip::VIP_8 => User_Config_Vip::GIFT_4,
        User_Config_Vip::VIP_9 => User_Config_Vip::GIFT_4,
        User_Config_Vip::VIP_10 => User_Config_Vip::GIFT_4,
        User_Config_Vip::VIP_11 => User_Config_Vip::GIFT_5,
        User_Config_Vip::VIP_12 => User_Config_Vip::GIFT_5,
        User_Config_Vip::VIP_13 => User_Config_Vip::GIFT_5,
        User_Config_Vip::VIP_14 => User_Config_Vip::GIFT_5,
        User_Config_Vip::VIP_15 => User_Config_Vip::GIFT_5,
    );
    
    public static $vipTicket = array(
        User_Config_Vip::VIP_1 => 0,
        User_Config_Vip::VIP_2 => 0,
        User_Config_Vip::VIP_3 => 0,
        User_Config_Vip::VIP_4 => 0,
        User_Config_Vip::VIP_5 => 10,
        User_Config_Vip::VIP_6 => 30,
        User_Config_Vip::VIP_7 => 50,
        User_Config_Vip::VIP_8 => 100,
        User_Config_Vip::VIP_9 => 150,
        User_Config_Vip::VIP_10 => 200,
        User_Config_Vip::VIP_11 => 300,
        User_Config_Vip::VIP_12 => 500,
        User_Config_Vip::VIP_13 => 1000,
        User_Config_Vip::VIP_14 => 2000,
        User_Config_Vip::VIP_15 => 5000,
    );
    
    public static function getVip($expr) {
        $vip = 0;
        foreach (User_Config_Vip::$vipExpr as $key => $value) {
            if($expr >= $value) {
                $vip = $key;
            }else{
                break;
            }
        }
        return $vip;
    }
    
    public static function getNextVip($vip) {
        $vipNextLevel = $vip + 1;
        if(! isset(User_Config_Vip::$vipExpr[$vipNextLevel])) {
            $vipNextLevel = 0;
        }
        return $vipNextLevel;
    }
    
    public static function getVipExpr($vip) {
        return User_Config_Vip::$vipExpr[$vip];
    }
    
    public static function getTicketVip($vip) {
        $ticketVip = 0;
        foreach (User_Config_Vip::$vipTicket as $key => $value) {
            if($value == 0) continue;
            if($key <= $vip) continue;
            $ticketVip = $key;
            break;
        }
        return $ticketVip;
    }
    
    public static function getTicket($vip) {
        return User_Config_Vip::$vipTicket[$vip];
    }
    
    public static function getVipList() {
        return array_keys(User_Config_Vip::$vipExpr);
    }
    
    public static function getVipListByGiftLevle($giftLevel) {
        $vipList = array();
        foreach (self::$vipGift as $vip => $level) {
            if($giftLevel == $level) {
                $vipList[] = $vip;
            }
        }
        return $vipList;
    }
    
    public static function getGiftLevel($vipLevel) {
        return self::$vipGift[$vipLevel];
    }
    
}

