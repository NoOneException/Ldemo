<?php

namespace response;

use DOMDocument;
use L\response\Response;

class XmlData extends Response
{
    private $data;

    public function __construct(array $data = [])
    {
        $this->data = array_merge([
            'ok' => true,
            'servertime' => time(),
        ], $data);
    }

    protected function onGetContent(): string
    {
        return $this->arrayToXml($this->data);
    }

    private function arrayToXml(array $arr, $dom = null, $item = null)
    {
        if (!$dom) {
            $dom = new DOMDocument("1.0");
        }
        if (!$item) {
            $item = $dom->createElement("root");
            $dom->appendChild($item);
        }
        foreach ($arr as $key => $val) {
            $itemx = $dom->createElement(is_string($key) ? $key : "item");
            $item->appendChild($itemx);
            if (!is_array($val)) {
                $text = $dom->createTextNode($val);
                $itemx->appendChild($text);
            } else {
                $this->arrayToXml($val, $dom, $itemx);
            }
        }
        return $dom->saveXML();
    }

    public function getData()
    {
        return $this->data;
    }


}