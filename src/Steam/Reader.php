<?php

namespace App\Steam;

/**
 * Class Reader.
 */
class Reader
{
    /**
     * @param string $url
     *
     * @return string
     *
     * @throws \Exception
     */
    public function get($url)
    {
        if (false === $content = file_get_contents($url, false, $this->getStreamContext())) {
            $error = error_get_last();
            throw new \Exception("HTTP request failed. Error was: " . $error['message']);
        }
        return $content;
    }

    /**
     * @return resource
     */
    protected function getStreamContext()
    {
        return stream_context_create([
            'http' => [ 'ignore_errors' => true, ]
        ]);
    }
}
