<?php
/*********************************************************************************
 * This file is part of Myddleware.
 * @package Myddleware
 * @copyright Copyright (C) 2013 - 2015  Stéphane Faure - CRMconsult EURL
 * @copyright Copyright (C) 2015 - 2017  Stéphane Faure - Myddleware ltd - contact@myddleware.com
 * @copyright Copyright (C) 2017 - 2023  Stéphane Faure - CRMconsult EURL
 * @link http://www.myddleware.com
 *
 * This file is part of Myddleware.
 *
 * Myddleware is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Myddleware is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Myddleware.  If not, see <http://www.gnu.org/licenses/>.
 *********************************************************************************/

namespace App\Solutions;

use Datetime;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class hubspotcore extends solution
{
    protected $hubspot;
    protected $apiCallLimit = 100;

    protected array $FieldsDuplicate = array(
        'contacts' => array('email')
    );
	
    // Requiered fields for each modules
    protected array $required_fields = array(
        'default' => ['lastmodifieddate'],
        'companies' => ['hs_lastmodifieddate'],
        'deals' => ['hs_lastmodifieddate'],
    );

    public function getFieldsLogin(): array
    {
        return [
            [
                'name' => 'accesstoken',
                'type' => PasswordType::class,
                'label' => 'solution.fields.accesstoken',
            ],
        ];
    }

    // Connect to Hubspot
    public function login($paramConnexion)
    {
        parent::login($paramConnexion);
        try {
            $this->hubspot = \HubSpot\Factory::createWithAccessToken($this->paramConnexion['accesstoken']);
            // Call the standard API. OK if no exception.
            $response = $this->hubspot->crm()->contacts()->basicApi()->getPage();
    echo 'A'.chr(10);
            $properties = $this->hubspot->crm()->properties()->coreApi()->getAll('associations')->getResults();
            print_r($properties);
throw new \Exception('test');
            $this->connexion_valide = true;
        } catch (\Exception $e) {
            $error = $e->getMessage().' '.$e->getFile().' Line : ( '.$e->getLine().' )';
            $this->logger->error($error);
            return ['error' => $error];
        }
    }


    public function get_modules($type = 'source'): array
    {
        $modules = array(
            'companies' => 'Companies',
            'contacts' => 'Contacts',
            'deals' => 'Deals',
            'products' => 'Products',
            'line_items' => 'Line items',
        );


        
        return $modules;
    }

    // Renvoie les champs du module passé en paramètre
    public function get_module_fields($module, $type = 'source', $param = null): array
    {
        parent::get_module_fields($module, $type);
        try {
            $properties = $this->hubspot->crm()->properties()->coreApi()->getAll($module)->getResults();
            if (!empty($properties)){
                foreach($properties as $property) {
                    // List value
                    $options = $property->getOptions();
                    // Don't add records list fields
                    if (
                            $property->getFieldType() == 'select'
                        AND empty($options)
                    ) {
                        continue;
                    }
                    // Don't add the hs fields
                    $name = $property->getName();
                    if (substr($name,0,3) == 'hs_'){
                        continue;
                    }
                    $this->moduleFields[$name] = array(
                                'label' => $property->getLabel(),
                                'type' => 'varchar(255)',
                                'type_bdd' => 'varchar(255)',
                                'required' => false,
                                'relate' => (empty($property->getReferencedObjectType()) ? false : true),
                            );
                    // Add =value list
                    if(!empty($options)) {
                        foreach($options as $option) {
                            $this->moduleFields[$property->getName()]['options'][$option->getValue()] = $option->getLabel();
                        }
                    }
                }
            }

            return $this->moduleFields;
        } catch (\Exception $e) {
            $error = $e->getMessage().' '.$e->getFile().' Line : ( '.$e->getLine().' )';
            $this->logger->error($error);
            return ['error' => $error];
        }
    }

    public function read($param)
    {
        try {
            // Initialize result and parameters
            $result = array();
            $nbRecords = 0;
            $apiCallLimit = ($param['limit'] < $this->apiCallLimit ? $param['limit'] : $this->apiCallLimit);

            $after = 0;
            $filter = $this->getFilterObject($param['module']);
            $dateRef = $this->dateTimeFromMyddleware($param['date_ref']);
            $dateRefField = $this->getRefFieldName($param);

            // Set the filter 
            if (!empty($param['query'])) {
                foreach($param['query'] as $key=>$value) {
                    $filter->setOperator('EQ')
                            ->setPropertyName($key)
                            ->setValue($value);
                }
            } elseif (!empty($param['date_ref'])) {
                $filter->setOperator('GT')
                        ->setPropertyName($dateRefField)
                        ->setValue($dateRef);
            }
            $filterGroup = $this->getFilterGroupObject($param['module']);
            $filterGroup->setFilters([$filter]);
			$searchRequest = $this->getSearchRequestObject($param['module']);
            $searchRequest->setFilterGroups([$filterGroup]);

            // Always sort by last modified date ascending
            $sorts = array(
                        array(
                            'propertyName' => $dateRefField,
                            'direction' => 'ASCENDING',
                         ),
                    );
            $searchRequest->setSorts($sorts);
            // Set the limit and the offset
            $searchRequest->setLimit($apiCallLimit);

            // Set the fields requested
            $searchRequest->setProperties($param['fields']);
            do {
                // Manage offset
                $searchRequest->setAfter($after);

                // Search records from Hubspot
                if (!empty($param['query']['id'])) {
                    $records[0] = $this->getRecordById($param);
                } else {
                    $recordList = $this->getRecords($param, $searchRequest);
                    $records = $recordList->getResults();        
                }
                // Format results
                if (!empty($records)) {
                    foreach($records as $record) {
                        // Stop the process if limit has been reached
                        if ($param['limit'] <= $nbRecords) {
                            break;
                        }
                        $recordValues = $record->getProperties();
                        $recordId = $record->getId();
                        $result[$recordId]['id'] = $recordId;
                        // Fill every rule fields
                        foreach($param['fields'] as $field) {
                            $result[$recordId][$field] = $recordValues[$field] ?? null;
                        }
                        $nbRecords++;
                    }
                }
                // No pagination for search by id (only 1 result)
                if (!empty($param['query']['id'])) {
                    break;
                }
                if (!is_null($recordList->getPaging())) {
                    $after = $recordList->getPaging()->getNext()->getAfter();
                }
            // Stop if no result or if the rule limit has been reached
            } while (
                    is_object($recordList) 
                AND $recordList->getPaging()
                AND $param['limit'] > $nbRecords
            );
        } catch (\Exception $e) {
            throw new \Exception('Error : '.$e->getMessage().' '.__CLASS__.' Line : ( '.$e->getLine().' )');
        }
        return $result;
    }

    // Create a record into Hubspot
    protected function create($param, $record, $idDoc = null): ?int
    {    
		try {
            switch ($param['module']) {
                case 'companies':
                    $SimplePublicObjectInputForCreate = new \HubSpot\Client\Crm\Companies\Model\SimplePublicObjectInput();
                    $SimplePublicObjectInputForCreate->setProperties($record);
                    $apiResponse = $this->hubspot->crm()->companies()->basicApi()->create($SimplePublicObjectInputForCreate);
                    break;
                case 'contacts':
                    $SimplePublicObjectInputForCreate = new \HubSpot\Client\Crm\Contacts\Model\SimplePublicObjectInput();
                    $SimplePublicObjectInputForCreate->setProperties($record);
                    $apiResponse = $this->hubspot->crm()->contacts()->basicApi()->create($SimplePublicObjectInputForCreate);
                    break;
                case 'deals':
                    $SimplePublicObjectInputForCreate = new \HubSpot\Client\Crm\Deals\Model\SimplePublicObjectInput();
                    $SimplePublicObjectInputForCreate->setProperties($record);
                    $apiResponse = $this->hubspot->crm()->deals()->basicApi()->create($SimplePublicObjectInputForCreate);
                    break;
                case 'products':
                    $SimplePublicObjectInputForCreate = new \HubSpot\Client\Crm\Products\Model\SimplePublicObjectInput();
                    $SimplePublicObjectInputForCreate->setProperties($record);
                    $apiResponse = $this->hubspot->crm()->products()->basicApi()->create($SimplePublicObjectInputForCreate);
                    break;
                case 'line_items':
                    $SimplePublicObjectInputForCreate = new \HubSpot\Client\Crm\LineItems\Model\SimplePublicObjectInput();
                    $SimplePublicObjectInputForCreate->setProperties($record);
                    $apiResponse = $this->hubspot->crm()->line_items()->basicApi()->create($SimplePublicObjectInputForCreate);
                    break;
                default: 
                    throw new \Exception('No ceate function found for the module '.$param['module']);
            }
		} catch (\Exception $e) {
            throw new \Exception('Exception when calling create function: '.$e->getMessage());
        }
		return $apiResponse->getId();
    }

    // Update a record into Hubspot
    protected function update($param, $record, $idDoc = null): ?int
    {    
		try {
            // Get the target ID and remove it from the record data 
            $recordId = $record['target_id'];
            unset($record['target_id']);
print_r($record);
            switch ($param['module']) {
                case 'companies':
                    $SimplePublicObjectInputForUpdate = new \HubSpot\Client\Crm\Companies\Model\SimplePublicObjectInput();
                    $SimplePublicObjectInputForUpdate->setProperties($record);
                    $apiResponse = $this->hubspot->crm()->companies()->basicApi()->update($recordId, $SimplePublicObjectInputForUpdate);
                    break;
                case 'contacts':
                    $SimplePublicObjectInputForUpdate = new \HubSpot\Client\Crm\Contacts\Model\SimplePublicObjectInput();
                    $SimplePublicObjectInputForUpdate->setProperties($record);
                    $apiResponse = $this->hubspot->crm()->contacts()->basicApi()->update($recordId, $SimplePublicObjectInputForUpdate);
                    break;
                case 'deals':
                    $SimplePublicObjectInputForUpdate = new \HubSpot\Client\Crm\Deals\Model\SimplePublicObjectInput();
                    $SimplePublicObjectInputForUpdate->setProperties($record);
                    $apiResponse = $this->hubspot->crm()->deals()->basicApi()->update($recordId, $SimplePublicObjectInputForUpdate);
                    break;
                case 'products':
                    $SimplePublicObjectInputForUpdate = new \HubSpot\Client\Crm\Products\Model\SimplePublicObjectInput();
                    $SimplePublicObjectInputForUpdate->setProperties($record);
                    $apiResponse = $this->hubspot->crm()->products()->basicApi()->update($recordId, $SimplePublicObjectInputForUpdate);
                    break;
                case 'line_items':
                    $SimplePublicObjectInputForUpdate = new \HubSpot\Client\Crm\LineItems\Model\SimplePublicObjectInput();
                    $SimplePublicObjectInputForUpdate->setProperties($record);
                    $apiResponse = $this->hubspot->crm()->line_items()->basicApi()->update($recordId, $SimplePublicObjectInputForUpdate);
                    break;          
                default: 
                    throw new \Exception('No update function found for the module '.$param['module']);
            }
		} catch (\Exception $e) {
            throw new \Exception('Exception when calling update function: '.$e->getMessage());
        }
print_r($apiResponse);
		return $apiResponse->getId();
    }
	
	// Get Hubspot filter object depending on the module
	protected function getFilterObject($module) {
		switch ($module) {
			case 'contacts':
				return new \HubSpot\Client\Crm\Contacts\Model\Filter();
			case 'companies':
				return new \HubSpot\Client\Crm\Companies\Model\Filter();
			case 'deals':
				return new \HubSpot\Client\Crm\Deals\Model\Filter();
            case 'products':
                return new \HubSpot\Client\Crm\Products\Model\Filter();
            case 'line_items':
                return new \HubSpot\Client\Crm\LineItems\Model\Filter();
			default: 
				throw new \Exception('No filter found for the module '.$module);
		}
	}
	
	// Get Hubspot FilterGroup object depending on the module
	protected function getFilterGroupObject($module) {
		switch ($module) {
			case 'contacts':
				return new \HubSpot\Client\Crm\Contacts\Model\FilterGroup();
			case 'companies':
				return new \HubSpot\Client\Crm\Companies\Model\FilterGroup();
			case 'deals':
				return new \HubSpot\Client\Crm\Deals\Model\FilterGroup();
            case 'products':
                return new \HubSpot\Client\Crm\Products\Model\FilterGroup();
            case 'line_items':
                return new \HubSpot\Client\Crm\LineItems\Model\FilterGroup();
			default: 
				throw new \Exception('No filter group found for the module '.$module);
		}
	}
	
	// Get Hubspot filter object depending on the module
	protected function getSearchRequestObject($module) {
		switch ($module) {
			case 'contacts':
				return new \HubSpot\Client\Crm\Contacts\Model\PublicObjectSearchRequest();
			case 'companies':
				return new \HubSpot\Client\Crm\Companies\Model\PublicObjectSearchRequest();
			case 'deals':
				return new \HubSpot\Client\Crm\Deals\Model\PublicObjectSearchRequest();
            case 'products':
                return new \HubSpot\Client\Crm\Products\Model\PublicObjectSearchRequest();
            case 'line_items':
                return new \HubSpot\Client\Crm\LineItems\Model\PublicObjectSearchRequest();
                                 
			default: 
				throw new \Exception('Nosearch request object found for the module '.$module);
		}
	}

	protected function getRecordById($param) {
		switch ($param['module']) {
			case 'contacts':
				return $this->hubspot->crm()->Contacts()->basicApi()->getById($param['query']['id']);
			case 'companies':
				return $this->hubspot->crm()->Companies()->basicApi()->getById($param['query']['id']);
			case 'deals':
				return $this->hubspot->crm()->Deals()->basicApi()->getById($param['query']['id']);
            case 'products':
                return $this->hubspot->crm()->Products()->basicApi()->getById($param['query']['id']);
            case 'line_items':
                return $this->hubspot->crm()->LineItems()->basicApi()->getById($param['query']['id']);
			default: 
				throw new \Exception('No getRecordById function found for the module '.$param['module']);
		}
	}

	protected function getRecords($param, $searchRequest) {
		switch ($param['module']) {
			case 'contacts':
				return $this->hubspot->crm()->Contacts()->searchApi()->doSearch($searchRequest);
			case 'companies':
				return $this->hubspot->crm()->Companies()->searchApi()->doSearch($searchRequest);
			case 'deals':
				return $this->hubspot->crm()->Deals()->searchApi()->doSearch($searchRequest);
            case 'products':
                return $this->hubspot->crm()->Products()->searchApi()->doSearch($searchRequest);
            case 'line_items':
                return $this->hubspot->crm()->LineItems()->searchApi()->doSearch($searchRequest);
			default: 
				throw new \Exception('No getRecordById function found for the module '.$param['module']);
		}
	}
	

     /**
     * @throws \Exception
     */
    public function getRefFieldName($param): string
    {
        if (in_array($param['module'], array('companies','deals'))) {
            return 'hs_lastmodifieddate'; 
        }
        return 'lastmodifieddate';
    }

    // Function de conversion de datetime format solution à un datetime format Myddleware
    protected function dateTimeToMyddleware($dateTime)
    {
        $dto = new \DateTime($dateTime);
        // Return date with milliseconds
        return $dto->format('Y-m-d H:i:s.v');
    }

    // Function de conversion de datetime format Myddleware à un datetime format solution
    protected function dateTimeFromMyddleware($dateTime)
    {
        $dto = DateTime::createFromFormat('Y-m-d H:i:s.v', $dateTime);
        // If the user set a reference date manually then there is no milliseconds
        if (empty($dto)) {
            $dto = DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);
        }
        return $dto->format('Uv');
    }

}

class hubspot extends hubspotcore
{
}
