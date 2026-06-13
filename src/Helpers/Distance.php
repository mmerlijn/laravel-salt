<?php

namespace mmerlijn\LaravelSalt\Helpers;


use mmerlijn\LaravelSalt\Enums\DistanceUnitsEnum;
use mmerlijn\LaravelSalt\Exceptions\DistanceException;

class Distance
{
    private float $from_latitude = 0;
    private float $from_longitude = 0;
    private float $to_latitude = 0;
    private float $to_longitude = 0;

    public function __constructor()
    {
    }

    /**
     * From city/coordinates
     *
     * @param float|string|array $param1
     * @param float|string|null $param2
     * @return $this
     * @throws DistanceException
     */
    public function from(float|string|array $param1, float|string|null $param2 = null): self
    {
        $this->handleInput($param1, $param2, "from");
        return $this;
    }


    /**
     * To city/coordinates
     *
     * @param float|string|array $param1
     * @param float|string|null $param2
     * @return $this
     * @throws DistanceException
     */
    public function to(float|string|array $param1, float|string|null $param2 = null): self
    {
        $this->handleInput($param1, $param2, "to");
        return $this;
    }


    /**
     * Get distance
     *
     * W@param DistanceUnitsEnum $unit
     * @param bool $format
     * @param int $precision
     * @return float|string
     * @throws DistanceException
     */
    public function get(DistanceUnitsEnum $unit = DistanceUnitsEnum::KM, bool $format = false, int $precision = 1): float|string
    {
        if ($this->from_latitude and $this->from_longitude and $this->to_latitude and $this->to_longitude) {
            if ($format) {
                return number_format($this->distance() / $unit->value, $precision, ",", ".") . $unit->getUnit();
            } else {
                return round($this->distance() / $unit->value, $precision);
            }

        }
        throw new DistanceException('Not all coordinates are set');
    }


    /**
     * @param float $lat
     * @param float $long
     * @return $this
     */
    public function setFrom(float $lat, float $long): self
    {
        $this->from_latitude = $lat;
        $this->from_longitude = $long;
        return $this;
    }


    /**
     * @param float $lat
     * @param float $long
     * @return $this
     */
    public function setTo(float $lat, float $long): self
    {
        $this->to_latitude = $lat;
        $this->to_longitude = $long;
        return $this;
    }


    /**
     * @param $lat
     * @param $long
     * @param $type
     * @return void
     * @throws DistanceException
     */
    private function handleInput($lat, $long, $type): void
    {
        if (gettype($lat) == "string") {
            if (is_numeric($lat)) {
                if (!is_numeric($long)) {
                    throw new DistanceException('From/To longitude: ' . $long . " is not valid");
                }
            } else {//city
                list($lat, $long) = CityCoordinates::from($lat);
            }
        } elseif (gettype($lat) == "double") {
            if (!is_numeric($long)) {
                throw new DistanceException('From/To longitude: ' . $long . " is not valid");
            }
        } elseif (gettype($lat) == "array") {
            if (!$lat['lat'] or !$lat['long']) {
                throw new DistanceException('From/To input array doesnt contain keys lat and long');
            }
            $long = $lat['long'];
            $lat = $lat['lat'];
        }
        if ($type == "to") {
            $this->setTo((float)$lat, (float)$long);
        } else {
            $this->setFrom((float)$lat, (float)$long);
        }
    }


    /**
     * @return float
     */
    private function distance(): float
    {
        return $this->vincentyGreatCircleDistance($this->from_latitude, $this->from_longitude, $this->to_latitude, $this->to_longitude);
    }


    /**
     * @param float $latitudeFrom
     * @param float $longitudeFrom
     * @param float $latitudeTo
     * @param float $longitudeTo
     * @param $earthRadius
     * @return float|int
     */
    private function vincentyGreatCircleDistance(
        float $latitudeFrom, float $longitudeFrom, float $latitudeTo, float $longitudeTo, $earthRadius = 6371000): float|int
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
            pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);
        return $angle * $earthRadius;
    }

}