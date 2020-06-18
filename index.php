<?php

// Created by Best IT Pro, 2020

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Проверяем рабочий график своего подразделения");

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

// --------- Подключение классов --------
require_once 'classes/Boss.php';
require_once 'classes/BestTimanUser.php';   // Класс для работы с графиком работ

// --------- Подключение констант --------
require_once 'include/constants.php';

global $USER;
global $DB;

$ID = $USER->GetID();

$IsAdmin = $USER->IsAdmin();
// получим массив групп пользователя
$GROUP_ID =  CUser::GetUserGroup($ID);

// Получаем информацию о пользователе
$Userinfo = CUser::GetByID($ID);
$arUser = $Userinfo->getNext();

$ACTIVE = $arUser['ACTIVE'];
$LAST_NAME = $arUser['LAST_NAME'];
$NAME = $arUser['NAME'];
$SECOND_NAME = $arUser['SECOND_NAME'];
$EMAIL = $arUser['EMAIL'];
$PERSONAL_PHOTO = $arUser['PERSONAL_PHOTO'];
$PERSONAL_GENDER = $arUser['PERSONAL_GENDER'];
$PERSONAL_BIRTHDAY = $arUser['PERSONAL_BIRTHDAY'];
$WORK_PHONE = $arUser['WORK_PHONE'];
$WORK_POSITION = $arUser['WORK_POSITION'];

// Получаем название подразделения человека
$oDep = CIntranetUtils::GetDepartmentsData($arUser["UF_DEPARTMENT"]);
$UF_DEPARTMENT = $arUser["UF_DEPARTMENT"][0];
$UF_DEPARTMENT_NAME = current($oDep);


// Доп.защита - проверка на активность пользователя - если пользователь некативен - редирект (!)
if($ACTIVE != 'Y')
{
    // Если по какой-то причине пользователь заблокирован, возвращаем его назад
    ?>
        <script>
            window.history.back();
        </script>
    <?php
}


// Если с пользователем всё ок - продолжаем (!)

// Новый экземпляр класса Boss (!!!)
$Boss = new Boss($ID, $ACTIVE, $LAST_NAME, $NAME, $SECOND_NAME, $EMAIL, $PERSONAL_GENDER, $PERSONAL_PHOTO, $PERSONAL_BIRTHDAY, $WORK_PHONE, $WORK_POSITION, $GROUP_ID, $UF_DEPARTMENT, $UF_DEPARTMENT_NAME, $IsAdmin);
//best_debug($Boss);

// Проверям - есть ли права на получение отчёта
$Boss -> CheckRights();

?>
<link rel="stylesheet" type="text/css" href="css/style.css">
<?php
//$Boss->getInfo();

// Период отчёта
$from_date = $_GET['DATE_FROM'];
$to_date = $_GET['DATE_TO'];

// Период отчёта: начало текущего месяца
if(!isset($from_date))
{
    $begin = mktime(0, 0, 0, date("m"), 1,  date("Y"));
}
else
{
    $begin = strtotime($from_date);
}
$from_date_report = date("d.m.Y", $begin);

// Период отчёта: конец - вчера)
if(!isset($to_date))
{
    $end = mktime(23, 59, 59, date("m"), date("d")-1,  date("Y"));
}
else
{
    $end = strtotime($to_date);
    $end = mktime(23, 59, 59, date("m", $end), date("d", $end),  date("Y", $end));
}
$to_date_report = date("d.m.Y", $end);

// Для получения информации об отработанном времени
$dateFrom = MakeTimeStamp($from_date,"YYYY.MM.DD");
$dateTo = MakeTimeStamp($to_date,"YYYY.MM.DD");

?>

<div id='main'>
    <div id='my_form_div'>
        <form action='index.php' method='GET'>
            Период отчёта: 	<br><br>

            С: <input id="filter_date_from" type="text" name="DATE_FROM" style='width:70px' value=<?= $from_date_report?>>
            по: <input id="filter_date_to" type="text" name="DATE_TO" style='width:70px' value=<?= $to_date_report ?>>

            <input type="submit" name='submit' value="Показать">
        </form>

    </div>
</div>
<hr>

<div id="print-content">

<h3>Отчет по рабочему времени: <?= $Boss->UF_DEPARTMENT_NAME ?></h3>

<?php

// Получаем список сотрудников подразделения
$order = array("UF_DEPARTMENT" => "asc", "timestamp_x" => "desc");
$tmp = 'asc';
$filter = array("ACTIVE" => "Y");
$rsUsers = CUser::GetList(
    $order,
    $tmp,
    $filter,
    array("SELECT"=>array("UF_*"))
);

$arrUsers = [];
while ($u = $rsUsers->GetNext()) {

    if(in_array($Boss->UF_DEPARTMENT, $u['UF_DEPARTMENT']))
    {
        // Новый экземляр класса BestTimanUser (!)
        $arrUsers[] = new BestTimanUser($u['ID'], $u['LAST_NAME'], $u['NAME'], $u['SECOND_NAME'], $u['EMAIL'], $u['WORK_PHONE'], $u['WORK_POSITION'], $u['PERSONAL_GENDER'], $u['UF_DEPARTMENT']);
    }
};

// График выходов
$WORK_TIME = [];

?>
    <table id='table_result_1' width="100%">
        <tr bgcolor="#ddd">
            <td width="2%">№</td>
            <td width="25%">Ф.И.О.</td>
            <td width="15%">Должность</td>
            <td width="10%">График работы</td>
            <td width="43%">Данные по Биотайм</td>
        </tr>

        <?php

        for ($i = 0; $i < count($arrUsers); $i++) {

            $WORK_TIME[$i]['ID'] = $arrUsers->ID;
            ?>
            <tr>
                <td width="2%"><?= ($i+1) ?></td>
                <td width="25%"><?php
                    $UserLink = "<a href='http://".$_SERVER['HTTP_HOST']. PERSONAL_LINKS . $arrUsers[$i]->UserID . "/' target='_blank'>";
                    $UserLink = $UserLink . $arrUsers[$i]->LAST_NAME." ".$arrUsers[$i]->NAME." ".$arrUsers[$i]->SECOND_NAME . "</a>";
                    echo $UserLink;
                    ?></td>
                <td width="15%"><?= $arrUsers[$i]->WORK_POSITION ?></td>
                <td width="10%"><?php
                    echo "Н: ".$arrUsers[$i]->UserMaxStart_time."<br>";
                    echo "К: ".$arrUsers[$i]->UserMinFinish_time."<br>";
                    echo "П: ".$arrUsers[$i]->UserMinDuration_time;
                    ?></td>
                <td width="43%" style="padding: 0px;">
                    <?php
                    // Получение информации об отработанном времени
                    // Пример: SELECT ID, TIMESTAMP_X, USER_ID, DATE_START, DATE_FINISH, DURATION, FORUM_TOPIC_ID FROM `b_timeman_entries` WHERE USER_ID='3757' AND DATE_START >= '2020-06-01 00:00:00' AND DATE_FINISH <= '2020-06-08 23:59:59'
                    $date_start = date('Y-m-d H:i:s', $begin);
                    $date_finish = date('Y-m-d H:i:s', $end);
                    $query = "SELECT ID, TIMESTAMP_X, USER_ID, DATE_START, DATE_FINISH, DURATION, FORUM_TOPIC_ID FROM `b_timeman_entries` WHERE USER_ID='".$arrUsers[$i]->UserID ."' AND DATE_START >= '".$date_start ."' AND DATE_FINISH <= '".$date_finish ."';";
                    //echo "query = " . $query . "<br>";

                    // Делаем запрос к базе
                    $dbSelect = $DB->Query($query);
                    $j = 0; // Счётчик
                    ?>
                        <table id="table_result_2" width="100%">
                            <tr>
                            <td width="35%">Время входа</td>
                            <td width="35%">Время выхода</td>
                            <td width="30%">Продолж.</td>
                            </tr>
                            <?php

                            while ($arSelect = $dbSelect->Fetch())
                            {
                                    
                                    $WORK_TIME[$i]['DATE_START'][$j] = $arSelect['DATE_START'];
                                    $WORK_TIME[$i]['DATE_FINISH'][$j] = $arSelect['DATE_FINISH'];
                                    $WORK_TIME[$i]['DURATION'][$j] = $arSelect['DURATION'];
                                    $WORK_TIME[$i]['FORUM_TOPIC_ID'][$j] = Comment($arSelect['FORUM_TOPIC_ID']);
                                    $WORK_TIME[$i]['TIMESTAMP_X'][$j] = $arSelect['TIMESTAMP_X'];

                                    $flag = 0;

                                    if (CheckTime_Max ($WORK_TIME[$i]['DATE_START'][$j], $arrUsers[$i]->UserMaxStart) == 1){
                                        $WORK_TIME[$i]['DATE_START'][$j] = "<font color='red'>".$WORK_TIME[$i]['DATE_START'][$j]."</font>";
                                        $flag = 1;
                                    }

                                    if (CheckTime_Min ($WORK_TIME[$i]['DATE_FINISH'][$j], $arrUsers[$i]->UserMinFinish) == 1){
                                        $WORK_TIME[$i]['DATE_FINISH'][$j] = "<font color='red'>".$WORK_TIME[$i]['DATE_FINISH'][$j]."</font>";
                                        $flag = 1;
                                    }

                                    if (CheckTime_Duration ($WORK_TIME[$i]['DURATION'][$j], $arrUsers[$i]->UserMinDuration_time) == 1){
                                        $WORK_TIME[$i]['DURATION'][$j] = "<font color='red'>".HourToTime($WORK_TIME[$i]['DURATION'][$j])."</font>";
                                        $flag = 1;
                                    }
                                    else
                                    {
                                        $WORK_TIME[$i]['DURATION'][$j] = HourToTime($WORK_TIME[$i]['DURATION'][$j]);
                                    }

                                    ?>
                                    <tr>
                                    <td width="35%"><?= $WORK_TIME[$i]['DATE_START'][$j] ?></td>
                                    <td width="35%"><?= $WORK_TIME[$i]['DATE_FINISH'][$j] ?></td>
                                    <td width="30%"><?= $WORK_TIME[$i]['DURATION'][$j] ?></td>
                                    </tr>
                                <?php
                                $j++;
                            }
                            ?>

                        </table>
                </td>
            </tr>
            <?php
        }
        ?>
    </table>
<?php

echo "<br>Выбран период: <b>с " . date('d.m.Y H:i:s',$begin) ." по " . date('d.m.Y H:i:s',$end) ." </b><br>";
// Подпись Руководителя
$Boss->signature();
?>
</div>
<br> <button onClick="javascript:CallPrint('print-content');" title="Версия для печати"><b>Вывести отчёт на печать</b></button>

<script src="js/script.js"></script>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");