<?php

namespace response;

use L\response\Response;

class FileData extends Response
{

    private $filename;
    private $showname;

    public function __construct(string $filename, $showname = null)
    {
        if (!is_file($filename)) {
            throw new \ErrorException();
        }
        $this->filename = $filename;
        $this->showname = $showname ?? pathinfo($filename, PATHINFO_BASENAME);
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    protected function onGetContent(): string
    {
        if (file_exists($this->filename)) {
            if (false !== ($file = fopen($this->filename, 'r'))) {
                while (!feof($file)) {
                    echo fgets($file, 4096);
                }
            }
        }
        return '';
    }

    public function getHeaders(): array
    {
        return [
            'Content-Description' => 'File Transfer',
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename=' . $this->showname,
            'Content-Transfer-Encoding' => 'chunked',
            'Expires' => gmdate('D, d M Y H:i:s T'),
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Pragma' => 'public',
            'Content-Length' => filesize($this->filename),
        ];
    }
}