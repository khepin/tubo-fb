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
     * @Template()
     */
    public function indexAction() {
        $buzz = $this->get('buzz');
        $response = $buzz->get('http://b.mytubo.net/index.php?action=getfeedsNew&TargetUserID=155287&Limit=10&Reflesh=0&MinPicID=99999999&UserID=-1&Email=&Token=ff4009e330710727c594c7698e8a0b2a&_=1329303139384');
        $name = json_decode($response->getContent());

        $em = $this->getDoctrine()->getEntityManager();
        $q = $em->createQuery('SELECT MAX(p.tubo_id) FROM KhepinTuboBundle:Photo p');
        $max = $q->getSingleScalarResult();
        echo $max;
        $new = array();
        foreach ($name->data as $pic) {
            if ($pic->PicID > $max) {
                $photo = new Photo();
                $photo->setTuboId($pic->PicID);
                $photo->setLegend($pic->Description);
                $photo->setLink($pic->OriURL);
                $em->persist($photo);
                $new[] = $photo;
            }
        }
        $em->flush();
    }

    /**
     * @Route("/u/pics") 
     */
    public function updatepicsAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $q = $em->createQuery('SELECT MAX(p.tubo_id) FROM KhepinTuboBundle:Photo p');
        $last_known_id = $q->getSingleScalarResult();
        $pics = $this->getRecentPics($last_known_id, $em);

        $user = $this->getDoctrine()->getRepository('KhepinTuboBundle:User')
                ->findOneBy(array('username' => '621327693'));

        foreach ($pics as $pic) {
            $this->uploadPic($pic, $user);
        }
        $r = 'We found ' . count($pics) . ' new pictures to upload to Facebook.';
        return new \Symfony\Component\HttpFoundation\Response($r);
    }

    protected function uploadPic(Photo $photo, $user) {
        $buzz = $this->get('buzz');
        $access_token = $user->getAccessToken();
        $domain = 'https://graph.facebook.com';
        $resource = '/' . $user->getUsername() . '/photos?access_token=' . $access_token;
        $buzz->getClient()->setTimeout(20000);
        $file = $buzz->get($photo->getLink())->getContent();

        $form = new FormUpload();
        $form->setContent($file);
        $form->setFilename('picture.jpg');
        $form->setName('source');

        $request = new FormRequest('POST', $resource, $domain);
        $request->setField('name', $photo->getLegend());
        $request->setField('source', $form);

        $response = new \Buzz\Message\Response();
        $buzz->getClient()->send($request, $response);
    }

    protected function getRecentPics($last_known_id, $em = null) {
        $buzz = $this->get('buzz');
        $response = $buzz->get(
                'http://b.mytubo.net/index.php?action=getfeedsNew&TargetUserID=155287&Limit=10&Reflesh=0&MinPicID=99999999&UserID=-1&Email=&Token=ff4009e330710727c594c7698e8a0b2a&_=1329303139384
        ');
        $data = json_decode($response->getContent());
        $new = array();
        foreach ($data->data as $pic) {
            if ($pic->PicID > $last_known_id) {
                $photo = new Photo();
                $photo->setTuboId($pic->PicID);
                $photo->setLegend($pic->Description);
                $photo->setLink($pic->OriURL);
                if (!is_null($em)) {
                    $em->persist($photo);
                }
                $new[] = $photo;
            }
        }
        $em->flush();
        return $new;
    }

}
