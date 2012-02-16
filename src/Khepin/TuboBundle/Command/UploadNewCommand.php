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
        $user = $this->getContainer()->get('doctrine')->getRepository('KhepinTuboBundle:User')
                ->findOneBy(array('username' => '621327693'));
        $buzz = $this->getContainer()->get('buzz');
        $access_token = $user->getAccessToken();
        $graph_url= 'https://graph.facebook.com';
        $resource = '/'.$user->getUsername().'/photos?access_token=' .$access_token;
        echo $access_token;
        $buzz->getClient()->setTimeout(20000);
        $file = $buzz->get('http://p.mytubo.net/Pictures/Original/2012-02-15/1329323372722_155287.jpg')->getContent();

        $form = new FormUpload();
        $form->setContent($file);
        $form->setFilename('picture.jpg');
        $form->setName('source');
        
        $request = new FormRequest('POST', $resource, $graph_url);
        $request->setField('name', 'Some Name');
        $request->setField('source', $form);
        
        $response = new \Buzz\Message\Response();
        $buzz->getClient()->send($request, $response);
        
        var_dump($response);
    }

}