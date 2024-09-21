<?php

namespace App\Services\WhatsApp;

use App\Services\WhatsApp\WhatsAppConfigService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppMessageService
{
    protected $configService;

    public function __construct(WhatsAppConfigService $configService)
    {
        $this->configService = $configService;
    }

    /**
     * Envia uma mensagem de texto.
     *
     * @param string $phone Número de telefone do destinatário.
     * @param string $message Mensagem a ser enviada.
     * @param string|null $messageId Identificador da mensagem de resposta.
     * @return array
     */
    public function sendMessage($phone, $message, $messageId = null)
    {
        if(!$this->isValidInput($phone, $message)) {
            return ['error' => 'WA003Phone number and message cannot be empty'];
        }

        try {
            $url = $this->configService->sendMessageUrl();
            $headers = $this->configService->getDefaultHeaders();

            $response = Http::withHeaders($headers)
                ->post($url, [
                    'phone' => $phone,
                    'message' => $message,
                    'messageId' => $messageId
                ]);

            if(isset($response['error'])) {
                Log::channel('z-api')->error('Erro ao enviar mensagem: ' . $response->body());
                return ['error' => 'WAMS001: ' . $response['message']];
            }

            return $response->json();
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::channel('z-api')->error('Erro na requisição ao enviar mensagem: ' . $e->getMessage());
            return ['error' => 'WAMS002 Request error: ' . $e->getMessage()];
        }catch (\Exception $e) {
            Log::channel('z-api')->error('Erro ao enviar mensagem: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    
    private function isValidInput($phone, $message)
    {
        return !empty($phone) && !empty($message);
    }

}