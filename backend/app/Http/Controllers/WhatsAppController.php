<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WhatsApp\WhatsAppMessageService;
use App\Services\WhatsApp\WhatsAppConfigService;

class WhatsAppController extends Controller
{
    protected $messageService;
    protected $configService;

    /**
     * Construtor para injetar os serviços de configuração e de mensagens.
     *
     * @param WhatsAppConfigService $configService
     * @param WhatsAppMessageService $messageService
     */
    public function __construct(WhatsAppMessageService $messageService, WhatsAppConfigService $configService)
    {
        $this->messageService = $messageService;
        $this->configService = $configService;
    }

    /**
     * Retorna o QR Code para autenticação.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQrCode(): \Illuminate\Http\JsonResponse
    {
        $result = $this->configService->getQrCode();

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 500);
        }

        return response()->json($result, 200);
    }

    /**
     * Desconecta o WhatsApp da instância da Z-API.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function disconnect(): \Illuminate\Http\JsonResponse
    {
        $result = $this->configService->disconnect();

        if(isset($result['error'])) {
            return response()->json($result, 500);
        }

        return response()->json($result);
    }

    /**
     * Obtém o status da instância da Z-API.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInstanceStatus(): \Illuminate\Http\JsonResponse
    {
        $result = $this->configService->getInstanceStatus();

        if(isset($result['error'])) {
            return response()->json($result, 500);
        }

        return response()->json($result);
    }

    /**
     * Obtém os dados da instância da Z-API.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInstanceData()
    {
        $response = $this->configService->getInstanceData();

        if (isset($response['error'])) {
            return response()->json($response, 500);
        }

        return response()->json($response);
    }

    /**
     * Envia uma mensagem de texto.
     * 
     * @param Request $request 
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
            'messageId' => 'nullable|string',
        ]);

        $response = $this->messageService->sendMessage(
            $validated['phone'], 
            $validated['message'], 
            $validated['messageId'] ?? null
        );

        if (isset($response['error'])) {
            return response()->json($response, 500);
        }

        return response()->json($response);
    }
}
