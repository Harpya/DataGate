<?php
declare(strict_types=1);

namespace Harpya\DataGate;

class Rule
{
    const ON_ERROR_JUST_FIX = 'on_error_fix';
    const ON_ERROR = 'onError';

    const VALIDATION_MAXLENGTH = 'maxlength';

    protected $sanitization = [];
    protected $validation = [];

    public function __construct($sanitizationOptions = [], $validationOptions = [])
    {
        $this->sanitization = $sanitizationOptions;
        $this->validation = $validationOptions;
    }

    public function getSanitizationSpecs()
    {
        return $this->sanitization;
    }

    public function getValidationSpecs()
    {
        return $this->validation;
    }

    public function execValidation(&$variable)
    {
        $isValid = true;

        // echo "\n Value = $variable ";
        $specs = $this->getValidationSpecs();

        if (isset($specs[static::VALIDATION_MAXLENGTH]) && strlen($variable) > $specs[static::VALIDATION_MAXLENGTH]) {
            if (isset($specs[static::ON_ERROR]) && $specs[static::ON_ERROR] == static::ON_ERROR_JUST_FIX) {
                $variable = substr($variable, 0, $specs[static::VALIDATION_MAXLENGTH]);
            } else {
                $isValid = false;
            }
            // }
        }
    }
}
