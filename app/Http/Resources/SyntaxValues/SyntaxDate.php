<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxDate
 * Datumswerte
 * @package App\Http\Resources
 */
class SyntaxDate extends JsonResource {

    /**
     * @var ProcessVersion
     */
    public $resource;

    /**
     * Transform the resource into an array.
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            (array) new Item('Aktuelles Datum - DD.MM.YYYY (z.B. 03.08.2011)', '[[date.format(d.m.Y)]]', 'date'),
            (array) new Item('Aktuelles Datum - DD.MM.YYYY H:i (z.B. 03.08.2011 13:37)', '[[date.format(d.m.Y H:i)]]', 'date'),
            (array) new Item('Aktuelles Datum - DD.MM.YYYY H:M:S (z.B. 03.08.2011 13:37:00)', '[[date.format(d.m.Y H:i:s)]]', 'date'),
            (array) new Item('Aktuelles Datum - DD-MM-YYYY (z.B. 03-08-2011)', '[[date.format(d-m-Y)]]', 'date'),
            (array) new Item('Aktuelles Datum - DD-MM-YYYY H:M (z.B. 03-08-2011 13:37)', '[[date.format(d-m-Y H:i)]]', 'date'),
            (array) new Item('Aktuelles Datum - DD-MM-YYYY H:M:S (z.B. 03-08-2011 13:37:00)', '[[date.format(d-m-Y H:i:s)]]', 'date'),
            (array) new Item('Aktuelles Datum plus 1 Tag (z.B. 04.08.2011)', '[[date.add(days=1).format(d.m.Y)]]', 'date'),
            (array) new Item('Aktuelles Datum plus 2 Tage (z.B. 05.08.2011)', '[[date.add(days=2).format(d.m.Y)]]', 'date'),
            (array) new Item('Aktuelles Datum plus 1 Woche (z.B. 10.08.2011)', '[[date.add(weeks=1).format(d.m.Y)]]', 'date'),
            (array) new Item('Aktuelles Datum plus 2 Wochen (z.B. 17.08.2011)', '[[date.add(weeks=2).format(d.m.Y)]]', 'date'),
            (array) new Item('Aktuelles Datum plus 1 Monat (z.B. 03.09.2011)', '[[date.add(months=1).format(d.m.Y)]]', 'date'),
            (array) new Item('Aktuelles Datum minus 1 Tag (z.B. 02.08.2011)', '[[date.sub(days=1).format(d.m.Y)]]', 'date'),
            (array) new Item('Aktuelles Datum minus 2 Tage (z.B. 01.08.2011)', '[[date.sub(days=2).format(d.m.Y)]]', 'date'),
            (array) new Item('Aktuelles Datum minus 1 Woche (z.B. 27.07.2011)', '[[date.sub(weeks=1).format(d.m.Y)]]', 'date'),
            (array) new Item('Aktuelles Datum minus 2 Wochen (z.B. 20.07.2011)', '[[date.sub(weeks=2).format(d.m.Y)]]', 'date'),
            (array) new Item('Aktuelles Datum minus 1 Monat (z.B. 03.07.2011)', '[[date.sub(months=1).format(d.m.Y)]]', 'date'),
            (array) new Item('Vierstellige Jahreszahl, z.B. 2021', '[[date.format(Y)]]', 'date'),
            (array) new Item('Zweistellige Jahreszahl, z.B. 21', '[[date.format(y)]]', 'date'),
            (array) new Item('Monatszahl mit führender Null, z.B. 02', '[[date.format(m)]]', 'date'),
            (array) new Item('Monatszahl ohne führender Null, z.B. 2', '[[date.format(n)]]', 'date'),
            (array) new Item('Monatstag mit führender Null, z.B. 09', '[[date.format(d)]]', 'date'),
            (array) new Item('Monatstag ohne führender Null, z.B. 09', '[[date.format(j)]]', 'date'),
            (array) new Item('Stundenzahl mit führender Null, z.B. 09', '[[date.format(H)]]', 'date'),
            (array) new Item('Stundenzahl ohne führender Null, z.B. 9', '[[date.format(G)]]', 'date'),
            (array) new Item('Minutenzahl mit führender Null, z.B. 09', '[[date.format(i)]]', 'date'),
            (array) new Item('UNIX Zeitstempel in Sekunden', '[[date.format(timestamp)]]', 'date'),
        ];
    }
}
