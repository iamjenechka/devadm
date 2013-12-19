<?php
// класс шаблона
class template_class
    {
    var $values     = array();	// переменные шаблона
    var $html;				// HTML-код

// функция загрузки шаблона
    function get_tpl($tpl_name)
      {
      if(empty($tpl_name) || !file_exists($tpl_name))
        {
        return false;
        }
      else
        {
        $this->html  = join('',file($tpl_name));
        }
      }

// функция установки значения
    function set_value($key,$var)
      {
      $key = '{' . $key . '}';
      $this->values[$key] = $var;
      }

// парсинг шаблона
    function tpl_parse()
      {
      foreach($this->values as $find => $replace)
             {
             $this->html = str_replace($find, $replace, $this->html);
             }
      }
    }

// экземпляр класса
$tpl = new template_class;
?>
