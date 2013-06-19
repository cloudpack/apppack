<?php
class MyValidations
{
    public static function _validation_valid_date($val)
    {
        if (!$val)
        {
            return true;
        }

        $parts = array();
        // yyyy-mm-dd
        if (!preg_match('/^([0-9]{4})[\-\/\.](0?[0-9]|1[0-2])[\-\/\.]([0-2]?[0-9]|3[01])$/', $val, $parts))
        {
            return false;
        }

        if (checkdate($parts[2], $parts[3], $parts[1]) === true)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
