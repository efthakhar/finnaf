<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class CombineUnique implements ValidationRule
{
    public $pairs = [];

    public $table = '';

    public $message = '';

    public $ignore_id = '';

    public function __construct($pairs, $table, $message, $ignore_id = '')
    {
        $this->pairs = $pairs;
        $this->table = $table;
        $this->message = $message;
        $this->ignore_id = $ignore_id;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        if ($this->ignore_id) {
            $count = DB::table($this->table)->where($this->pairs)->where('id', '!=', $this->ignore_id)->count();
            if ($count > 0) {
                $fail($this->message);
            }
        } else {
            $count = DB::table($this->table)->where($this->pairs)->count();
            if ($count > 0) {
                $fail($this->message);
            }
        }

    }
}
