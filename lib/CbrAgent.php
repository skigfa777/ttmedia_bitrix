<?php
namespace TTMedia;

use \TTMedia\CurrencyTable;
use \SoapClient;
use \SimpleXMLElement;

class CbrAgent {

    public static function agent()
    {

        $client = new SoapClient("http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL");

        $params = [
            'On_date' => date('Y-m-d'),
        ];

        try {
            $response = $client->GetCursOnDateXML($params);

            $xml = new SimpleXMLElement($response->GetCursOnDateXMLResult->any);

            // возвращаемых валют <50, потому сойдет и так
            foreach ($xml->ValuteCursOnDate as $item) {
                $result = CurrencyTable::Add([
                    'CODE' => $item->VchCode,
                    'COURSE' => $item->Vcurs
                ]);
            }  
        } catch (SoapFault $e) {
            throw new Exception($e->getMessage());
        }

        return "TTMedia\CbrAgent::agent();";
    }
}
