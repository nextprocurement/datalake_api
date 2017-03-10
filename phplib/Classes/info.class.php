<?php

/**
 * Aggregated info from DataStores
 *
 * @author gelpi
 */

class info extends DataStore {

    const software = 'ELIXIR Benchmarking Data Store';
    const version = '0.1';

    public $baseXMLTag = 'ELIXIRBenchData';

    function search($params) { //api/info/
        $data = [
            'Software' => info::software,
            'Version' => info::version
        ];
        foreach ($GLOBALS['loadedClasses'] as $cl) {
            if (method_exists($cl, 'info')) {
                $data[preg_replace('/s$/','s_',$cl)] = $cl::info($params)[1];
            }
        }
        return [STRUCT,$data];
    }
}
