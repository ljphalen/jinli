<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * @author Ryan
 * Class Apk_UserController
 */
class SuperController extends Api_BaseController {
  public function jsAction() {
    header('Content-Type: application/javascript');
    $jsfile = Common::getConfig("apiConfig", "superJs");
    echo file_get_contents($jsfile.'?t='.Common::getConfig('siteConfig', 'version'));
  }
  public function cssAction() {
    header('Content-Type: text/css');
    $cssfile = Common::getConfig("apiConfig", "superCss");
    echo file_get_contents($cssfile.'?t='.Common::getConfig('siteConfig', 'version'));
  }
}
