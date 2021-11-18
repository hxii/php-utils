<?php

namespace hxii\CLI;

class CLI_Helper {

    public $opentag = "\033[";
    public $resetTag = "\033[0m";
    public $formats = [
        'bold' => '1',
        'dim' => '2',
        'underline' => '4',
        'invert' => '7'
    ];
    
    public function format(string $tag, string $text) : String {
        if (array_key_exists($tag, $this->formats)) {
            $code = $this->formats[$tag];
            return "{$this->opentag}{$code}m{$text}{$this->opentag}2{$code}m";
        }
    }

    public function print($data, string $title = '', $newline = true) : Void {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data, JSON_PRETTY_PRINT);
        }
        $title = !empty($title) ? $this->format('underline', "$title:").' ' : '';
        echo "$title$data" . (($newline) ? "\n" : "\r");
    }

    public function normalize($data) {
        $return = '';
        if (!is_array($data)) {
            $return .= $data;
        } elseif(is_array($data)) {
            $return .= PHP_EOL;
            foreach ($data as $key=>$item) {
                $return .= '  ' . $key . ': ' . $this->normalize($item).PHP_EOL;
            }
        }
        return $return;
    }

    public function arr2keys(Array $array, $depth = 0) : String {
        // $return = '';
        // foreach ($array as $key=>$value) {
            //   if (is_array($value)) {
            //     $return .= str_repeat(' ', $depth) . "$key:".PHP_EOL;
            //     $return .= $this->arr2keys($value, $depth+1) . PHP_EOL;
            //   } else {
            //     $return .= str_repeat(' ', $depth) . "$key, ";
            //   }
            // }
            // $return = rtrim($return, ',');
            // return $return;
        $return = "";
        foreach ($array as $key=>$value) {
            $return .= "$key";
            if (is_array($value)) {
                $return .= ': ';
                $this->arr2keys($value);
            }
            $return .= ',';
        }
        // return implode(',', array_keys($array));
        return rtrim($return, ',');
      }

      public function break(string $string, int $length = 70, int $limit = 5) {
        $pattern = '/(.{0,'.$length.'}.+?\b)/m';
        $replacement = '\n';
        preg_match_all($pattern, $string, $matches, PREG_PATTERN_ORDER);
        return array_splice($matches[0], 0, $limit);
      }

}