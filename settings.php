<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->model("settings_model");
		$this->load->library("session");
		$user_id=$this->session->userdata("id");
		$user=$this->settings_model->get_basic($user_id);
		$user_data=$user->row();
		$data=array(
			"user_data"=>$user_data
		);
		$this->load->view('settings',$data);
	}
	public function update_auth_settings()
	{
		$this->load->library("session");
		$user_id=$this->session->userdata("id");
		$pass=$this->input->post("password");
		$re_pass=$this->input->post("re_password");
		$data;
		if($pass==$re_pass){
			$user=array(
				"password"=>md5($pass)
			);
			$this->db->where("id",$user_id);
			$this->db->update("user",$user);
			$data=array(
			"display"=>'password  updated'
			);
		}
		else{
			$data=array(
			"display"=>'The two inputs do not match'
			);
		}
		
		$this->load->view('settings',$data);
	}
	public function update_basic()
	{
		$this->load->library("session");
		$this->load->model("settings_model");
		$user_id=$this->session->userdata("id");
		$firstname=$this->input->post("firstname");
		$lastname=$this->input->post("lastname");
		$phone_number=$this->input->post("phone_number");
		$town=$this->input->post("town");
		$user=array(
			"firstname"=>$firstname,
			"lastname"=>$lastname
		);
		$user_details=array(
			"town"=>$town,
			"mobile_number"=>$phone_number
		);
		$this->db->where("id",$user_id);
		$this->db->update("user",$user);

		$this->db->where("user_id",$user_id);
		$this->db->update("user_details",$user_details);

		$user=$this->settings_model->get_basic($user_id);
		$user_data=$user->row();
		$data=array(
			"display"=>'basic information updated',
			"user_data"=>$user_data
		);
		$this->load->view('settings',$data);
	}
	public function update_privacy_settings()
	{
		$data=array(
			"display"=>'privacy settings updated'
		);
		$this->load->view('settings',$data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */