<?php
set_time_limit(0);

define('DB_NAME', 'sqlite3_rand.db');
define('UUNIQID_LENGTH', 11);
define('DEBUG', false);


// sqlite> select * from uniqid where shuffle_count > 18;
// 16778|64r48nC35nD|19
// 17922|44C3nr5Dn68|20
// sqlite> select count(*) from uniqid;
// 30000

$test = new m_rand;
    for($i=0; $i <30000; $i++){
        $test->uniqid();
        if(DEBUG){
            echo "#count::{$i}";
            echo $test->uniqid();
            echo '<br>';
          }
    }


class m_rand{

  public function m_rand(){
      if(!file_exists(DB_NAME)){
          $this->create_db();
       }
    }

  public function uniqid(){
         $db = new sqlite3(DB_NAME);
         $db->busyTimeout(10000);
         $temp_id = $this->mf_rand();
         $array_shuffle_count = 0;

          do{
            shuffle($temp_id);
            $temp_id_str = implode($temp_id);
            $array_shuffle_count ++;
            $sql_select = "select * from uniqid where uniqids='{$temp_id_str}'";
            $id_check = $db->querySingle($sql_select,true);
              if(DEBUG){
                  echo '<pre>';
                  echo '#00032';
                  var_dump($id_check);
                }
          }while($id_check);
      
          $sql_insert = "insert into uniqid(uniqids,shuffle_count)VALUES('{$temp_id_str}',{$array_shuffle_count})";
          $db->exec($sql_insert);
          $db->close();
          
          return $temp_id_str;
      }

  public function mf_rand(){
          $str = array(range('z','a'),range('Z','A'),range('9','0'));
          $length = UUNIQID_LENGTH;
          $str_r =array();
          for ($i=0; $i<$length ;$i++){
              $temp = $str[array_rand($str)];
              $str_r[count($str_r)] = $temp[array_rand($temp)];
            }
          return($str_r); 
  }

  public function create_db(){
          $db = new sqlite3(DB_NAME);
          $db->busyTimeout(10000);
          $sql_create = 
                  "create table uniqid(
                  rowid INTEGER PRIMARY KEY AUTOINCREMENT,
                  uniqids text UNIQUE,
                  shuffle_count integer
                )";
          $db->exec($sql_create);
          $db->close();
    }
  }

?>

