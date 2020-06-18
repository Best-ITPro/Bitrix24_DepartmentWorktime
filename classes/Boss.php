<?php

// Boss Class - класс руководителя для работы с отчётами по рабочему времени
// Created by Best IT Pro, 2020


class Boss
{

    public $ID;
    public $ACTIVE;
    public $LAST_NAME;
    public $NAME;
    public $SECOND_NAME;
    public $EMAIL;
    public $PERSONAL_GENDER;
    public $PERSONAL_PHOTO;
    public $PERSONAL_BIRTHDAY;
    public $WORK_PHONE;
    public $WORK_POSITION;
    public $GROUP_ID = [];
    public $UF_DEPARTMENT;
    public $UF_DEPARTMENT_NAME;
    public $BOSS_RIGHTS; // Права боса, входит ли в группу BOSS_GROUPS
    public $IsAdmin;

    // Конструктор
    function __construct($ID, $ACTIVE, $LAST_NAME, $NAME, $SECOND_NAME, $EMAIL, $PERSONAL_GENDER, $PERSONAL_PHOTO, $PERSONAL_BIRTHDAY, $WORK_PHONE, $WORK_POSITION, $GROUP_ID, $UF_DEPARTMENT, $UF_DEPARTMENT_NAME, $IsAdmin)
    {
        $this->ID = $ID;
        $this->ACTIVE = $ACTIVE;
        $this->LAST_NAME = $LAST_NAME;
        $this->NAME = $NAME;
        $this->SECOND_NAME = $SECOND_NAME;
        $this->EMAIL = $EMAIL;
        $this->PERSONAL_GENDER = $PERSONAL_GENDER;
        $this->PERSONAL_PHOTO = $PERSONAL_PHOTO;
        $this->PERSONAL_BIRTHDAY = $PERSONAL_BIRTHDAY;
        $this->WORK_PHONE = $WORK_PHONE;
        $this->WORK_POSITION = $WORK_POSITION;
        $this->GROUP_ID = $GROUP_ID;
        $this->UF_DEPARTMENT = $UF_DEPARTMENT;
        $this->UF_DEPARTMENT_NAME = $UF_DEPARTMENT_NAME;
        $this->IsAdmin = $IsAdmin;
        $this->IsAdmin == 1 ? $this->IsAdmin = 'Y' : $this->IsAdmin = 'N';
    }

    function CheckRights()
    {

        $this->BOSS_RIGHTS = 'N';
        // В случае использования одной группы - const BOSS_GROUP
        in_array(BOSS_GROUP, $this->GROUP_ID) ? $this->BOSS_RIGHTS = 'Y' : $this->BOSS_RIGHTS = 'N';

        // Права босса = Права админа (для простоты отладки)
        if ($this->IsAdmin == 'Y')
        {
            $this->BOSS_RIGHTS = 'Y';
        }
    }

    // Получить инфо
    function getInfo()
    {
        echo "ID : {$this->ID} <br>";
        echo "Активность: {$this->ACTIVE} <br>";
        echo "Фамилия: {$this->LAST_NAME} <br>";
        echo "Имя: {$this->NAME} <br>";
        echo "Права босса: {$this->IsAdmin} <br>";
        echo "Права админа: {$this->IsAdmin} <br>";
        echo "Пол:  {$this->PERSONAL_GENDER} <br>";
        echo "Город:  {$this->PERSONAL_CITY} <br>";
        echo "Должность: {$this->WORK_POSITION} <br>";
        echo "Телефон: {$this->WORK_PHONE} <br>";
        echo "E-mail: {$this->EMAIL} <br>";
        echo "Название подразделения:  {$this->UF_DEPARTMENT_NAME} <br><br>";
        echo "Группы: ";
        best_bebug($this->GROUP_ID);
    }

    // Подпись под отчётом
    function signature()
    {
        echo "<br>";
        echo "Отчёт подготовил: " . $this->WORK_POSITION . " <b>" . $this -> LAST_NAME. " " . $this->NAME . " " . $this->SECOND_NAME . "</b><br>";
        echo "Отчёт подготовлен:<b>" . date('d.m.Y H:i:s') . "</b><br>";
        echo "<br>";
    }


}