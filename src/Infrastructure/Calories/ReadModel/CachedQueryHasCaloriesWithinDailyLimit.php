<?php

declare(strict_types=1);

namespace Caloriary\Infrastructure\Calories\ReadModel;

use Caloriary\Calories\CaloricRecord;
use Caloriary\Calories\ReadModel\HasCaloriesWithinDailyLimit;
use Doctrine\DBAL\Connection;

final class CachedQueryHasCaloriesWithinDailyLimit implements HasCaloriesWithinDailyLimit
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var mixed[]
     */
    private $cache = [];


    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }


    public function __invoke(CaloricRecord $caloricRecord): bool
    {
        $user = $caloricRecord->ownedBy();
        $limit = $user->dailyLimit();
        $email = $user->emailAddress()->toString();
        $date = $caloricRecord->ateAt()->format('Y-m-d');

        if (!isset($this->cache[$email][$date])) {
            if ($limit->isLimited() === false) {
                $isWithinLimit = true;
            } else {
                $calories = $this->getCaloriesForUserAtDate($email, $date);
                $isWithinLimit = $calories < $limit->toInteger();
            }

            $this->cache[$email][$date] = $isWithinLimit;
        }

        return $this->cache[$email][$date];
    }


    private function getCaloriesForUserAtDate(string $email, string $date): int
    {
        $query = <<<SQL
SELECT SUM(calories) 
FROM caloric_record 
WHERE owner_id = :email 
  AND DATE(ate_at) = :date 
GROUP BY owner_id
SQL;
        $statement = $this->connection->executeQuery($query, [
            'email' => $email,
            'date' => $date,
        ]);

        return (int) $statement->fetchColumn();
    }
}
