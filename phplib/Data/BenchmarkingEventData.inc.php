<?php

/*
 * Specific functions to BenchmarkingEvent
 */

function sumMetrics($data) {
    $metrData['community_id'] = $data['community_id'];
    switch ($data['community_id']) {
        case 'CAMEO-3D':
            foreach (array_values($data['InputDatasets']) as $dts) {
                $dtsdata = getOneDocument("Dataset", $dts);
                $metrData['MSresults'][] = ['dataset_id' => $dtsdata['_id'], 'metrics' => $dtsdata['metrics']];
            }
            break;
        case 'QfO':
            break;
        default:
    }
    return $metrData;
}

function prepMetricsHTML($data, $templates) {
    $html = '';
    switch ($data['community_id']) {
        case "CAMEO-3D":
            foreach ($data['metricsSummary']['MSresults'] as $res) {
                $res['MSLines'] = '';
                foreach ($res['metrics'] as $rmet) {
                    $MSinnerhtml = parseTemplate(['metrics_id'=> $rmet['metrics_id']],$templates['CAMEO-3D']['InnerHeader']);
                    foreach (array_keys($rmet['result']) as $tool_id) {
                        $MSinnerhtml .= parseTemplate(['tool_id'=>$tool_id, 'MSresult'=>$rmet['result'][$tool_id]], $templates['CAMEO-3D']['InnerLine']);
                    }
                    $res['MSLines'] .= parseTemplate(
                            ['MSinnerhtml'=>$MSinnerhtml], $templates['CAMEO-3D']['InnerTable']);
                }
                $html .= parseTemplate($res, $templates['CAMEO-3D']['Line']);
            }
            break;
        default:
            $html = "<p>Not implemented (yet)</p>";
    }
    return $html;
}
