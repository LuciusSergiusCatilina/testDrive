<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use App;
class showTestDrivesComponent extends CBitrixComponent
{
    /**
     * Подготавливаем входные параметры
     *
     * @param  array $arParams
     *
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {

    }

    public function executeComponent()
    {
        // Кешируем результат, чтобы не делать постоянные запросы к базе
        if ($this->startResultCache())
        {
            $this->initResult();
            $this->includeComponentTemplate();
        }
    }
    private function initResult(): void
    {

        $testDrivesInfo = App\TestDrives::getTestDrives();
        foreach ($testDrivesInfo as $index => $testDrive){
            $this->arResult[$index]['UF_MODEL'] = $testDrive['UF_MODEL'];
            $this->arResult[$index]['UF_DATE_START'] = $testDrive['UF_DATE_START']->format('d.m.Y H:i');
            $this->arResult[$index]['UF_DATE_END'] = $testDrive['UF_DATE_END']->format('d.m.Y H:i');
        }
    }
}
