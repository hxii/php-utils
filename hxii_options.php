<?php

namespace hxii;

class Options {

    public static $rawArgs;
    public static $options = [];
    public static $aliases = [];
    public static $flags = [];
    public static $required = [];
    public $header = '';
    public $lastValue;
    public $rawString;

    public function __isset($name)
    {
        return isset($this->$name);
    }

    public function __get($name)
    {
        if (isset($this->{$name})) {
            return $this->{$name};
        } else {
            $this->out('error', "Property $name doesn't exist!");
        }
    }

    public function process() {
        global $argv;
        self::$rawArgs = array_slice($argv, 1); // Remove filename from options
        $this->rawString = implode(' ', self::$rawArgs); // Reconstruct argumemts so that we parse them ourselves
        if (empty($this->rawString)) {
            $this->showHelp();
        }
        $this->parse($this->rawString);
    }

    private function parse(string $args) {
        $pairs_pattern = '@((^|\s)-{1,2})@';
        $ov_pattern = '@(=|\s|,)@';
        $pairs = preg_split($pairs_pattern, $args);
        $pairs = array_filter($pairs);
        foreach ($pairs as $pair) {
            $ov = preg_split($ov_pattern, $pair);
            list($option, $values) = [ltrim($ov[0], '-'), array_slice($ov, 1)];
            if (!empty($values)) {
                if (!array_key_exists($option, self::$options) && !array_key_exists($option, self::$aliases)) {
                    $this->out('error', "Option $option is invalid");
                    continue;
                }
                if (array_key_exists($option, self::$aliases)) {
                    // If the alias exists, set the original property instead
                    $option = self::$aliases[$option];
                }
                $this->{$option} = $values;
                if ($this->is_required($option)) {
                    self::$required = array_diff(self::$required, [$option]); // Remove required option
                }
            } elseif (empty($values)) {
                $this->{$option} = true;
            }
        }
        if (!empty(self::$required)) {
            foreach (self::$required as $item) {
                $this->out('error', "Option '$item' is required!");
            }
            die();
        }
    }

    private function is_required(string $option) {
        return in_array($option, self::$required);
    }

    public function option(string $option, string $help = '') {
        if (!array_key_exists($option, self::$options)) {
            self::$options[$option] = $help;
            $this->lastValue = $option;
        }

        return $this;
    }

    public function flag(string $flag, string $help = '') {
        if (!array_key_exists($flag, self::$flags)) {
            self::$flags[$flag] = $help;
            $this->lastValue = $flag;
        }

        return $this;
    }

    public function explain(string $help) {
        if (array_key_exists($this->lastValue, self::$options)) {
            self::$options[$this->lastValue] = $help;
        } elseif (array_key_exists($this->lastValue, self::$flags)) {
            self::$flags[$this->lastValue] = $help;
        }

        return $this;
    }

    public function alias(string $alias) {
        if (!isset($this->lastValue)) die ("Alias $alias not chained properly!");
        if (array_key_exists($this->lastValue, self::$options)) {
            self::$aliases[$alias] = $this->lastValue; 
        } else {
            die("Option {$this->lastValue} doesn't exist!");
        }

        return $this;
    }

    public function require() {
        if (!isset($this->lastValue)) die ("Require not chained properly!");
        if (array_key_exists($this->lastValue, self::$options) ||
            array_key_exists($this->lastValue, self::$flags)) {
            self::$required[] = $this->lastValue;
        }

        return $this;
    }

    public function header(string $text) {
        $this->header = $text;
    }

    private function out(string $type, string $message) {
        $type = strtolower($type);
        switch ($type) {
            case 'error':
                echo $this->color('red', 'ERROR: ') . $message . PHP_EOL;
                break;
        }
    }

    private function color(string $color, string $text) {
        switch ($color) {
            case 'red':
                return "\033[31m$text\033[0m";
                break;
            case 'bold':
                return "\033[1m$text\033[0m";
                break;
        }
    }

    private function showHelp() {
        print("$this->header\n");
        $args = array_keys(self::$options);
        usort($args, function($a, $b) {
            strlen($a) > strlen($b);
        });
        $len = strlen(reset($args)) + 10;
        $maskHeader = "\n%s\n";
        $maskOption = "%-{$len}s%s\n";
        $maskAlias = "%-{$len}s%s\n";
        printf($maskHeader, $this->color('bold', 'Options'));
        foreach (self::$options as $option => $explanation) {
            $required = $this->is_required($option) ? ' (Required)' : '';
            printf($maskOption, '--'.$option, $explanation . $required);
            $aliases = array_keys(self::$aliases, $option);
            if (!empty($aliases)) {
                printf($maskAlias, '', 'Aliases: ' . implode(',', $aliases));
            }
        }
        printf($maskHeader, $this->color('bold', 'Flags'));
        foreach (self::$flags as $flag => $explanation) {
            printf($maskOption, '-'.$flag, $explanation);
        }
    }

}