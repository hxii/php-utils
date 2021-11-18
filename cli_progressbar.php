<?php

namespace hxii\CLI;

class CLI_ProgressBar extends CLI_Helper {

    private $startValue, $endValue, $currentValue;

    public function __construct(int $startValue = 1, int $endValue = 10, string $type = 'counter', $message = "Progress", $barSize = 40)
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
        $width = (int) trim(@shell_exec('tput cols') ?? 100);
        // if (!$width) $width = 100;
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