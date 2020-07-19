<?php

namespace PSX\Data\Tests\Exporter;

/**
 * @JS\Title("location")
 * @JS\Description("Location of the person")
 * @JS\AdditionalProperties(true)
 * @JS\Required({"lat", "long"})
 */
class Location
{
    /**
     * @var float
     * @JS\Key("lat")
     */
    public $lat;
    /**
     * @var float
     * @JS\Key("long")
     */
    public $long;

    public function __construct($lat, $long)
    {
        $this->lat  = $lat;
        $this->long = $long;
    }

    public function setLat($lat)
    {
        $this->lat = $lat;
    }

    public function getLat()
    {
        return $this->lat;
    }

    public function setLong($long)
    {
        $this->long = $long;
    }

    public function getLong()
    {
        return $this->long;
    }
}
