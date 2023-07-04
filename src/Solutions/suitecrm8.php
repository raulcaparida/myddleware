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
            'password' => $this->paramConnexion['password']
        ];

        $this->paramConnexion['url'] = str_replace('index.php', '', $this->paramConnexion['url']);
        $this->paramConnexion['url'] .= '/Api/access_token';  // SuiteCRM v8 access_token endpoint

        $result = $this->call('POST', $login_parameters, $this->paramConnexion['url']);

        if ($result != false) {
            if (empty($result->access_token)) {
                throw new \Exception("Authentication failed");
            }

            $this->session = $result->access_token;
            $this->connexion_valide = true;
        } else {
            throw new \Exception('Please check url');
        }
    } catch (\Exception $e) {
        $error = 'Error : ' . $e->getMessage() . ' ' . $e->getFile() . ' Line : ( ' . $e->getLine() . ' )';
        $this->logger->error($error);

        return ['error' => $error];
    }
}

}
class suitecrm8 extends suitecrm8core
{
}
