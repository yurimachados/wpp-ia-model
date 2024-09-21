<?php

namespace App\Services\WhatsApp;

use App\Models\ZApiInstance;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Client\RequestException as HttpRequestException;

/**
 * Classe ZApiService
 *
 * Esta classe é responsável por interagir com a API do Z-API.
 *
 * @author Yuri Machado <ymachado1995@gmail.com>
 * @package App\Http\Controllers\WhatsApp
 ** @doc https://developer.z-api.io Documentação da Z-API
 */
class WhatsAppConfigService
{
    protected $instanceId;
    protected $instanceToken;
    protected $securityToken;
    protected $url;

    public function __construct()
    {
        $this->instanceId = env('Z_API_INSTANCE_ID');
        $this->instanceToken = env('Z_API_INSTANCE_TOKEN');
        $this->securityToken = env('Z_API_SECURITY_TOKEN');
        $this->url = "https://api.z-api.io/instances/{$this->instanceId}/token/{$this->instanceToken}/";
    }

    /**
     * getUrl
     *
     * Método responsável por retornar a URL da instancia da Z-API.
     * @return string $url URL da instancia da Z-API.
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * getDefaultHeaders
     *
     * Método responsável por retornar os headers padrões para as requisições.
     */
    public function getDefaultHeaders()
    {
        return [
            'accept' => 'application/json',
            'client-token' => $this->securityToken
        ];
    }

    /**
     * getQrCode
     *
     * Método responsável por gerar um QR Code para conectar whatsApp com instancia da Z-API.
     *
     * @return string $imageUrl URL da imagem do QR Code
     */
    public function getQrCode()
    {
        try {
            $url = $this->url . 'qr-code/image';
            $response = Http::withHeaders($this->getDefaultHeaders())
                ->get($url);

            $responseData = $response->json();

            if (isset($responseData['error'])) {
                $errorMessage = 'WACS001: ' . $responseData['error'];
                Log::channel('z-api')->error('Erro ao gerar QR Code: ' . $errorMessage);
                return ['error' => $errorMessage];
            }

            if (isset($responseData['connected']) && $responseData['connected'] === true) {
                Log::channel('z-api')->info('WhatsApp já conectado');
                return ['message' => 'WhatsApp já conectado', 'connected' => true];
            }

            if (!isset($responseData['value'])) {
                $errorMessage = 'Unexpected response format';
                Log::channel('z-api')->error('Erro ao gerar QR Code: ' . $errorMessage);
                return ['error' => $errorMessage];
            }

            $imageBase64 = substr($responseData['value'], strpos($responseData['value'], ",") + 1);
            $time = time();
            $filename = "z-api/qrCode/qr-code{$time}.png";
            
            Storage::disk('public')->makeDirectory('z-api/qrCode');
            Storage::disk('public')->put($filename, base64_decode($imageBase64));

            $imageUrl = asset('storage/' . $filename);

            Log::channel('z-api')->info('QR Code gerado com sucesso', ['url' => $imageUrl]);
            return ['qrCode' => $imageUrl, 'connected' => false];
        } catch (\Exception $e) {
            Log::channel('z-api')->error('Erro ao gerar QR Code: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * disconnect
     *
     * Método responsável por desconectar a o whatsApp da instancia da Z-API.
     *
     * @return bool $response['value'] Retorna true se a desconexão foi realizada com sucesso.
     */
    public function disconnect()
    {
        try {
            $url = $this->url . 'disconnect';
            $response = Http::withHeaders($this->getDefaultHeaders())
                ->get($url);

            $responseData = $response->json();
            
            if(isset($responseData['error'])){
                $errorMessage = $responseData['error'];
                Log::channel('z-api')->error('Erro ao desconectar: ' . $errorMessage);
                return ['error' => 'WACS002: ' . $errorMessage];
            }

            if (isset($responseData['value']) && $responseData['value'] == true) {
                Log::channel('z-api')->info('Desconectado com sucesso');
                return $response->json();
            }

            Log::channel('z-api')->error('WACS003: response exeption : ');
            return ['error' => "WACS003: response exeption"];
        } catch (\Exception $e) {
            Log::channel('z-api')->error('Erro ao desconectar: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * getInstanceStatus
     *
     * Método responsável por obter o status da instancia da Z-API.
     *
     * @return array $response Retorna o status da instancia.
     */
    public function getInstanceStatus()
    {
        try {
            $url = $this->url . 'status';
            $response = Http::withHeaders($this->getDefaultHeaders())
                ->get($url);

            if (isset($response->json()['connected']) && $response->json()['connected'] == true){
                return ['status' => 'connected'];
            } else {
                return ['status' => 'disconnected'];
            }
        } catch (HttpRequestException $e) {
            Log::channel('z-api')->error('HTTP request failed: ' . $e->getMessage());
            return ['error' => 'WACS004: HTTP request failed'];
        } catch (\Exception $e) {
            Log::channel('z-api')->error('Error getting instance status: ' . $e->getMessage());
            return ['error' => 'WACS005: error getting instance status' . $e->getMessage()];
        }
    }

    /**
     * getInstanceData
     *
     * Método responsável por obter os dados da instancia da Z-API.
     *
     * @return array $response Retorna os dados da instancia.
     */
    public function getInstanceData()
    {
        try {
            $url = $this->url . 'me';
            $response = Http::withHeaders($this->getDefaultHeaders())
                ->get($url);

            return $response->json();
        } catch (\Exception $e) {
            Log::channel('z-api')->error('Erro ao obter dados da instancia: ' . $e->getMessage());
            return ['error' => "WA006: " . $e->getMessage()];
        }
    }

    public function sendMessageUrl()
    {
        return $this->url . 'send-message';
    }
}