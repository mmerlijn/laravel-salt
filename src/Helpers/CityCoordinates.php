<?php

namespace mmerlijn\LaravelSalt\Helpers;


use mmerlijn\LaravelSalt\Exceptions\DistanceException;

class CityCoordinates
{

    /**
     * @param $city
     * @return array
     * @throws DistanceException
     */
    public static function from($city): array
    {
        return match (strtolower($city)) {
            "purmerend" => [52.5144, 4.9641],
            "monnickendam" => [52.4555687, 5.0392316],
            "marken" => [52.4589926, 5.1032057],
            "edam" => [52.5126367, 5.0491819],
            "graft" => [52.5622696, 4.832044],
            "hobrede" => [52.5780353, 4.9986224],
            "katwoude" => [52.4694862, 5.0471278],
            "kwadijk" => [52.527382, 4.9837605],
            "noordbeemster" => [52.5797174, 4.9322059],
            "westbeemster" => [52.5752469, 4.8995178],
            "middenbeemster" => [52.548942, 4.9154702],
            "oosthuizen" => [52.5739553, 4.9975843],
            "purmer" => [52.4844342, 4.961566],
            "purmerland" => [52.483289, 4.909167],
            "volendam" => [52.4968694, 5.0727015],
            "wijdewormer" => [52.4873869, 4.8597355],
            "zuidoostbeemster" => [52.5151991, 4.9421222],
            "hoorn" => [52.6423654, 5.0602124],
            "zwaag" => [52.6671969, 5.073672],
            "amstelveen" => [52.3031178, 4.8611997],
            "broek in waterland" => [52.4357808, 4.991315,],
            "amsterdam" => [52.3702157, 4.8951679],
            "diemen" => [52.3389926, 4.9591888],
            "ilpendam" => [52.46591995, 4.96990604343952],
            "landsmeer" => [52.4403382, 4.9209233],
            "watergang" => [52.4398855, 4.9521153],
            "halfweg" => [52.3819918, 4.7538293],
            "castricum" => [52.5452585, 4.6727354],
            "uitgeest" => [52.5312254, 4.7120459],
            "akersloot" => [52.5609159, 4.7338008],
            "beverwijk" => [52.4869842, 4.6574468],
            "heemskerk" => [52.514146, 4.6821367],
            "alkmaar" => [52.6323813, 4.7533754],
            "egmond aan den hoef" => [52.6209748, 4.65312, 5],
            "egmond aan zee" => [52.6186114, 4.6302431],
            "egmond-binnen" => [52.5938149, 4.6560387],
            "groet" => [52.7220044, 4.6670183],
            "heerhugowaard" => [52.662677, 4.8324767],
            "heiloo" => [52.6012341, 4.7004931],
            "limmen" => [52.5715831, 4.6942467],
            "zuidschermer" => [52.5843905, 4.7812001],
            "ijmuiden" => [52.4569544, 4.6060138],
            "assendelft" => [52.4870604, 4.7560638],
            "jisp" => [52.5078271, 4.8483414],
            "koog aan de zaan" => [52.4607871, 4.8047292],
            "krommenie" => [52.5034775, 4.7571696],
            "oostzaan" => [52.4376266, 4.8756005],
            "west-grafdijk" => [52.5542536, 4.7950025],
            "westknollendam" => [52.515415, 4.77787],
            "westzaan" => [52.4642168, 4.7719876],
            "wormer" => [52.4986623, 4.8124621],
            "wormerveer" => [52.4903502, 4.7980674],
            "zaandam" => [52.4420399, 4.8291992],
            "zaandijk" => [52.4740433, 4.8028179],
            "zaanstad" => [52.4579659, 4.7510425],
            "haarlem" => [52.3873878, 4.6462194],
            "heemstede" => [52.3510634, 4.6203004],
            default => throw new DistanceException('City: ' . $city . " not exists in predefined cities"),
        };
    }
}