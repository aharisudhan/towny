<?php
class HomeModel extends Model {

 function HomeModel(){
  parent::Model();
 }

 function getEmployees(){
  $this->db->select("`pid`, `pname`, `pimg_path`, `pdesc`, `status`");
  $this->db->from('promotions');
  $query = $this->db->get();
  return $query->result();
 }
}
?>