<?php

namespace App\Controller\Empresa;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/empresa/api/tester', name: 'app_empresa_api_tester_')]
class APITesterController extends AbstractController
{
    #[Route('/', name: 'mostrar')]
    public function index()
    {
        return $this->render('empresa/api_tester/index.html.twig');
    }

	#[Route('/request', name: 'request', methods: ['POST'])]
	public function request(Request $request): JsonResponse
	{
		// Solo funciona si estamos en prod o en un servidor local que no sea el de symfony caddy, apache o nginx
		$url = $request->request->get('url');
		$method = $request->request->get('method');
		$optionsJson = $request->request->get('options');
		
		if ($url && $method && $optionsJson) {
			// Evitar auto-llamadas: Verificar que la URL no sea la misma ruta
			if (strpos($url, $request->getUri()) !== false) {
				return $this->json(['error' => 'No se puede hacer la solicitud a la misma ruta.'], JsonResponse::HTTP_BAD_REQUEST);
			}

			$client = new Client(['verify' => false]);

			// Decodificar opciones de JSON a array
			$options = $optionsJson ? json_decode($optionsJson, true) : [];

			// Añadir logging para depuración
			error_log('Request to ' . $url);
			error_log('Method: ' . $method);
			error_log('Options: ' . print_r($options, true));

			try {
				$response = $client->request($method, $url, $options);
				$data = json_decode($response->getBody()->getContents(), true);
				return $this->json($data, JsonResponse::HTTP_OK);
			} catch (RequestException $e) {
				error_log('Request error: ' . $e->getMessage());
				$errorResponse = [
					'error' => 'Request failed',
					'message' => $e->getMessage(),
				];
				if ($e->hasResponse()) {
					$errorResponse['response'] = json_decode($e->getResponse()->getBody()->getContents(), true);
				}
				return $this->json($errorResponse, JsonResponse::HTTP_BAD_REQUEST);
			} catch (\Exception $e) {
				error_log('Request error: ' . $e->getMessage());
				return $this->json(['error' => 'Invalid request'], JsonResponse::HTTP_BAD_REQUEST);
			}
		}
		return $this->json(['error' => 'Invalid request'], JsonResponse::HTTP_BAD_REQUEST);
	}
}
