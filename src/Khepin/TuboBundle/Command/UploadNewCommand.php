<?php

namespace Khepin\TuboBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Khepin\TuboBundle\Entity\Photo;
use Buzz\Message\FormUpload;
use Buzz\Message\FormRequest;

class UploadNewCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('tubo:photo:upload')
                ->setDescription('Greet someone')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $q = $em->createQuery('SELECT MAX(p.tubo_id) FROM KhepinTuboBundle:Photo p');
        $last_known_id = $q->getSingleScalarResult();
        $pics = $this->getRecentPics($last_known_id, $em);
        $output->writeln('Found <info>' . count($pics) . '</info> new pictures.');

        $user = $this->getContainer()->get('doctrine')->getRepository('KhepinTuboBundle:User')
                ->findOneBy(array('username' => '621327693'));

        foreach ($pics as $pic) {
            $output->writeln('Uploading picture: <info>'.$pic->getLegend().'</info>');
            $this->uploadPic($pic, $user);
        }
    }

    protected function uploadPic(Photo $photo, $user) {
        $buzz = $this->getContainer()->get('buzz');
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
        $buzz = $this->getContainer()->get('buzz');
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