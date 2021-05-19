<?php

namespace hxii;

class CSV_Helper {

    private $column,$lastValue;

    public function __construct($delimeter = ',', $enclosure = '"')
    {
        $this->delimeter = $delimeter;
        $this->enclosure = $enclosure;

        return $this;
    }

    public function open($filename) {
        if (is_file($filename) && is_readable($filename)) {
            $this->filename = $filename;
            $fh = fopen($this->filename, 'r');
            while (false !== ($data = fgetcsv($fh, 0, $this->delimeter, $this->enclosure))) {
                $this->csv[] = $data;
            }
            fclose($fh);
            // $this->csv = array_map('str_getcsv', file($filename));
            $this->csv = $this->array_map_recursive('trim', $this->csv);
            $this->columns = $this->columns();
        } else {
            return false;
        }
        return $this;
    }

    public function delimeter($delimeter) {
        $this->delimeter = $delimeter;

        return $this;
    }

    public function enclosure($enclosure) {
        $this->enclosure = $enclosure;

        return $this;
    }

    public function findHeader($string) {
        $matches = preg_grep("#$string#i", $this->columns());
        $this->columns(array_values($matches));

        return $this;
    }

    public function columns($headers = NULL) {
        if (!is_null($headers) && '' !== $headers) {
            unset($this->columns);
            if (is_array($headers)) {
                foreach ($headers as $header) {
                    $this->columns[$header] = array_keys($this->csv[0], $header)[0];
                }
            } else {
                $this->columns = array_keys($this->csv[0], $headers);
            }
            return $this;
        } else {
            return array_flip($this->csv[0]);
        }
    }

    public function values($columns = null) {

        foreach ($this->columns as $column=>$key) {
            $values[$column] = array_column($this->csv, $key);
            unset($values[$column][0]);
        }

        if (!is_null($columns)) {
            $columns = array_flip($columns);
            $values = array_intersect_key($values, $columns);
        }

        return $values;
    }

    public function dump() {
        var_dump($this->csv);
    }

    private function array_map_recursive($callback, $array)
    {
      $func = function ($item) use (&$func, &$callback) {
        return is_array($item) ? array_map($func, $item) : call_user_func($callback, $item);
      };
    
      return array_map($func, $array);
    }

}