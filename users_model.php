<?php
	class Users_model extends CI_Model{
		public function __construct(){
		}
		public function get_users(){
			$this->db->select("id, username, firstname, lastname, activated");
			$this->db->from("user");
			return $this->db->get();
		}
		public function get_user($id){
			$this->db->select("id, username, firstname, lastname, activated,type");
			$this->db->from("user");
			$this->db->where("id",$id);
			return $this->db->get();
		}
		public function edit_password($id, $newpassword){
			$data=array(
				"password"=>$password
			);
			$this->db->update("user",$data);
			$this->db->where("id",$id);
		}
		public function edit_firstname($id, $firstname){
			$data=array(
				"firstname"=>$firstname
			);
			$this->db->update("user",$data);
			$this->db->where("id",$id);
		}
		public function edit_lastname($id, $lastname){
			$data=array(
				"lastname"=>$lastname
			);
			$this->db->update("user",$data);
			$this->db->where("id",$id);
		}
		public function edit_activation($id, $token){
			$data=array(
				"activated"=>$token
			);
			$this->db->where("id",$id);
			$this->db->update("user",$data);
			
		}
		public function edit_type($id,$type){
			$data=array(
				"type"=>$type
			);
			$this->db->where("id",$id);
			$this->db->update("user",$data);
		}
		
		
	}
?>