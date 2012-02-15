<?php

namespace Khepin\TuboBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Route("/hello/")
     * @Template()
     */
    public function indexAction()
    {
        $buzz = $this->get('buzz');
        $response = $buzz->get('http://b.mytubo.net/index.php?action=getfeedsNew&TargetUserID=155287&Limit=10&Reflesh=0&MinPicID=99999999&UserID=-1&Email=&Token=ff4009e330710727c594c7698e8a0b2a&_=1329303139384');
        $name = json_decode($response->getContent());
        foreach($name->data as $pic){
            echo $pic->PicID.' - <img src="'.$pic->OriURL.'" /> - '.$pic->Description.'<br>';
        }
        $name = 'paul';
        return array('name' => $name);
    }
    
    /**
     * @Route("/s/test") 
     */
    public function testAction(){
        return new \Symfony\Component\HttpFoundation\Response('huhuhu');
    }
}
