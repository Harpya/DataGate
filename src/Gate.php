<?php
declare(strict_types=1);

namespace Harpya\DataGate;

class Gate
{
    protected $policyManager;

    protected $sources = ['_GET', '_POST'];

    /**
     * Returns the current PolicyManager configured for this instance.
     *
     * @return PolicyManager
     */
    public function getPolicyManager() : PolicyManager
    {
        if (!$this->policyManager) {
            $this->policyManager = new PolicyManager();
        }
        return $this->policyManager;
    }


    /**
     * Applies the sanitizing and validation rules over all variables sent by the client.
     *
     * @param boolean $sanitize
     * @param boolean $validate
     * @return void
     */
    public function handle($sanitize=true, $validate=true) : void
    {
        $rules = $this->getPolicyManager()->getAllRules();



        if ($sanitize) {
            foreach ($this->sources as $sourceName) {
                // $ls = $GLOBALS[$sourceName];
                // echo "\n $sourceName -- " . print_r($ls, true);
                Sanitizer::sanitize($GLOBALS[$sourceName], $rules);
            }
        }
        if ($validate) {
            foreach ($this->sources as $sourceName) {
                // $ls = $$sourceName;
                // echo "\n $sourceName - " . print_r($ls, true);
                Validator::validate($GLOBALS[$sourceName], $rules);
            }
        }
    }
}
