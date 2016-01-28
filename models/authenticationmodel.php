<?php
class AuthenticationModel extends CI_Model {
	function AuthenticationModel() {
		parent::__construct();
		$this->load->database();
	}
	
	function checkLogin() {
		$this->db->where('Username', $this->input->post('username'));
		$this->db->where('Passwd', sha1($this->input->post('passwd')));
		$query = $this->db->get('users');
		if($query->num_rows() > 0) {
			$row = $query->row();
			$session = array('id'=>$row->UsersID,
				'name'=>$row->Username,
				'firstname'=>$row->FirstName,
				'surname'=>$row->Surname,
				'email'=>$row->Email,
				'role'=>$row->Role);
			$this->session->set_userdata($session);
			return true;
		} else return false;
	}
    
    public function checkUsername($username) {
        $this->db->select('UsersID');
        $this->db->from('users');
        $this->db->where('Username', $username);
        $query = $this->db->get();
        if ($query->num_rows())
            return TRUE;
        else {
            return FALSE;
        }
    }
    
    public function createAccount($data) {
        $insertArray = array(
            'Username' => $data['username'],
            'Passwd' => sha1($data['passwd']),
            'FirstName' => $data['firstname'],
            'LastName' => $data['lastname'],
            'Email' => $data['email'],
            'Role' => 'User',
        );
        $this->db->insert('users', $insertArray);
        return $this->db->affected_rows();
    }
    
    public function storeCaptcha($captcha) {
        $data = array(
            'captcha_time' => $captcha['time'],
            'ip_address' => $this->input->ip_address(),
            'session_id' => $this->session->userdata('session_id'),
            'word' => $captcha['word']
        );
        $this->db->insert('captcha', $data);
    }
    
    public function getCaptchaWord($length=15) {
        $this->db->select('i.Name');
        $this->db->from('items i');
        $this->db->join('leads l', 'i.ItemsID=l.LeadsID');
        $this->db->where("length(i.Name) <= $length", FALSE, FALSE);
        $this->db->order_by(false, 'random');
        $this->db->limit(1);
        $query = $this->db->get();
        $row = $query->row();
        return $row->Name;
    }
    
    public function validateCaptcha($captcha) {
        // delete old captchas
        $expiration = time()-120;
        $this->db->where('captcha_time <', $expiration);
        $this->db->delete('captcha');
        
        // validate
        $this->db->select('count(*) as count', false);
        $this->db->from('captcha');
        $this->db->where('word', $captcha);
        //$this->db->where('ip_address', $this->input->ip_address());
        $this->db->where('session_id', $this->session->userdata('session_id'));
        $this->db->where('captcha_time >', $expiration);
        $query = $this->db->get();
        $row = $query->row();
        return $row->count;
    }
}
?>