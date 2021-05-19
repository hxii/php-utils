<?php

namespace hxii\CLI;

class CLI_Table {

    private $header;
    private $columns;
    private $table;

    public function __construct($padding = 1, $borderChars = '╔═╗╠═╣║╚╝') {
        $this->padding = $padding;
        $this->borderChars = $borderChars;
    }

    private function table(Array $data, $padding, String $border) {
        list($b_top_left, $b_hor, $b_top_right, $b_mid_left, $b_mid_hor, $b_mid_right, $b_vert, $b_bot_left, $b_bot_right) = $this->str_split_unicode($border);
        $lengths = $this->columnLenghts($data);
        //             Length of strings           Length of padding chars        Length of border chars
        $totalLength = array_sum($lengths) + ( count($lengths) * 2 * $padding ) + (count($lengths) + 1);
        $return = $b_top_left . str_repeat($b_hor, ($totalLength-2)) . $b_top_right .PHP_EOL;
        foreach($data as $i=>$array) {
            $return .= $b_vert;
            foreach($array as $col=>$item) {
                $rightpad = $lengths[$col] + $padding;
                $return .= sprintf("%s%-{$rightpad}s%s", str_repeat(' ' , $padding), $item, $b_vert);
            }
            $return .= PHP_EOL;
            if ($i == 0) {
                $return .= $b_mid_left . str_repeat($b_mid_hor, ($totalLength-2)) . $b_mid_right .PHP_EOL;
            }
        }
        $return .= $b_bot_left . str_repeat($b_hor, ($totalLength-2)) . $b_bot_right .PHP_EOL;
        return $return;
    }

    private function str_split_unicode($str, $l = 0) {
        if ($l > 0) {
            $ret = array();
            $len = mb_strlen($str, "UTF-8");
            for ($i = 0; $i < $len; $i += $l) {
                $ret[] = mb_substr($str, $i, $l, "UTF-8");
            }
            return $ret;
        }
        return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    }

    public function print() {
        echo $this->table($this->data, $this->padding, $this->borderChars);
    }

    public function header(Array $header, String $style = '') {
        // if ($style !== '') {
        //     include 'cli.php';
        //     $cli = new \hxii\CLI_Helper();
        //     foreach ($header as &$item) {
        //         $item = $cli->format($style, $item);
        //     }
        // }
        $this->data[0] = $header;

        return $this;
    }

    public function row(Array $row) {
        if (empty($row)) {
            $row = array_fill(0, $this->columns, '');
        }
        $this->data[] = $row;

        return $this;
    }
    
    private function columnLenghts(Array $array) {
      $lengths = [];
      $columnCount = count(reset($array));
      for ($i=0; $i<$columnCount; $i++) {
        $column = array_column($array, $i);
        $lengths[$i] = max(array_map('strlen', $column));
      }
      return $lengths;
    }

}