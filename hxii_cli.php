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
        $return = '';
        foreach ($array as $key=>$value) {
          if (is_array($value)) {
            $return .= str_repeat(' ', $depth) . "$key:".PHP_EOL;
            $return .= $this->arr2keys($value, $depth+1) . PHP_EOL;
          } else {
            $return .= str_repeat(' ', $depth) . "$key, ";
          }
        }
        $return = rtrim($return, ',');
        return $return;
      }

}

class ProgressBar extends CLI_Helper {

    private $startValue, $endValue, $currentValue;

    public function __construct(int $startValue = 1, int $endValue, string $type = 'counter', $message = "Progress", $barSize = 40)
    {
        $this->startValue = $startValue;
        $this->currentValue = $this->startValue;
        $this->endValue = $endValue;
        $this->type = $type;
        if (!method_exists($this, $type)) {
            die("Type $type doesn't exist!");
        }
        $this->message = $message;
        $this->barSize = $barSize;
    }

    public function advance($customMessage = '', int $advanceBy = 1) {
        $progress = call_user_func([$this,$this->type]);
        $line = "$customMessage$progress";
        $this->print(str_pad('', strlen($line), ' '), '', false);
        if ($this->currentValue <= $this->endValue) {
            $this->print($line, $this->message, false);
            $this->currentValue += $advanceBy;
        }
    }

    public function finish($finishMessage = 'Complete') {
        $width = @shell_exec('tput cols');
        if (!$width) $width = 100;
        $this->print(str_pad('', $width, ' '), '', false);
        $this->print($finishMessage, $this->message, true);
    }

    private function bar() : String {
        $progress = round($this->currentValue / $this->endValue,1) * $this->barSize;
        $remaining = $this->barSize - $progress;
        return str_repeat('█', $progress).str_repeat('░', $remaining);
    }

    private function counter() : String {
        return "($this->currentValue / $this->endValue)";
    }
}