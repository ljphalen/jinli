<?php
use Guzzle\Service\Resource\Model;
class DataDictAction extends Action {
	function index(){
		$model = new AdvModel();
		$model->query('set names ' . C('DB_CHARSET'));
		$sql = "SHOW TABLE STATUS FROM " . C('DB_NAME');
		$result = $model->query($sql);
		// table count
		$tab_count = count($result);
		
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="zh">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>'.C('SITENAME').'--数据字典</title>
<style type="text/css">
    table caption, table th, table td {
        padding: 0.1em 0.5em 0.1em 0.5em;
        margin: 0.1em;
        vertical-align: top;
    }
    th {
        font-weight: bold;
        color: black;
        background: #D3DCE3;
    }
    table tr.odd th, .odd {
        background: #E5E5E5;
    }
    table tr.even th, .even {
        background: #f3f3f3;
    }
    .db_table{
        border-top:1px solid #333;
    }
    .title{font-weight:bold;}
</style>
</head>
<body>
<div style="text-align:center;background:#D3DCE3;font-size:19px;">
    <b>'.APPNAME.'--数据字典</b>
</div>
<div style="background:#f3f3f3;text-align:center;">（注：共'.$tab_count.'张表，按ctrl+F查找关键字）</div>'."\n";
		for($i=0;$i<$tab_count;$i++){
			
			//查询数据库字段信息
			$tab_name = $result[$i]['Name'];
			$sql_tab = 'show full fields from `' . $result[$i]['Name'] . '`';
			$tab_ddl = $model->query("show create table `{$result[$i]['Name']}`");
			$tab_array = $model->query($sql_tab);
			
			echo '<ul type="square">'."\n";
			echo '  <li class="title">';
			echo ($i+1).'、表名：[' . $result[$i]['Name'] . ']&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注释：' . $result[$i]['Comment'];
			echo '</li>'."\n";

			//show keys
			$arr_keys = $model->query("show keys from `".$result[$i]['Name'].'`');
			echo '<li style="list-style: none outside none;"><table border="0" class="db_table" >';
			echo '<tr class="head">
        <th style="width:110px">字段</th>
        <th>类型</th>
        <th>为空</th>
        <th>额外</th>
        <th>默认</th>
        <th style="width:95px">整理</th>
        <th>备注</th></tr>';
			for($j=0;$j<count($tab_array);$j++){
				$key_name=$arr_keys['Key_name'];
				if($key_name="PRIMARY"){
					$key_name='主键（'.$key_name.'）';
				}
				$key_field=$arr_keys['Column_name'];
				if ( $tab_array[$j]['Field']==$key_field){
					$key_value="PK";
				}else{
					$key_value="";
				}
				echo '        <tr class="'.($j%2==0?"odd":"even").'">'."\n";
				echo '          <td>' . $tab_array[$j]['Field'] . '</td>'."\n";
				echo '          <td>' . $tab_array[$j]['Type'] . '</td>'."\n";
				echo '          <td>' . ($key_value!=''?$key_value:$tab_array[$j]['Null']) . '</td>'."\n";
				echo '          <td>' . $tab_array[$j]['Extra'] . '</td>'."\n";
				echo '          <td>' . $tab_array[$j]['Default'] . '</td>'."\n";
				echo '          <td>' . $tab_array[$j]['Collation'] . '</td>'."\n";
				echo '          <td>' . ($key_value!=''?$key_name:$tab_array[$j]['Comment']) . '</td>'."\n";
				echo '        </tr>'."\n";
			}
			echo '  </table></li>'."\n";
			echo "<pre>{$tab_ddl[0]['Create Table']}</pre>";
			echo '</ul>'."\n";
		
		}
		echo '</body>'."\n";
		echo '</html>'."\n";
		die;
	}
}

?>