<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace client\components;
use yii\base\Component;

use DOMDocument;
use DOMElement;
use DOMException;
use DOMText;
use yii\base\Arrayable;
use yii\helpers\StringHelper;

/**
 * Description of XMLFormatter
 *
 * @author avelare
 */
class XMLFormatter extends Component {
    /**
     * @var string the Content-Type header for the response
     */
    public $contentType = 'application/xml';
    /**
     * @var string the XML version
     */
    public $version = '1.0';
    /**
     * @var string the XML encoding. If not set, it will use the value of [[Response::charset]].
     */
    public $encoding;
    /**
     * @var string the name of the root element. If set to false, null or is empty then no root tag should be added.
     */
    public $rootTag = 'response';
    /**
     * @var string the name of the elements that represent the array elements with numeric keys.
     */
    public $itemTag = 'item';
    /**
     * @var bool whether to interpret objects implementing the [[\Traversable]] interface as arrays.
     * Defaults to `true`.
     * @since 2.0.7
     */
    public $useTraversableAsArray = true;
    /**
     * @var bool if object tags should be added
     * @since 2.0.11
     */
    public $useObjectTags = true;
    
    /*
     * @var XMLResponse response;
     */
    public $response;
    
    
    /**
     * Formats the specified response.
     * @param XMLResponse $response the response to be formatted.
     */
    public function format(&$response)
    {
        $this->response = $response;
        $charset = $this->encoding === null ? $response->charset : $this->encoding;
        if (stripos($this->contentType, 'charset') === false) {
            $this->contentType .= '; charset=' . $charset;
        }
        $response->setHeaders('Content-Type', $this->contentType);
        if ($response->data !== null) {
            $dom = new DOMDocument($this->version, $charset);
            if (!empty($this->rootTag)) {
                $root = new DOMElement($this->rootTag);
                $dom->appendChild($root);
                $this->buildXml($root, $response->data);
            } else {
                $this->buildXml($dom, $response->data);
            }
            $response->content = $dom->saveXML();
            $response->dom = $dom;
        }
    }

    /**
     * @param DOMElement $element
     * @param mixed $data
     */
    protected function buildXml($element, $data)
    {
        if (is_array($data) ||
            ($data instanceof \Traversable && $this->useTraversableAsArray && !$data instanceof Arrayable)
        ) {
            foreach ($data as $name => $value) {
                if (is_int($name) && is_object($value)) {
                    $this->buildXml($element, $value);
                } 
                #elseif(is_int($name) || !$this->isValidXmlName($value)){
                #    $this->buildXml($element, $value);
                #} 
                elseif (is_array($value) || is_object($value)) {
                    $child = new DOMElement($this->getValidXmlElementName($name));
                    $element->appendChild($child);
                    $this->buildXml($child, $value);
                } else {
                    if($this->response->isAttribute($name)){
                        $element->setAttribute($name, $value);
                    } else{
                        $child = new DOMElement($this->getValidXmlElementName($name));
                        $element->appendChild($child);
                        $child->appendChild(new DOMText($this->formatScalarValue($value)));
                    }
                }
            }
        } elseif (is_object($data)) {
            if ($this->useObjectTags) {
                $child = new DOMElement(StringHelper::basename(get_class($data)));
                $element->appendChild($child);
            } else {
                $child = $element;
            }
            if ($data instanceof Arrayable) {
                $this->buildXml($child, $data->toArray());
            } else {
                $array = [];
                foreach ($data as $name => $value) {
                    $array[$name] = $value;
                }
                $this->buildXml($child, $array);
            }
        } else {
            $element->appendChild(new DOMText($this->formatScalarValue($data)));
        }
    }

    /**
     * Formats scalar value to use in XML text node.
     *
     * @param int|string|bool|float $value a scalar value.
     * @return string string representation of the value.
     * @since 2.0.11
     */
    protected function formatScalarValue($value)
    {
        if ($value === true) {
            return 'true';
        }
        if ($value === false) {
            return 'false';
        }
        if (is_float($value)) {
            return StringHelper::floatToString($value);
        }
        return (string) $value;
    }

    /**
     * Returns element name ready to be used in DOMElement if
     * name is not empty, is not int and is valid.
     *
     * Falls back to [[itemTag]] otherwise.
     *
     * @param mixed $name
     * @return string
     * @since 2.0.12
     */
    protected function getValidXmlElementName($name)
    {
        if (empty($name) || is_int($name) || !$this->isValidXmlName($name)) {
            return $this->itemTag;
        }

        return $name;
    }

    /**
     * Checks if name is valid to be used in XML.
     *
     * @param mixed $name
     * @return bool
     * @see http://stackoverflow.com/questions/2519845/how-to-check-if-string-is-a-valid-xml-element-name/2519943#2519943
     * @since 2.0.12
     */
    protected function isValidXmlName($name)
    {
        try {
            new DOMElement($name);
            return true;
        } catch (DOMException $e) {
            return false;
        }
    }
}
