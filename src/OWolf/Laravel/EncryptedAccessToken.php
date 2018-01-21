<?php

namespace OWolf\Laravel;

use Illuminate\Contracts\Support\Htmlable;

class EncryptedAccessToken implements Htmlable
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $payload;

    /**
     * EncryptedAccessToken constructor.
     * @param string $payload
     * @param string $key
     */
    public function __construct($payload, $key = 'oauth')
    {
        $this->payload = $payload;
        $this->key     = $key;
    }

    /**
     * @return string
     */
    public function key()
    {
        return $this->getKey();
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param  string  $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $name = htmlspecialchars($this->key, ENT_QUOTES, 'UTF-8', false);
        $payload = htmlspecialchars($this->payload, ENT_QUOTES, 'UTF-8', false);
        return '<input type="hidden" name="' . $name . '" value="' . $payload . '">';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->payload;
    }
}