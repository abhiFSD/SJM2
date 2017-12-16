<?php

namespace POW;

class BaseModel
{
    protected static $table_name = '__invalid_table';
    protected static $fillable = [];
    protected static $decoration = [];
    protected static $hidden = [];
    protected static $associations = [];
    protected static $db = null;
    protected static $time_attributes = [];

    protected $_values = [];
    protected $_associations = [];

    public function __set($key, $value)
    {
        if (in_array($key, static::$fillable) || in_array($key, static::$decoration))
        {
            $this->_values[$key] = $value;
        }
        elseif (in_array($key, static::$hidden))
        {
            $this->_values[$key] = $value;
        }
        else
        {
            throw new \Exception("Unknown attribute $key", 1);
        }
    }

    public function __get($key)
    {
        if (in_array($key, static::$fillable) || in_array($key, static::$decoration))
        {
            return empty($this->_values[$key]) ? null : $this->_values[$key];
        }
        elseif (0 === strpos($key, 'hidden_'))
        {
            $real_key = substr($key, 7);

            if (in_array($real_key, static::$hidden))
            {
                return empty($this->_values[$real_key]) ? null : $this->_values[$real_key];
            }
        }
        elseif (array_key_exists($key, static::$associations))
        {
            return $this->retrieve_association($key, []);
        }
        elseif (method_exists($this, 'get_'.$key))
        {
            return call_user_func([$this, 'get_'.$key]);
        }

        throw new \Exception("Unknown attribute $key", 1);
    }

    public function __call($func, $args)
    {
        $keys = array_merge(static::$fillable, static::$decoration, static::$hidden);
        $var = substr($func, 4);

        if (0 === strpos($func, 'get_'))
        {
            $value = $this->{$var};

            if (in_array($var, $keys) && !empty($args))
            {
                return static::process_attribute($var, $value, $args);
            }
            if (in_array($var, $keys))
            {
                return $value;
            }
            if (array_key_exists($var, static::$associations))
            {
                return $this->retrieve_association($key, $args);
            }
        }
        elseif (0 === strpos($func, 'set_'))
        {
            if (in_array($var, $keys))
            {
                $this->{$var} = $args[0];
            }
        }
        else {
            throw new \Exception("Unknown function $func", 1);
        }
    }

    public function __isset($key)
    {
        return isset($this->_values[$key]);
    }

    public static function __callStatic($func, $args)
    {
        if (0 === strpos($func, 'with_'))
        {
            $key = substr($func, 5);

            return call_user_func_array([get_called_class(), 'query_with_attribute'], array_merge([$key], $args));
        }

        throw new \Exception("Error Calling $func", 1);
    }

    public static function get_session()
    {
        return get_instance()->session;
    }

    protected static function query_with_attribute($key, $value)
    {
        $query = null;

        if (!empty($value))
        {
            $query = static::getdb()
                ->where($key, $value)
                ->get(static::$table_name);
        }

        if ($key == 'id')
        {
            $row = $query && $query->num_rows() ? $query->row() : null;

            $object = new static();
            $object->assign_row($row);
    
            return $object;
        }
        else
        {
            $items = [];

            if ($query && $query->num_rows())
            {
                foreach ($query->result() as $row)
                {
                    $object = new static();
                    $object->assign_row($row);

                    $items[] = $object;
                }
            }

            return $items;
        }

    }

    protected static function process_attribute($attribute, $value, $args)
    {
        if (in_array($attribute, static::$time_attributes))
        {
            return date_format(date_create($value), $args[0]);
        }

        throw new \Exception("Extra attributes", 1);
    }

    //==================================================
    // Under the hood
    //==================================================

    public static function getdb()
    {
        if (!static::$db)
        {
            static::$db = get_instance()->db;
        }

        return static::$db;
    }

    protected function assign_row($row)
    {
        $keys = array_merge(static::$fillable, static::$decoration, static::$hidden);

        foreach ($keys as $key)
        {
            if ($row && !empty($row->{$key}))
            {
                $this->_values[$key] = $row->{$key};
            }
        }
    }

    protected static function listify($query)
    {
        $rows = [];

        if ($query && $query->num_rows())
        {
            foreach ($query->result() as $row)
            {
                $object = new static();
                $object->assign_row($row);

                $rows[] = $object;
            }
        }

        return $rows;
    }

    protected static function row($query)
    {
        $row = $query && $query->num_rows() ? $query->row() : null;
        
        $object = new static();
        $object->assign_row($row);

        return $object;
    }

    protected function retrieve_association($key)
    {
        if (!in_array($key, $this->_associations))
        {
            $options = static::$associations[$key];

            $func = $options['func'];
            $args = $options['args'];

            $argument_values = [];

            foreach ($args as list($type, $value))
            {
                if ($type == 'attribute')
                {
                    $function = 'get_'.$value;
                    $argument_values[] = $this->$function();
                }
                elseif ($type == 'value')
                {
                    $argument_values[] = $value;
                }
            }

            $this->_associations[$key] = call_user_func_array($func, $argument_values);
        }

        return $this->_associations[$key];
    }

    //==================================================
    // User-facing functions
    //==================================================

    public function debug($return_array = false)
    {
        if ($return_array)
        {
            return $this->_values;
        }
        else 
        {
            print_r($this->_values);
        }
    }

    public function save()
    {
        $data = [];
        foreach ($this->_values as $key => $value)
        {
            if ('id' == $key) continue;
            
            if (in_array($key, static::$fillable) || in_array($key, static::$hidden))
            {
                $data[$key] = $value;
            }
        }

        if ($this->get_id())
        {
            static::getdb()
                ->where('id', $this->get_id())
                ->update(static::$table_name, $data);
        }
        else
        {
            static::getdb()
                ->insert(static::$table_name, $data);

            $id = static::getdb()->insert_id();

            $this->set_id($id);

            return $id;
        }
    }

    public function delete()
    {
        if ($this->get_id())
        {
            $status = static::getdb()
                ->where('id', $this->get_id())
                ->delete(static::$table_name);

            $this->_values = [];
            $this->_associations = [];

            return $status;
        }
    }

    public function assign_array($array)
    {
        $keys = array_merge(static::$fillable, static::$decoration, static::$hidden);

        foreach ($array as $key => $value)
        {
            if (in_array($key, $keys))
            {
                $this->_values[$key] = $value;
            }
        }
    }

    public static function column($query, $column_name)
    {
        $rows = [];

        if ($query && $query->num_rows())
        {
            foreach ($query->result() as $row)
            {
                $rows[] = $row->{$column_name};
            }
        }

        return $rows;
    }

    public static function list_key_val($key_column, $value_column, $sort = null)
    {
        $db = static::getdb();

        if (is_array($sort))
        {
            foreach ($sort as $row)
            {
                $db->order_by($row[0], $row[1]);
            }
        }

        $query = $db
            ->select($key_column)
            ->select($value_column)
            ->get(static::$table_name);

        $items = [];
        if ($query && $query->num_rows())
        {
            foreach ($query->result() as $row)
            {
                $items[$row->{$key_column}] = $row->{$value_column};
            }
        }

        return $items;
    }

}
