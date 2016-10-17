<?php
	class Settings_model extends CI_Model{
		public function __construct(){
		}
		function get_basic($user_id){
			$this->load->library("session");
			$user_id=$this->session->userdata("id");
			$this->db->select("user.firstname as fname, user.lastname as lname, user_details.mobile_number as phone_number, user_details.town as town");
			$this->db->from("user");
			$this->db->join("user_details","user.id=user_details.user_id");
			$this->db->where("user.id",$user_id);
			$user=$this->db->get();
			return $user;
		}
	}
?>