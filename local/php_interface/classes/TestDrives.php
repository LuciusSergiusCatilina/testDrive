<?php

namespace App;

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Main\ORM\Query\Query;

Loader::includeModule("highloadblock");

class TestDrives
{
    //id hlblock с бронью
    private static $hlBlockId = 2;

    private static function getEntity($hlbId)
    {
        $hlblock = HL\HighloadBlockTable::getById(self::$hlBlockId)->fetch();

        if (!$hlblock) {
            throw new \Exception("Highload-блок с ID " . self::$hlBlockId . " не найден.");
        }
        $entity = HL\HighloadBlockTable::compileEntity($hlbId);
        return $entity;
    }

    public static function createTestDrive($data)
    {
        try {

            $carsEntity = self::getEntity(1);
            $cars = $carsEntity->getDataClass();
            $testDrivesEntity = self::getEntity(self::$hlBlockId);
            $testDrives = $testDrivesEntity->getDataClass();
            $carInfo = $cars::getList([
                'select' => ['ID','UF_STATUS'],
                'filter' => ['=ID' => $data['UF_CAR']]
            ])->fetch();
            if (!$carInfo) {
                throw new \Exception("Автомобиля с таким ID не существует");
            }

            if ($carInfo['UF_STATUS'] !== '1') {
                throw new \Exception("Автомобиль в ремонте");
            }


            $carAvailability = TestDrives::checkAvailability($data['UF_CAR'], $data['UF_DATE_START'], $data['UF_DATE_END']);
            if (!$carAvailability) {
                throw new \Exception("Автомобиль забронирован в этот период");
            }
            $result = $testDrives::add([
                'UF_CAR' => trim($data['UF_CAR']),
                'UF_DATE_START' => $data['UF_DATE_START'],
                'UF_DATE_END' => $data['UF_DATE_END'],
                'UF_TOTAL_COST' => TestDrives::totalCost($data['UF_CAR'], $data['UF_DATE_START'], $data['UF_DATE_END'])
            ]);
            if ($result->isSuccess()) {
                $id = $result->getId();
                return $id;
            }

        } catch (\Throwable $e) {
            throw $e;
        }

    }

    private static function checkAvailability($carId, $dateStart, $dateEnd)
    {
        try {
            $testDrives = self::getEntity(self::$hlBlockId)->getDataClass();

            $unavailableCars = $testDrives::getList([
                'select' => ['ID'],
                'filter' => [
                    '=UF_CAR' => $carId,
                    'LOGIC' => 'AND',
                    ['<=UF_DATE_START' => $dateStart],
                    ['>=UF_DATE_END' => $dateEnd]
                ]
            ]);
            return empty($unavailableCars->fetchAll());
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    private static function totalCost($carId, $dateStart, $dateEnd)
    {
        try {
            $cars = self::getEntity(1)->getDataClass();
            $pricePerDay = $cars::getList([
                'select' => ['UF_PRICE_PER_DAY'],
                'filter' => ['=ID' => $carId]
            ])->fetch();
            $pricePerDay = $pricePerDay['UF_PRICE_PER_DAY'];
            $start = new \DateTime($dateStart);
            $end = new \DateTime($dateEnd);
            $daysCount = ($start->diff($end))->format('%a') + 1;
            $totalCost = $pricePerDay * $daysCount;
            return $totalCost;
        } catch (\Throwable $e) {
            throw $e;
        }


    }

    public static function getTestDrives()
    {
        try {
            $entity = self::getEntity(self::$hlBlockId);
            $dataClass = $entity->getDataClass();
            $carEntity = self::getEntity(1);
            $carDataClass = $carEntity->getDataClass();
            $result = $dataClass::getList([
                'select' => ['UF_CAR',
                    'UF_DATE_START',
                    'UF_DATE_END',
                    'UF_MODEL' => 'Cars.UF_MODEL'],
                'runtime' => [
                    new Entity\ReferenceField(
                        'Cars',
                        $carDataClass,
                        [
                            '=this.UF_CAR' => 'ref.ID'
                        ],
                        [
                            'join_type' => 'LEFT'
                        ]
                    )
                ]
            ]);
            return $result->fetchAll();
        } catch (\Throwable $e) {
            throw $e;
        }

    }


}