<?php
/**
 * @return array|mixed
 * @throws Exception
 */
function cfg()
{
    static $cfg;

    if (empty($cfg)) {
        $defaultJson = ROOT . "/app/conf/config.json";

        if (!file_exists($defaultJson)) {
            throw new Exception("File not found {$defaultJson}");
        }

        $cfg = json_decode(file_get_contents($defaultJson), true);

        /**
         * Check if config.local.json exists
         * Load overriding values to default config from local JSON
         */
        $localJson = ROOT . "/app/conf/config.local.json";
        if (file_exists($localJson)) {
            $localCfg = json_decode(file_get_contents($localJson), true);
            $cfg = array_replace_recursive($cfg, $localCfg);
        }

        /** trick to get object from array */
        $cfg = json_decode(json_encode($cfg));
    }

    return $cfg;
}