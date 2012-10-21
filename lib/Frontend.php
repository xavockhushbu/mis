<?php
/**
 * Consult documentation on http://agiletoolkit.org/learn 
 */
class Frontend extends ApiFrontend {
    function init(){
        parent::init();
        // Keep this if you are going to use database on all pages
        $this->dbConnect();
        $this->requires('atk','4.2.0');

        // This will add some resources from atk4-addons, which would be located
        // in atk4-addons subdirectory.
        $this->addLocation('atk4-addons',array(
                    'php'=>array(
                        'mvc',
                        'misc/lib',
                        )
                    ))
            ->setParent($this->pathfinder->base_location);

        $xa=$this->addLocation('.',array(
            "addons"=>'xavoc-addons'
            ));

        // A lot of the functionality in Agile Toolkit requires jUI
        $this->add('jUI');

        // Initialize any system-wide javascript libraries here
        // If you are willing to write custom JavaScritp code,
        // place it into templates/js/atk4_univ_ext.js and
        // include it here
        $this->js()
            ->_load('atk4_univ')
            ->_load('ui.atk4_notify')
            ->_load('ui.combobox')
            ;

        // If you wish to restrict actess to your pages, use BasicAuth class
        $auth=$this->add('BasicAuth');
        $auth->setModel('Member','username','password');
//            ->allow('demo','demo')
            // use check() and allowPage for white-list based auth checking
            $auth->check();
            // ;

        // This method is executed for ALL the peages you are going to add,
        // before the page class is loaded. You can put additional checks
        // or initialize additional elements in here which are common to all
        // the pages.

        // Menu:

        // If you are using a complex menu, you can re-define
        // it and place in a separate class
        $menu=$this->add('Menu',null,'Menu')
            ->addMenuItem('index','Dashboard');

        if($this->auth->model->ref('acl_id')->get('Level') == 100){
            $menu->addMenuItem('branches','Branches');
            $menu->addMenuItem('products','Products');
            $menu->addMenuItem('members','Members');
        }
        elseif($this->auth->model->ref('acl_id')->get('Level')  >= 50){
            $menu->addMenuItem('members','Members');
        }
            $menu->addMenuItem('sales_entry','Sales Entry');
            $menu->addMenuItem('report','Report');
            $menu->addMenuItem('pivot','Pivot');
            $menu->addMenuItem('logout');
            
            
        $this->add('View_License',null,'my_version');

        $this->addLayout('UserMenu');
        $this->add('H1',null,'logo')->set('Welcome '. $this->auth->model['name']. " [ " . $this->auth->model['acl'] ." ] (" . $this->auth->model->ref('acl_id')->get('Level') .")" );
    }
    
    function layout_UserMenu(){
        if($this->auth->isLoggedIn()){
            $this->add('Text',null,'UserMenu')
                ->set('Hello, '.$this->auth->get('username').' | ');
            $this->add('HtmlElement',null,'UserMenu')
                ->setElement('a')
                ->set('Logout')
                ->setAttr('href',$this->getDestinationURL('logout'))
                ;
        }else{
            $this->add('HtmlElement',null,'UserMenu')
                ->setElement('a')
                ->set('Login')
                ->setAttr('href',$this->getDestinationURL('authtest'))
                ;
        }
    }
    function page_examples($p){
        header('Location: '.$this->pm->base_path.'examples');
        exit;
    }
}
