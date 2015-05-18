<?php
/**
 * ExternalResource.php
 * @author Revin Roman http://phptime.ru
 */

namespace rmrevin\yii\module\File\resources;

/**
 * Class ExternalResource
 * @package rmrevin\yii\module\File\resources
 */
class ExternalResource extends AbstractResource implements ResourceInterface
{

    /** @var string */
    private $temp;

    /** @var string */
    private $source;

    /**
     * @param string $source
     */
    public function setSource($source)
    {
        $this->source = $source;
        $this->temp = $this->copyTempResource();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return basename($this->source);
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return filesize($this->temp);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getMime()
    {
        return \yii\helpers\FileHelper::getMimeType($this->temp);
    }

    /**
     * @return string
     */
    public function getTemp()
    {
        return $this->temp;
    }

    /**
     * @return string|false
     */
    private function copyTempResource()
    {
        if (false === $this->checkAvailable()) {
            throw new \RuntimeException('External resource not available');
        }

        $url = parse_url($this->source);
        $ext = pathinfo($url['path'], PATHINFO_EXTENSION);
        $temp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('file-') . '.' . $ext;

        $result = copy($this->source, $temp);
        @chmod($temp, 0664);

        return true === $result ? $temp : false;
    }

    /**
     * @return bool
     */
    private function checkAvailable()
    {
        $result = false;

        $ch = curl_init($this->source);
        curl_setopt_array($ch, [
            CURLOPT_HEADER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        curl_exec($ch);

        if (!curl_errno($ch)) {
            $info = curl_getinfo($ch);

            if ($info['http_code'] === 200) {
                $result = true;
            }
        }

        curl_close($ch);

        return $result;
    }
}