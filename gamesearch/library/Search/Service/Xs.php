<?php
require_once __DIR__ . '/XS/XS.php';
class Search_Service_Xs
{
    private $_xs;
    private $_highlight;

    public function __construct($highlight = false)
    {
        $this->_highlight = $highlight;
    }

    public function suggest($query)
    {
        $this->_xs = new \XS('game');
        $search = $this->_xs->getSearch(); //建立项目对象
        $data = $search->getExpandedQuery($query);
        return $data;
    }

    public function searchGame($query, $page, $limit = 10){
    	
    	
        $this->_xs = new \XS('game');
        $search = $this->_xs->getSearch(); //建立项目对象
        $offset = ($page - 1) * $limit;
        $search->setLimit($limit, $offset); // 设置返回结果最多为 $rn 条，并跳过前 $offset 条, 默认每页10条

        
        $docs = $search->setAutoSynonyms()->setFuzzy()->setQuery($query)->search();
        $totalCount = $search->setQuery($query)->count();
           
        if (0 == $totalCount) {
            $corrected = $search->getCorrectedQuery();
            if (!empty($corrected)) {
                $query = $corrected[0];
                $docs = $search->setAutoSynonyms()->setFuzzy()->setQuery($query)->search();
                $totalCount = $search->setAutoSynonyms()->setFuzzy()->setQuery($query)->Count();
            }
        }

        $data = array();
        foreach ($docs as $val){
            $item = array(
                'id' => $val->id,
                'name' => $val->name,
                'resume' => $val->resume,
                'label' => $val->label,
                'create_time' => $val->create_time,
            );

            if (true === $this->_highlight) {
                $item['name'] = $search->highlight($val->name);
            }

            $data[] = $item;
        }


        $hasNext   = (($page * $limit)  >= $totalCount) ? false : true;
        
        return array(
		            'hasNext' => $hasNext,
		            'totalCount' => $totalCount,
		        	'curPage' => $page,
		            'list'    => $data,
        );
    }

    public function addIndex($type, $data)
    {
        // 创建文档对象
        $doc = new \XSDocument;
        $doc->setFields($data);

        // 添加到索引数据库中
        $this->_xs = new \XS($type);
        $index = $this->_xs->getIndex();
        return $index->add($doc)->flushIndex();
    }

    public function updateIndex($type, $data)
    {
        // 创建文档对象
        $doc = new \XSDocument;
        $doc->setFields($data);

        // 更新到索引数据库中
        $this->_xs = new \XS($type);
        $index = $this->_xs->getIndex();
        return $index->update($doc)->flushIndex();
    }

    public function deleteIndex($type, $priKey)
    {
        // 从索引数据库中删除
        $this->_xs = new \XS($type);
        $index = $this->_xs->getIndex();
        return $index->del($priKey)->flushIndex();
    }
    
    public function cleanIndex()
    {
    	// 从索引数据库中删除
        $this->_xs = new \XS('game');
    	$index = $this->_xs->getIndex();
    	return $index->clean();
    }
    
    public function getCustomDict(){
    	// 从索引数据库中删除
    	$this->_xs = new \XS('game');
    	$index = $this->_xs->getIndex();
    	return $index->getScwsMulti();
    }

}
