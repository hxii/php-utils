<?php

namespace hxii;

class CLI_Helper {

    public $opentag = "\033[";
    public $resetTag = "\033[0m";
    public $formats = [
        'bold' => '1',
        'dim' => '2',
        'underline' => '4',
        'invert' => '7'
    ];
    
    public function format(string $tag, string $text) {
        if (array_key_exists($tag, $this->formats)) {
            $code = $this->formats[$tag];
            return "{$this->opentag}{$code}m{$text}{$this->opentag}2{$code}m";
        }
    }

    public function print($data, string $title = '') {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data, JSON_PRETTY_PRINT);
        }
        $title = !empty($title) ? $this->format('underline', "$title:").' ' : '';
        echo "$title$data\n";
    }

}