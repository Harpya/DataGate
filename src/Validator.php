<?php
declare(strict_types=1);

namespace Harpya\DataGate;

class Validator
{
    public static function validate(&$lsVars = [], $lsRules = [])
    {
        foreach ($lsVars as $varName => $varValue) {
            $rule = $lsRules[$varName] ?? false;
            if ($rule) {
                $rule->execValidation($lsVars[$varName]);
                // static::execValidation($lsVars[$varName], $rule);
            }
        }
    }

    /**
     * Execute all steps to validate all input
     *
     * @param mixed $variable
     * @param Rule $rule
     * @return void
     */
    protected static function execValidation(&$variable, Rule $rule)
    {
        $isValid = true;

        // echo "\n Value = $variable ";
        $specs = $rule->getValidationSpecs();

        $rule->applyValidation($variable);

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
