<?php
class GiftCodesAction extends SystemAction
{
    public $model = "GiftCodes";

    public function _filter(&$map)
    {
        $_search = MAP();
        $map = !empty($_search) ? array_merge($_search, $map) : $map;
    }
}