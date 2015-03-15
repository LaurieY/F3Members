<?php

class UserController extends Controller {

	public function index()
    {
        $user = new User($this->db);
        $this->f3->set('users',$user->all());
        $this->f3->set('page_head','User List');
        $this->f3->set('message', $this->f3->get('PARAMS.message'));
        $this->f3->set('view','user/list.htm');

	}
		public function listn()	
	{
	        $user = new User($this->db);
        $this->f3->set('users',$user->all());
		        $this->f3->set('page_head','User List');
        $this->f3->set('message', $this->f3->get('PARAMS.message'));
		$this->f3->set('listn', $this->f3->get('PARAMS.mylist'));

		
	     $this->f3->set('listnn','user/list'.$this->f3->get('listn').'.htm');
		 $this->f3->set('view','user/list'.$this->f3->get('listn').'.htm');
		 
	}
/*	public function list2()	
	{
	        $user = new User($this->db);
        $this->f3->set('users',$user->all());
		        $this->f3->set('page_head','User List');
        $this->f3->set('message', $this->f3->get('PARAMS.message'));
	        $this->f3->set('view','user/list2.htm');
	}
	public function list3()	
	{
	        $user = new User($this->db);
        $this->f3->set('users',$user->all());
		        $this->f3->set('page_head','User List');
     //   $this->f3->set('message', $this->f3->get('PARAMS.message'));
	        $this->f3->set('view','user/list3.htm');
	}
		
	public function list4()	
	{
	        $user = new User($this->db);
        $this->f3->set('users',$user->all());
		        $this->f3->set('page_head','User List');
     //   $this->f3->set('message', $this->f3->get('PARAMS.message'));
	        $this->f3->set('view','user/list4.htm');
	}
	*/
	public function listget_json()
	{
	header("Content-type: text/xml;charset=utf-8"); 
	echo ' id: "1", invdate: "2007-10-01", name: "test", note: "note", amount: "200.00", tax: "10.00", total: "210.00" }';
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