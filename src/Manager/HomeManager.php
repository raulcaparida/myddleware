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

namespace App\Manager;

use App\Entity\User;
use App\Repository\DocumentRepository;
use App\Repository\JobRepository;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

class HomeManager
{
    protected Connection $connection;
    protected LoggerInterface $logger;

    const historicDays = 7;
    const nbHistoricJobs = 5;
    protected string $historicDateFormat = 'M-d';
    private JobRepository $jobRepository;
    private DocumentRepository $documentRepository;

    public function __construct(
        LoggerInterface $logger,
        Connection $connection,
        JobRepository $jobRepository,
        DocumentRepository $documentRepository
    ) {
        $this->logger = $logger;
        $this->connection = $connection;
        $this->jobRepository = $jobRepository;
        $this->documentRepository = $documentRepository;
    }

    public function countTransferHisto(User $user = null): array
    {
        try {
            $historic = [];
            // Start date
            $startDateOri = date('Y-m-d', strtotime('-'.self::historicDays.' days'));
            $startDate = date('Y-m-d', strtotime('-'.self::historicDays.' days'));
            // End date
            $endDate = date('Y-m-d');
            // Init array
            while (strtotime($startDate) < strtotime($endDate)) {
                $startDateFormat = date($this->historicDateFormat, strtotime('+1 day', strtotime($startDate)));
                $startDate = date('Y-m-d', strtotime('+1 day', strtotime($startDate)));
                $historic[$startDate] = ['date' => $startDateFormat, 'open' => 0, 'error' => 0, 'cancel' => 0, 'close' => 0];
            }

            // Stop using doctrine for performance reasons
            // Select the number of transfers per day
            // $result = $this->documentRepository->countTransferHisto($user);
			$sqlParams = "	SELECT
								DATE_FORMAT(d.date_modified, '%Y-%m-%d') AS date,
								d.global_status,
								COUNT(d.id) AS nb
							FROM document d
							WHERE
									d.deleted = 0
								AND d.date_modified >= :date_modified
							group By date, d.global_status
						";
            $stmt = $this->connection->prepare($sqlParams);
            $stmt->bindValue(':date_modified', $startDateOri);
            $result = $stmt->executeQuery();
            $result = $result->fetchAllAssociative();
            if (!empty($result)) {
                foreach ($result as $row) {
                    $historic[$row['date']][strtolower($row['global_status'])] = $row['nb'];
                }
            }
        } catch (\Exception $e) {
            $this->logger->error('Error : '.$e->getMessage().' '.$e->getFile().' Line : ( '.$e->getLine().' )');
        }

        return $historic;
    }
	
	public function countTransferRule(): array
    {
        try {
			$countTransferRule = array();
			$startDate = date('Y-m-d', strtotime('-'.self::historicDays.' days'));
            $sqlParams = "	SELECT 
								data.nb,
								rule.name
							FROM (
								SELECT 
									COUNT(document.id) as nb, 
									document.rule_id
								FROM document
								WHERE 
									document.global_status = :close
								AND document.date_modified > :date_modified
								GROUP BY document.rule_id
							) data
								INNER JOIN rule    
									on data.rule_id = rule.id";
            $stmt = $this->connection->prepare($sqlParams);
			$stmt->bindValue(':date_modified', $startDate);
			$stmt->bindValue(':close', 'Close');
            $result = $stmt->executeQuery();
            $values = $result->fetchAllAssociative();

            if (count($values)) {
				$countTransferRule[] = ['rule', 'value'];
                foreach ($values as $value) {
                    $countTransferRule[] = [$value['name'], (int) $value['nb']];
                }
            } 
        } catch (\Exception $e) {
            $this->logger->error('Error : '.$e->getMessage().' '.$e->getFile().' Line : ( '.$e->getLine().' )');
        }

        return $countTransferRule;
    }
}
