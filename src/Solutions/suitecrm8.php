<?php
/*********************************************************************************
 * This file is part of Myddleware.

 * @package Myddleware
 * @copyright Copyright (C) 2013 - 2015  Stéphane Faure - CRMconsult EURL
 * @copyright Copyright (C) 2015 - 2016  Stéphane Faure - Myddleware ltd - contact@myddleware.com
 * @link http://www.myddleware.com

 This file is part of Myddleware.

 Myddleware is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 Myddleware is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Myddleware.  If not, see <http://www.gnu.org/licenses/>.
*********************************************************************************/

namespace App\Solutions;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class suitecrm8core extends solution
{

    public function getFieldsLogin(): array
    {
        return [
            [
                'name' => 'login',
                'type' => TextType::class,
                'label' => 'solution.fields.login',
            ],
            [
                'name' => 'password',
                'type' => PasswordType::class,
                'label' => 'solution.fields.password',
            ],
            [
                'name' => 'url',
                'type' => TextType::class,
                'label' => 'solution.fields.url',
            ],
            [
                'name' => 'client_id',
                'type' => TextType::class,
                'label' => 'solution.fields.client_id',
            ],
            [
                'name' => 'client_secret',
                'type' => TextType::class,
                'label' => 'solution.fields.client_secret',
            ],
        ];
    }

    public function login($paramConnexion)
{
    parent::login($paramConnexion);

    try {
        $login_parameters = [
            'grant_type' => 'password',
            'client_id' => $this->paramConnexion['client_id'],
            'client_secret' => $this->paramConnexion['client_secret'],
            'username' => $this->paramConnexion['login'],
            'password' => $this->paramConnexion['password'],
            'url' => $this->paramConnexion['url'],
        ];

        $this->paramConnexion['url'] = str_replace('index.php', '', $this->paramConnexion['url']);
        $this->paramConnexion['url'] .= '/Api/access_token';  // SuiteCRM v8 access_token endpoint

        // $result = $this->call('POST', $login_parameters, $this->paramConnexion['url']);
        $result = $this->callDocumentation('GET', $login_parameters, $this->paramConnexion['url']);

        if (strlen($result) > 3) {
            throw new \Exception($result);
        }

        if ($result != false) {
            if (empty($result->access_token)) {
                throw new \Exception("Authentication failed");
            }

            $this->session = $result->access_token;
            $this->connexion_valide = true;
        } else {
            throw new \Exception($result);
        }
    } catch (\Exception $e) {
        $error = 'Error : ' . $e->getMessage() . ' ' . $e->getFile() . ' Line : ( ' . $e->getLine() . ' )';
        $this->logger->error($error);

        return ['error' => $error];
    }
}

protected function call($method, $parameters)
{
    try {
        ob_start();
        $curl_request = curl_init();
        curl_setopt($curl_request, CURLOPT_URL, $this->paramConnexion['url']);
        curl_setopt($curl_request, CURLOPT_POST, 1);
        curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($curl_request, CURLOPT_HEADER, 1);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);

        curl_setopt($curl_request, CURLOPT_POSTFIELDS, $parameters); // Use the array directly
        $result = curl_exec($curl_request);

        // if $result has a length of more than 3, throw an error
        if (strlen($result) > 3) {
            throw new \Exception($result);
        }
        
        curl_close($curl_request);
        if (empty($result)) {
            return false;
        }
        $resultExploded = explode("\r\n\r\n", $result, 2);
        $response = json_decode($resultExploded[1]);
        ob_end_flush();

        return $response;
    } catch (\Exception $e) {
        // return false;
        return new \Exception($result);
    }
}

protected function callDocumentation($method, $parameters)
{
    try {
        $documentationUrl = 'https://suitecrm.myddleware.cloud/Api/V8/meta/swagger.json';
        ob_start();
        $curl_request = curl_init();
        
        // set the URL of the request
        curl_setopt($curl_request, CURLOPT_URL, $documentationUrl);

        // Check if the method is GET
        if($method == 'GET'){
            // append the parameters to the URL for a GET request
            curl_setopt($curl_request, CURLOPT_URL, $documentationUrl . '?' . http_build_query($parameters));
        }
        else{
            // set request type to POST and add POST fields for a POST request
            curl_setopt($curl_request, CURLOPT_POST, 1);
            curl_setopt($curl_request, CURLOPT_POSTFIELDS, $parameters);
        }

        curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($curl_request, CURLOPT_HEADER, 1);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);

        $result = curl_exec($curl_request);

        if (strlen($result) > 3) {
            throw new \Exception($result);
        }
        
        curl_close($curl_request);
        if (empty($result)) {
            return false;
        }

        $resultExploded = explode("\r\n\r\n", $result, 2);
        $response = json_decode($resultExploded[1]);
        ob_end_flush();

        return $response;
    } catch (\Exception $e) {
        return new \Exception($result);
    }
}



}
class suitecrm8 extends suitecrm8core
{
}
