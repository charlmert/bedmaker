<?php
namespace BedMaker;

use Illuminate\Support\Arr;
use BedMaker\Exception\ConfigException;

class Config
{
    /**
     * The config array
     * @var Arr
     */
    protected $config;

    /**
     * Constructs the configuration object.
     *
     * @param array $config The config array.
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get the configuration defined at a certain key/index using dot notation.
     *
     * @param string $dot The dot notation to the array element.
     *
     * @return array
     */
    public function get(string $dot)
    {
        $result = Arr::get($this->config, $dot);
        if ($result === null) {
            throw new ConfigException('No configuration defined at [' . $dot . ']');
        }
        return $result;
    }

    /**
     *
     * @param string $dot The dot notation to the array element.
     * @param array  $dot The dot notation to the array element.
     *
     * @return void
     */
    public function set(string $dot, array $config)
    {
        $this->config = Arr::set($this->config, $dot, $config);
    }

    /**
     * Return the original array.
     *
     */
    public function toArray()
    {
        return $this->config;
    }
}
