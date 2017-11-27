<?php

namespace PSX\Data\Tests\Exporter;

use PSX\Schema\Parser\Popo\Annotation as JS;

/**
 * @JS\Title("location")
 * @JS\Description("Location of the person")
 * @JS\AdditionalProperties(true)
 * @JS\Required({"lat", "long"})
 */
class Location
{
    /**
     * @JS\Key("lat")
     * @JS\Type("number")
     */
    public $lat;
    /**
     * @JS\Key("long")
     * @JS\Type("number")
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
