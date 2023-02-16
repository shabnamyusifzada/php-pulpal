<?php

namespace ShabnamYusifzada\Pulpal\Utilities;

/**
 * Helper for curl request
 */

trait Curl
{
    /**
     * Send request to the proper url and the necessary method
     *
     * @param string $url
     * @param string $method
     * @param string $params Serialized params
     * @param array $headers
     * @return array = [
     * 'status' => 'success',
     * 'data' => [],
     * 'code' => 200,
     * 'url' => '',
     * 'message' => 'OK'
     * ]
     */
    private function sendRequest($url, $method, $params, array $headers)
    {
        $response = array('status' => 'error', 'data' => []);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $data = curl_exec($ch);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        $code = $info['http_code'];
        $response['code'] = $code;
        $response['url'] = $url;
        if ($error) {
            $response['message'] = $error;
        } else {
            if ($code === 200) {
                $response['status'] = 'success';
                $response['data'] = json_decode($data, true);
            }
            $response['message'] = $this->getResponseDefinitionByStatusCode($code);
        }

        return $response;
    }

    /**
     * Get request response status definition by its code
     *
     * @param int $code
     * @return string
     */
    private function getResponseDefinitionByStatusCode($code)
    {
        $responseStatuses = [
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'System Error'
        ];

        return $responseStatuses[$code];
    }
}