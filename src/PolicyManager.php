<?php
declare(strict_types=1);
namespace Harpya\DataGate;

class PolicyManager
{
    protected $rules = [];

    /**
     * Undocumented function
     *
     * @param [type] $attributeName
     * @param array $sanitizationOptions
     * @param array $validationOptions
     * @return void
     */
    public function createRule($attributeName, $sanitizationOptions=[], $validationOptions=[])
    {
        $this->rules[$attributeName] = new Rule($sanitizationOptions, $validationOptions);
    }

    /**
     * @return array
     */
    public function getAllRules() : array
    {
        return $this->rules;
    }
}
