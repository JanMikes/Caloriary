<?php

declare(strict_types=1);

namespace Caloriary\Infrastructure\Calories\Repository;

use Caloriary\Calories\CaloricRecord;
use Caloriary\Calories\Exception\CaloricRecordNotFound;
use Caloriary\Calories\Repository\CaloricRecords;
use Caloriary\Calories\Value\CaloricRecordId;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Ramsey\Uuid\Uuid;

final class DoctrineCaloricRecords implements CaloricRecords
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var ObjectRepository
     */
    private $repository;


    public function __construct(ObjectManager $manager, ObjectRepository $repository)
    {
        $this->manager = $manager;
        $this->repository = $repository;
    }



    public function nextIdentity(): CaloricRecordId
    {
        return CaloricRecordId::fromString(Uuid::uuid4()->toString());
    }


    public function get(CaloricRecordId $recordId): CaloricRecord
    {
        $record = $this->repository->find($recordId);

        if ($record instanceof CaloricRecord) {
            return $record;
        }

        throw new CaloricRecordNotFound();
    }


    public function add(CaloricRecord $record): void
    {
        $this->manager->persist($record);
        $this->manager->flush();
    }


    public function remove(CaloricRecord $caloricRecord): void
    {
        $this->manager->remove($caloricRecord);
        $this->manager->flush();
    }
}
