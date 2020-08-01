<?php
namespace Vexsoluciones\Module\Glovo;

/**
 * Class TimestampWorking
 * Created to handle Local TimeZone with UTC TimeZone that Glovo need to works successfully.
 *
 * @property \DateTimeZone $localTimeZone
 * @property \DateTime $dateUtc
 * @property \DateTime $dateLocal
 *
 * @package Vexsoluciones\Module\Glovo
 */
class TimestampWorking
{
    protected $localTimeZone;
    protected $requiredTimeZone = 'UTC';

    public $dateUtc;
    public $dateLocal;

    public function __construct(\DateTimeZone $dateTime)
    {
        $this->localTimeZone = $dateTime;
    }

    /**
     * Creates a new instance of class with LocalTimeZone.
     *
     * @return TimestampWorking
     * @throws \Exception
     */
    public static function createFromLocalTimeZone()
    {
        return new static((new \DateTime())->getTimezone());
    }

    /**
     * Get local format and time and returns UTC timestamp.
     *
     * Example:
     *      params: [d-m-y H:i:s], [21-07-19 8:00:00]
     *      returns: 1563714000 - 07/21/2019 @ 1:00pm (UTC)
     *
     * @param $format
     * @param $time
     * @return int
     */
    public function timestamp($format, $time)
    {
        $this->dateLocal = \DateTime::createFromFormat($format, $time, $this->localTimeZone);

        return $this->dateLocal->getTimestamp();
    }

    /**
     * Converts timestamp from LocalTimeZone to UTC TimeZone.
     *
     * Example:
     *      param: 1563664990 - 07/20/2019 @ 11:23pm (UTC)
     *      return: 1563682990 - 07/21/2019 @ 4:23am (UTC)
     *
     * @param $timestamp
     * @return int
     * @throws \Exception
     */
    public function fromTimestamp($timestamp)
    {
        $this->dateLocal = new \DateTime();
        $this->dateLocal->setTimestamp($timestamp)->setTimezone(new \DateTimeZone($this->requiredTimeZone));

        $this->dateUtc = \DateTime::createFromFormat('d-m-Y H:i:s', $this->dateLocal->format('d-m-Y H:i:s'), $this->localTimeZone);

        return $this->dateUtc->getTimestamp();
    }
}