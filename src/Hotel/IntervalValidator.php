<?php

namespace Hotel;

use DateTimeImmutable;

class IntervalValidator
{
    /** @var array */
    private $data = [];

    /** @var array */
    private $errors = [];

    public function validate(array $data): bool
    {
        $this->setData($data);
        $this->errors = [];

        $validators = [
            ['validateRequired', ['date_start', 'date_end', 'price'], ['stopOnErrors' => true]],
            ['filterTrim', ['date_start', 'date_end', 'price']],
            ['validateDate', ['date_start', 'date_end']],
            ['validateDateOrder', ['date_start', 'date_end']],
            ['filterFloat', 'price'],
        ];

        foreach ($validators as $validatorParams) {
            [$validator, $attributes,] = $validatorParams;
            $options = $validatorParams[2] ?? [];

            $this->$validator($attributes);

            if (!empty($options['stopOnErrors']) && $this->hasErrors()) {
                return false;
            }
        }

        return !$this->hasErrors();
    }

    /**
     * @param string|array $attributes
     */
    public function validateRequired($attributes): void
    {
        $attributes = (array)$attributes;
        foreach ($attributes as $attribute) {
            if (!isset($this->data[$attribute]) || trim($this->data[$attribute]) == '') {
                $this->addError($attribute, "`$attribute` cannot be blank");
            }
        }
    }

    /**
     * @param string|array $attributes
     */
    public function validateDate($attributes): void
    {
        $attributes = (array)$attributes;
        foreach ($attributes as $attribute) {
            if (!$this->date($this->data[$attribute])) {
                $this->addError($attribute, "Wrong `$attribute` date format");
            }
        }
    }

    /**
     * @param string|array $attributes
     */
    public function filterTrim($attributes): void
    {
        $attributes = (array)$attributes;
        foreach ($attributes as $attribute) {
            $this->data[$attribute] = trim($this->data[$attribute]);
        }
    }

    /**
     * @param array $attributes
     */
    public function validateDateOrder(array $attributes): void
    {
        if (count($attributes) < 2) {
            return;
        }

        array_reduce($attributes, function ($lower, $higher) {
            if ($lower !== null &&  $this->date($this->data[$lower]) > $this->date($this->data[$higher])) {
                $this->addError($lower, "`$lower` should be less than or equals to `$higher`");
            }
            return $higher;
        });
    }

    /**
     * @param string|array $attributes
     */
    public function filterFloat(string $attributes): void
    {
        $attributes = (array)$attributes;
        foreach ($attributes as $attribute) {
            $this->data[$attribute] = (float)str_replace(',', '.', $this->data[$attribute]);
        }
    }

    public function setData(array $data): void
    {
        $this->data = array_intersect_key($data, array_fill_keys(['date_start', 'date_end', 'price'], ''));
    }

    public function getFilteredData(): array
    {
        return $this->data;
    }

    public function hasErrors(): bool
    {
        return count($this->errors) != 0;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getErrorSummary(): array
    {
        return array_reduce($this->errors, function ($result, $attributeErrors) {
            return array_merge($result, $attributeErrors);
        }, []);
    }

    public function addError(string $attribute, string $message): void
    {
        $this->errors[$attribute][] = $message;
    }

    public function date(string $value): ?DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat('Y-m-d', $value) ?: null;
    }
}