<?php

  if ( ! defined('BASE_PATH')) exit('Access Denied!');

  /**
   * @author rainkid
   *
   */
  class Queue_Factory {

      static $instances;

      static public function getQueue($queueType = 'redis') {
          if (isset(self::$instances[$queueType])) return self::$instances[$queueType];
          switch ($queueType) {
              case 'redis':
              default:
                  $config = Common::getConfig('redisConfig' , ENV);
                  self::$instances[$queueType] = new Queue_Redis($config);
                  return self::$instances[$queueType];
                  break;
          }
      }

  }
