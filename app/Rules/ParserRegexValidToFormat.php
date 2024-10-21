<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class ParserRegexValidToFormat implements ValidationRule, DataAwareRule
{
    protected $data = [];

    public function __construct(protected string $format_field_name)
    {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //TODO refactoring
        $text = $this->data[$this->format_field_name];
        $regex = '/' . $value . '/mi';

        try {
            preg_match_all($regex, $text, $matches, PREG_SET_ORDER);
        } catch (\Exception $e) {}

        if (empty($matches[0]['amount'])) {
            $fail("Regex не соответствует данным в поле формат."); //TODO локализация
        }
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
