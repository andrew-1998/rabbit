<?php
function del_name ($arr, $name) {
    for (i=0; i<count($arr); i++) { 
        foreach($arr[i] as $key => $val) {
            if($val['name'] == 'Иванов') {
                unset($arr[i]);
                break;
            }
        }
    }
    return $arr;
}

$data=array(
array(
'name' => 'Иванов', 
'specialty' => 'хирург'
'),
array (
'name' => 'Петров',
'specialty' => 'кардиолог'
),

);
?>