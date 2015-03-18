<?php

class MemberController extends Controller {



		public function index()	
	{
	        $member = new Member($this->db);
        $this->f3->set('members',$member->all());
		        $this->f3->set('page_head','Member List');
        $this->f3->set('message', $this->f3->get('PARAMS.message'));
		$this->f3->set('listn', $this->f3->get('PARAMS.mylist'));

		
	//     $this->f3->set('listnn','member/list'.$this->f3->get('listn').'.htm');
	//	 $this->f3->set('view','member/list'.$this->f3->get('listn').'.htm');
	  $this->f3->set('listnn','member/list.htm');
	$this->f3->set('view','member/list.htm');
		 
	}
		
    public function create()
    {
        if($this->f3->exists('POST.create'))
        {
            $user = new User($this->db);
            $user->add();

            $this->f3->reroute('/success/New User Created');
        } else
        {
            $this->f3->set('page_head','Create User');
            $this->f3->set('view','user/create.htm');
        }

    }

    public function update()
    {

        $user = new User($this->db);

        if($this->f3->exists('POST.update'))
        {
            $user->edit($this->f3->get('POST.id'));
            $this->f3->reroute('/success/User Updated');
        } else
        {
            $user->getById($this->f3->get('PARAMS.id'));
            $this->f3->set('user',$user);
            $this->f3->set('page_head','Update User');
            $this->f3->set('view','user/update.htm');
        }

    }
		public function listn()	
	{
	        $user = new Member($this->db);
        $this->f3->set('members',$user->all());
		        $this->f3->set('page_head','User List');
        $this->f3->set('message', $this->f3->get('PARAMS.message'));
		$this->f3->set('listn', $this->f3->get('PARAMS.mylist'));

		
	     $this->f3->set('listnn','member/list'.$this->f3->get('listn').'.htm');
		 $this->f3->set('view','member/list'.$this->f3->get('listn').'.htm');
	//  $this->f3->set('listnn','member/list.htm');
	//$this->f3->set('view','member/list.htm');
		 
	}
		
   

    public function delete()
    {
        if($this->f3->exists('PARAMS.id'))
        {
            $user = new User($this->db);
            $user->delete($this->f3->get('PARAMS.id'));
        }

        $this->f3->reroute('/success/User Deleted');
    }
}