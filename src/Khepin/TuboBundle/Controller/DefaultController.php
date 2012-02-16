<?php

namespace Khepin\TuboBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Khepin\TuboBundle\Entity\Photo;
use Buzz\Message\FormUpload;
use Buzz\Message\FormRequest;

class DefaultController extends Controller {

    /**
     * @Route("/")
     * @Route("/hello/")
     * @Template()
     */
    public function indexAction() {
        $buzz = $this->get('buzz');
//        $response = $buzz->get('http://b.mytubo.net/index.php?action=getfeedsNew&TargetUserID=155287&Limit=10&Reflesh=0&MinPicID=99999999&UserID=-1&Email=&Token=ff4009e330710727c594c7698e8a0b2a&_=1329303139384');
//        $name = json_decode($response->getContent());
//
//        $em = $this->getDoctrine()->getEntityManager();
//        $q = $em->createQuery('SELECT MAX(p.tubo_id) FROM KhepinTuboBundle:Photo p');
//        $max = $q->getSingleScalarResult();
//        echo $max;
//        $new = array();
//        foreach ($name->data as $pic) {
//            if ($pic->PicID > $max) {
//                $photo = new Photo();
//                $photo->setTuboId($pic->PicID);
//                $photo->setLegend($pic->Description);
//                $photo->setLink($pic->OriURL);
//                $em->persist($photo);
//                $new[] = $photo;
//            }
//        }
//        $em->flush();
        
        $access_token = $this->get('fos_facebook.api')->getAccessToken();
        $graph_url= "https://graph.facebook.com";
        $resource = "/621327693/photos?access_token=" .$access_token;
//        $buzz->getClient()->setTimeout(20000);
//        $file = $buzz->get('http://p.mytubo.net/Pictures/Original/2012-02-15/1329323372722_155287.jpg')->getContent();
//
//        $form = new FormUpload();
//        $form->setContent($file);
//        $form->setFilename('picture.jpg');
//        $form->setName('source');
//        
//        $request = new FormRequest('POST', $resource, $graph_url);
//        $request->setField('name', 'Some Name');
//        $request->setField('source', $form);
//        
//        $response = new \Buzz\Message\Response();
//        $buzz->getClient()->send($request, $response);
//        var_dump($response);
//        foreach($new as $photo){
//            
//        }
        return array();
    }

    public function importAction() {
        $buzz = $this->get('buzz');
        $response = $buzz->get('http://b.mytubo.net/index.php?action=getfeedsNew&TargetUserID=155287&Limit=10&Reflesh=0&MinPicID=99999999&UserID=-1&Email=&Token=ff4009e330710727c594c7698e8a0b2a&_=1329303139384');
        $name = json_decode($response->getContent());

        $em = $this->getDoctrine()->getEntityManager();
        foreach ($name->data as $pic) {
            $photo = new Photo();
            $photo->setTuboId($pic->PicID);
            $photo->setLegend($pic->Description);
            $photo->setLink($pic->OriURL);
            $em->persist($photo);
        }
        $em->flush();
        return array();
    }

}
