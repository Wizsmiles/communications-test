<?php


namespace App\Controller\Communications;


use App\Services\Clients\HttpClientInterface;
use App\Services\Communications\CommunicationsParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class GetCommunicationsController extends AbstractController
{
    private $client;
    private $parser;

    public function __construct(HttpClientInterface $httpClient, CommunicationsParser $communicationsParser)
    {
        $this->client = $httpClient;
        $this->parser = $communicationsParser;
    }

    public function __invoke(Request $request)
    {
        try {
            $uri = $this->getParameter('communications_server');
            $data = $this->client->request(
                Request::METHOD_GET,
                $uri
            );
            $contacts = ($this->parser)($data);
            return $this->render('dashboard.html.twig', ['contacts' => $contacts]);
        } catch (\Exception $ex) {
            return $this->render('dashboard.html.twig', ['errors' => 'No data available!']);

        }
    }

}