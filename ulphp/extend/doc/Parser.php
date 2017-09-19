<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/9/18
 * Time: 16:46
 */

namespace ulphp\extend\doc;

class Parser
{
    /**
     * The string that we want to parse
     */
    private $string;

    public function __construct($string)
    {
        $this->string = $string;

    }

    /**
     * @return array
     */
    public function parse()
    {
        if (!preg_match('#^/\*\*(.*)\*/#s', $this->string, $comment))
            return [];
        $comment = trim($comment[1]);
        if (preg_match_all('#^\s*\*(.*)#m', $comment, $lines))
            return [];
        $lines = $lines[1];

        $parserValues = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            $params = preg_split('/\s+/is', $line);
            if ($params[0] == '@param') {
                $parserValue        = new ParserValue();
                $parserValue->name  = str_replace('$', '', $params[2]);
                $parserValue->types = explode('|', $params[1]);
                $parserValue->desc  = isset($params[3]) ? $params[3] : '';
                $parserValues[]     = $parserValue;
            }
        }

        return $parserValues;
    }
}

class ParserValue
{
    public $name  = '';
    public $types = [];
    public $desc  = '';
}