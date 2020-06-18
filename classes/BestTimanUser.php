<?

// BestTimanUser - extends CTimeManUser (  created from best_timeman_entries.php )
// Класс для работы с графиком работ
// Created by Best IT Pro, 2020


class BestTimanUser extends CTimeManUser
{
  public $UserID;
  public $UserMaxStart;
  public $UserMinFinish;
  public $UserMinDuration;
  public $UserMaxStart_time;
  public $UserMinFinish_time;
  public $UserMinDuration_time;

  public function __construct ($UserID, $LAST_NAME, $NAME, $SECOND_NAME, $EMAIL, $WORK_PHONE, $WORK_POSITION, $PERSONAL_GENDER, $UF_DEPARTMENT)
  {
    parent::__construct();  // конструктор родительского класса

    $this ->UserID = $UserID;

    $this ->LAST_NAME = $LAST_NAME;
    $this ->NAME = $NAME;
    $this ->SECOND_NAME= $SECOND_NAME;
    $this ->EMAIL = $EMAIL;
    $this ->WORK_PHONE = $WORK_PHONE;
    $this ->WORK_POSITION = $WORK_POSITION;
    $this ->PERSONAL_GENDER = $PERSONAL_GENDER;
    $this -> UF_DEPARTMENT = $UF_DEPARTMENT;

    $UserSetings = $this -> GetSettings();
    $this ->UserMaxStart = $UserSetings['UF_TM_MAX_START'];
    $this ->UserMinFinish = $UserSetings['UF_TM_MIN_FINISH'];
    $this ->UserMinDuration = $UserSetings['UF_TM_MIN_DURATION'];

    $this ->UserMaxStart_time = HourToTime($this ->UserMaxStart);
    $this ->UserMinFinish_time = HourToTime($this ->UserMinFinish);
    $this ->UserMinDuration_time = HourToTime($this ->UserMinDuration);
  }

}


// Another functions

function HourToTime ($argument)
{
  $hour = floor($argument/3600);
  $sec = $argument - ($hour*3600);
  $min = floor($sec/60);
  $sec = $sec - ($min*60);
  $result =  AddZeroToTime($hour).":".AddZeroToTime($min).":".AddZeroToTime($sec);
  return $result;
}


function AddZeroToTime ($argument)
{
  if ( strlen( (string) $argument) == 1 )
  {
    $argument= "0" . $argument;
    
  } 
  return $argument;
}

// Время в секунды
function Time2ToSeconds($time='00:00:00')
{
    list($hours, $mins, $secs) = explode(':', $time);
    return ($hours * 3600 ) + ($mins * 60 ) + $secs;
}

// Раскладываем дату и время - возвращаем только время
function TimeFromDate ($DateAndTime)
{

  $datetime = new DateTime($DateAndTime);

  $date = $datetime->format('Y-m-d');
  $time = $datetime->format('H:i:s');

  return $time;
}

// Если позже (больше)
function CheckTime_Max ($ThisTime, $ControlTime)
{
  $ThisTime_time = TimeFromDate($ThisTime);          // only time
  $timestamp = Time2ToSeconds($ThisTime_time);       // to secs

  if($timestamp > $ControlTime)
  {
    return 1;
  }
  else
  {
    return -1;
  }

}

// Если раньше (меньше)
function CheckTime_Min ($ThisTime, $ControlTime)
{
  $ThisTime_time = TimeFromDate($ThisTime);         // only time
  $timestamp = Time2ToSeconds($ThisTime_time);      // to secs

  if($timestamp < $ControlTime)
  {
    return 1;
  }
  else
  {
    return -1;
  }

}

// Проверка длительности рабочего дня
function CheckTime_Duration ($ThisTime, $ControlTime)
{


  $ControlTime = Time2ToSeconds($ControlTime); // to secs

  if($ThisTime < $ControlTime)
  {
    return 1;
  }
  else
  {
    return -1;
  }

}


// Проверка - была ли объяснительная 
function Comment($FORUM_TOPIC_ID)
{

  $result = '';
  // выберем все опубликованные сообщения темы с кодом $TID в порядке поступления
  $db_res = CForumMessage::GetList(array("ID"=>"ASC"), array("TOPIC_ID"=>$FORUM_TOPIC_ID));
  while ($ar_res = $db_res->Fetch())
  {
    $result = $ar_res["POST_MESSAGE"];
  }
  return $result;
}


?>