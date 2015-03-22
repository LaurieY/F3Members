<?php

class UserController extends Controller {

	public function index()
    {$f3=$this->f3;
        $user = new User($this->db);
        $f3->set('users',$user->all());
        $f3->set('page_head','User List');
        $f3->set('message', $f3->get('PARAMS.message'));
        $f3->set('view','user/list.htm');

	}
	
		
    public function create()
    {$f3=$this->f3;
        if($f3->exists('POST.create'))
        {
            $user = new User($this->db);
            $user->add();

            $f3->reroute('/success/New User Created');
        } else
        {
            $f3->set('page_head','Create User');
            $f3->set('view','user/create.htm');
        }

    }

    public function update()
    {$f3=$this->f3;
	$admin_logger = new Log('admin.log');
	$admin_logger->write('in User Update');
		$user = new User($this->db);
		$f3->set('mem_users',$user->all());
        

        if($f3->exists('POST.update'))
        {
            $user->edit($f3->get('POST.usr'));
            $f3->reroute('/admin');
        } else
        {$admin_logger->write('in User Update PARAMS.message is '.$f3->get('PARAMS.message'));
		$admin_logger->write('in User Update PARAMS.usr is '.$f3->get('PARAMS.usr'));
            $user->getById($f3->get('PARAMS.usr'));
			$f3->set('message','');
            $f3->set('user',$user);
            $f3->set('page_head','Update User');
            $f3->set('view','user/update.htm');
        }

    }

    public function delete()
    {$f3=$this->f3;
        if($f3->exists('PARAMS.id'))
        {
            $user = new User($this->db);
            $user->delete($f3->get('PARAMS.id'));
        }

        $f3->reroute('/success/User Deleted');
    }
}