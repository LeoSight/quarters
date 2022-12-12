<?php

namespace App\Controller;

use App\Entity\User;
use App\Model\LoginData;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class LoginController extends AbstractController
{
    private Serializer $serializer;

    private string $backlink = 'lsq'; // přidělený název služby
    private string $successParams = '/login/confirm/%s'; // návratová cesta, pouze [A-Za-z0-9\%\/\.\?\&]
    private string $failureParams = '/login/error/%s'; // návratová cesta, pouze [A-Za-z0-9\%\/\.\?\&]
    // NEMĚNIT!
    private string $tokenUrl = 'https://leosight.cz/api/public/login/token?b=%s&s=%s&f=%s';
    private string $captureUrl = 'https://leosight.cz/api/public/login/capture?token=%s';

    public function __construct(){
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        if($this->getParameter('kernel.debug')){
            $this->backlink = 'debug';
        }

        return $this->redirect(sprintf($this->tokenUrl, $this->backlink, $this->successParams, $this->failureParams));
    }

    #[Route('/login/confirm/{token}')]
    public function confirm(string $token, ManagerRegistry $doctrine, Security $security): Response
    {
        $data = $this->capture($token);

        if(!$data->getLogged() || $data->getId() == null || $data->getUsername() == null){
            return $this->redirect('/login/error/invalid');
        }

        $user = $doctrine->getRepository(User::class)->find($data->getId());
        if (!$user) {
            $entityManager = $doctrine->getManager();

            $user = new User();
            $user->setId($data->getId())->setUsername($data->getUsername())->setLastSeen(new \DateTime('now'));

            $entityManager->persist($user);
            $entityManager->flush();
        }

        $security->login($user);

        return $this->redirect('/game');
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(Security $security): Response
    {
        if($this->getUser()) {
            $security->logout(false);
        }

        return $this->redirect('/');
    }

    // volat pouze 1x! (úspěšný capture zruší platnost tokenu)
    // ex. output: [ 'logged' => false ] / [ 'logged' => true, 'id' => 1, 'username' => 'Rataj' ]
    /**
     * @param string $token
     * @return LoginData
     */
    function capture(string $token): LoginData
    {
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
        if(!$ch){
            return (new LoginData())->setLogged(false);
        }

        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        if(!$content){
            return (new LoginData())->setLogged(false);
        }

        curl_close($ch);

        $data = $this->serializer->deserialize($content, LoginData::class, 'json');
        if(!$data instanceof LoginData){
            return (new LoginData())->setLogged(false);
        }

        return $data;
    }
}
