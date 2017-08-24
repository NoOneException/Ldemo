<?php

namespace response;

use L\response\Response;

class FileData extends Response
{

    private $filename;
    private $showname;

    public function __construct(string $filename, $showname = null)
    {
        $this->filename = $filename;
        $this->showname = $showname ?? pathinfo($filename, PATHINFO_BASENAME);
        $this->setHeaders([
            'Content-Description' => 'File Transfer',
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename=' . $this->showname,
            'Content-Transfer-Encoding' => 'binary',
            'Expires' => '0',
            'Cache-Control' => 'must-revalidate',
            'Pragma' => 'public',
            'Content-Length' => filesize($this->filename),
        ]);
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
        return file_get_contents($this->filename);
    }
}