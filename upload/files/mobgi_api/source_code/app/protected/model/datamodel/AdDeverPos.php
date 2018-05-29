<?php
Doo::loadModel('datamodel/base/AdDeverPosBase');

class AdDeverPos extends AdDeverPosBase{
    /**
     * 根据deverposkey获取某条广告位的详细信息
     * @param type $deverposkey
     * @return type
     */
    public function getPosByDeverposkey($deverposkey){
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*';
        $whereArr['where'] = "dever_pos_key='".$deverposkey."'";
        $result = $this->getOne($whereArr);
        return $result;
    }
}