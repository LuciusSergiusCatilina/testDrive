<?php

namespace App;

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Main\ORM\Query\Query;

Loader::includeModule("highloadblock");

class Cars
{
    //id hlblock с машинами
    private static $hlBlockId = 1;
    public int $carId;

    public function __construct(int $carId)
    {
        $entity = Cars::getEntity();
        $dataClass = $entity->getDataClass();
        $existingCar = $dataClass::getList([
            'select' => ['ID'],
            'filter' => ['=ID' => $carId],
        ])->fetch();
        if (!$existingCar){
            throw new \Exception("Автомобиля с таким ID не существует");
        }
        $this->carId = $carId;
    }


    private static function getEntity()
    {
        $hlblock = HL\HighloadBlockTable::getById(self::$hlBlockId)->fetch();

        if (!$hlblock) {
            throw new \Exception("Highload-блок с ID " . self::$hlBlockId . " не найден.");
        }
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        return $entity;
    }

    public static function createCar($data)
    {
        try {
            if (!isset($data['UF_MODEL']) || empty(trim($data['UF_MODEL']))) {
                throw new \Exception("Обязательно заполните модель автомобиля!");
            }
            if ($data['UF_STATUS'] != 1 && $data['UF_STATUS'] != 2) {
                throw new \Exception("Недопустимый значение статуса! Допустимые значения: 1 (Доступен) и 2 (В ремонте)");
            }


            $entity = self::getEntity();
            $dataClass = $entity->getDataClass();

            $existingVIN = $dataClass::getList([
               'select' => ['ID'],
                'filter' => ['=UF_VIN' => $data['UF_VIN']]
            ])->fetch();

            if ($existingVIN){
                throw new \Exception("Такой VIN-код уже существует. Введите уникальный VIN-код");
            }

            $result = $dataClass::add([
                'UF_MODEL' => trim($data['UF_MODEL']),
                'UF_YEAR' => intval($data['UF_YEAR']),
                'UF_VIN' => trim($data['UF_VIN']),
                'UF_STATUS' => $data['UF_STATUS'],
                'UF_PRICE_PER_DAY' => intval($data['UF_PRICE_PER_DAY']),
            ]);

            if ($result->isSuccess()) {
                $id = $result->getId();
                return $id;
            }

        } catch (\Throwable $e) {
            throw $e;
        }

    }

    public static function createBunchCars($bunchData)
    {
        $application = \Bitrix\Main\Application::getInstance();
        $connection = $application->getConnection();
        $entity = self::getEntity();
        $dataClass = $entity->getDataClass();
        try {
            $connection->startTransaction();

            foreach ($bunchData as $index => $data) {
                if (!isset($data['UF_MODEL']) || empty(trim($data['UF_MODEL']))) {
                    throw new \Exception("Не заполнена модель машины №" . $index + 1);
                }
                if ($data['UF_STATUS'] != 1 && $data['UF_STATUS'] != 2) {
                    throw new \Exception("Недопустимый значение статуса машины №" . $index + 1 . "! Допустимые значения: 1 (Доступен) и 2 (В ремонте)");
                }

                $exictingVIN = $dataClass::getList([
                    'select' => ['ID'],
                    'filter' => ['=UF_VIN' => $data['UF_VIN']]
                ])->fetch();

                if ($exictingVIN){
                    throw new \Exception("VIN-код машины №" . $index +1 . " уже существует. Введите уникальный VIN-код.");
                }


                $result = $dataClass::add([
                    'UF_MODEL' => trim($data['UF_MODEL']),
                    'UF_YEAR' => intval($data['UF_YEAR']),
                    'UF_VIN' => trim($data['UF_VIN']),
                    'UF_STATUS' => $data['UF_STATUS'],
                    'UF_PRICE_PER_DAY' => intval($data['UF_PRICE_PER_DAY']),
                ]);
                if (!$result->isSuccess()) {
                    throw new \Exception("Ошибка при добавлении машины №" . $index + 1 . ": " . implode('| ', $result->getErrors()));
                }
            }

            $connection->commitTransaction();
        } catch (\Throwable $e) {
            $connection->rollbackTransaction();
            throw $e;
        }

    }

    public function updateCar($data)
    {
        $updateData = [];
        if (isset($data['UF_STATUS'])) {
            if ($data['UF_STATUS'] != 1 && $data['UF_STATUS'] != 2) {
                throw new \Exception("Недопустимый значение статуса! Допустимые значения: 1 (Доступен) и 2 (В ремонте)");

            }
            $updateData['UF_STATUS'] = $data['UF_STATUS'];
        }
        if (isset($data['UF_PRICE_PER_DAY'])){
            $updateData['UF_PRICE_PER_DAY'] = $data['UF_PRICE_PER_DAY'];
        }
        try {
            $entity = self::getEntity();
            $dataClass = $entity->getDataClass();
            $result = $dataClass::update($this->carId, $updateData);
            return $result->isSuccess();
        } catch (\Throwable $e) {
            throw $e;
        }

    }

    public function delete()
    {
        try {
            $entity = self::getEntity();
            $dataClass = $entity->getDataClass();
            $result = $dataClass::delete($this->carId);
            return $result->isSuccess();
        } catch (\Throwable $e) {
            throw $e;
        }


    }

    public static function getCars(?string $status = null)
    {
        try {
            $entity = self::getEntity();
            $dataClass = $entity->getDataClass();

            $params = [
                'select' => ['*'],
                'order' => ['ID']
            ];

            if ($status !== null) {
                $params['filter'] = ['=UF_STATUS' => $status];
            }

            $result = $dataClass::getList($params);

            return $result->fetchAll();
        } catch (\Throwable $e) {
            throw $e;
        }
    }

}