<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    //private RequestStack $requestStack;

    private string $backlink = 'lsq'; // přidělený název služby
    private string $successParams = '/login/confirm/%s'; // návratová cesta, pouze [A-Za-z0-9\%\/\.\?\&]
    private string $failureParams = '/login/error/%s'; // návratová cesta, pouze [A-Za-z0-9\%\/\.\?\&]
    // NEMĚNIT!
    private string $tokenUrl = 'https://leosight.cz/api/public/login/token?b=%s&s=%s&f=%s';
    private string $captureUrl = 'https://leosight.cz/api/public/login/capture?token=%s';

    /*public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }*/

    /*public function logged(): bool
    {
        $session = $this->requestStack->getSession();
        return $session->get('user:id') != null;
    }*/

    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        if($this->getParameter('kernel.debug')){
            $this->backlink = 'debug';
        }

        return $this->redirect(sprintf($this->tokenUrl, $this->backlink, $this->successParams, $this->failureParams));
    }

    #[Route('/login/confirm/{token}')]
    public function confirm($token, ManagerRegistry $doctrine, Security $security): Response
    {
        $data = $this->capture($token);

        if(!$data->logged){
            return $this->redirect('/login/error/invalid');
        }

        //$session = $this->requestStack->getSession();
        //$session->set('user:id', $data->id);
        //$session->set('user:name', $data->username);

        $user = $doctrine->getRepository(User::class)->find($data->id);
        if (!$user) {
            $entityManager = $doctrine->getManager();

            $user = new User();
            $user->setId($data->id)->setUsername($data->username)->setLastSeen(new \DateTime('now'));

            $entityManager->persist($user);
            $entityManager->flush();
        }

        $security->login($user);

        return $this->redirect('/game');
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(Security $security): Response
    {
        //$session = $this->requestStack->getSession();
        //$session->clear();

        if($this->getUser()) {
            $security->logout(false);
        }

        return $this->redirect('/');
    }

    // volat pouze 1x! (úspěšný capture zruší platnost tokenu)
    // ex. output: [ 'logged' => false ] / [ 'logged' => true, 'id' => 1, 'username' => 'Rataj' ]
    function capture($token){
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_USERAGENT      => 'LSLogin',
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_CONNECTTIMEOUT => 60,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_SSL_VERIFYPEER => false
        ];

        $ch = curl_init(sprintf($this->captureUrl, $token));
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        curl_close($ch);

        return json_decode($content);
    }
}
