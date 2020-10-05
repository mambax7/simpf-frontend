<?php

// $Id:$
// ------------------------------------------------------------------------- //
//  Generic XML-RPC Library for PHP                                          //
//  Copyright (C) 2009 RIKEN BSI Neuroinformatics Japan Center               //
//  All rights reserved.                                                     //
//  http://nijc.brain.riken.jp/                                              //
// ------------------------------------------------------------------------- //
//  Redistribution and use in source and binary forms, with or without       //
//  modification, are permitted provided that the following conditions       //
//  are met:                                                                 //
//                                                                           //
//  1. Redistributions of source code must retain the above copyright        //
//     notice, this list of conditions and the following disclaimer.         //
//                                                                           //
//  2. Redistributions in binary form must reproduce the above copyright     //
//     notice, this list of conditions and the following disclaimer in the   //
//     documentation and/or other materials provided with the distribution.  //
//                                                                           //
//  THIS SOFTWARE IS PROVIDED BY RIKEN AND CONTRIBUTORS ``AS IS'' AND ANY    //
//  EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE        //
//  IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR       //
//  PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL RIKEN OR CONTRIBUTORS BE      //
//  LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR      //
//  CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF     //
//  SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS //
//  INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN  //
//  CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)  //
//  ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF   //
//  THE POSSIBILITY OF SUCH DAMAGE.                                          //
// ------------------------------------------------------------------------- //
define('XMLRPC_VALUE_TYPE_ARRAY', 1);
define('XMLRPC_VALUE_TYPE_BASE64', 2);
define('XMLRPC_VALUE_TYPE_BOOLEAN', 3);
define('XMLRPC_VALUE_TYPE_DATETIME', 4);
define('XMLRPC_VALUE_TYPE_DOUBLE', 5);
define('XMLRPC_VALUE_TYPE_INTEGER', 6);
define('XMLRPC_VALUE_TYPE_STRING', 7);
define('XMLRPC_VALUE_TYPE_STRUCT', 8);
define('XMLRPC_VALUE_TYPE_NIL', 9);

/**
 * xmlrpc utility class.
 *
 * @copyright copyright &copy; 2009 RIKEN BSI Neuroinformatics Japan Center
 */
class XMLRPC_Utility
{
    /**
     * html character entity references.
     *
     * @var array strings of html character entity reference
     */
    public $_html_char_entity_ref = [
        '&quot;',     '&amp;',      '&apos;',     '&lt;',       '&gt;',
        '&nbsp;',     '&iexcl;',    '&cent;',     '&pound;',    '&curren;',
        '&yen;',      '&brvbar;',   '&sect;',     '&uml;',      '&copy;',
        '&ordf;',     '&laquo;',    '&not;',      '&shy;',      '&reg;',
        '&macr;',     '&deg;',      '&plusmn;',   '&sup2;',     '&sup3;',
        '&acute;',    '&micro;',    '&para;',     '&middot;',   '&cedil;',
        '&sup1;',     '&ordm;',     '&raquo;',    '&frac14;',   '&frac12;',
        '&frac34;',   '&iquest;',   '&Agrave;',   '&Aacute;',   '&Acirc;',
        '&Atilde;',   '&Auml;',     '&Aring;',    '&AElig;',    '&Ccedil;',
        '&Egrave;',   '&Eacute;',   '&Ecirc;',    '&Euml;',     '&Igrave;',
        '&Iacute;',   '&Icirc;',    '&Iuml;',     '&ETH;',      '&Ntilde;',
        '&Ograve;',   '&Oacute;',   '&Ocirc;',    '&Otilde;',   '&Ouml;',
        '&times;',    '&Oslash;',   '&Ugrave;',   '&Uacute;',   '&Ucirc;',
        '&Uuml;',     '&Yacute;',   '&THORN;',    '&szlig;',    '&agrave;',
        '&aacute;',   '&acirc;',    '&atilde;',   '&auml;',     '&aring;',
        '&aelig;',    '&ccedil;',   '&egrave;',   '&eacute;',   '&ecirc;',
        '&euml;',     '&igrave;',   '&iacute;',   '&icirc;',    '&iuml;',
        '&eth;',      '&ntilde;',   '&ograve;',   '&oacute;',   '&ocirc;',
        '&otilde;',   '&ouml;',     '&divide;',   '&oslash;',   '&ugrave;',
        '&uacute;',   '&ucirc;',    '&uuml;',     '&yacute;',   '&thorn;',
        '&yuml;',     '&OElig;',    '&oelig;',    '&Scaron;',   '&scaron;',
        '&Yuml;',     '&fnof;',     '&circ;',     '&tilde;',    '&Alpha;',
        '&Beta;',     '&Gamma;',    '&Delta;',    '&Epsilon;',  '&Zeta;',
        '&Eta;',      '&Theta;',    '&Iota;',     '&Kappa;',    '&Lambda;',
        '&Mu;',       '&Nu;',       '&Xi;',       '&Omicron;',  '&Pi;',
        '&Rho;',      '&Sigma;',    '&Tau;',      '&Upsilon;',  '&Phi;',
        '&Chi;',      '&Psi;',      '&Omega;',    '&alpha;',    '&beta;',
        '&gamma;',    '&delta;',    '&epsilon;',  '&zeta;',     '&eta;',
        '&theta;',    '&iota;',     '&kappa;',    '&lambda;',   '&mu;',
        '&nu;',       '&xi;',       '&omicron;',  '&pi;',       '&rho;',
        '&sigmaf;',   '&sigma;',    '&tau;',      '&upsilon;',  '&phi;',
        '&chi;',      '&psi;',      '&omega;',    '&thetasym;', '&upsih;',
        '&piv;',      '&ensp;',     '&emsp;',     '&thinsp;',   '&zwnj;',
        '&zwj;',      '&lrm;',      '&rlm;',      '&ndash;',    '&mdash;',
        '&lsquo;',    '&rsquo;',    '&sbquo;',    '&ldquo;',    '&rdquo;',
        '&bdquo;',    '&dagger;',   '&Dagger;',   '&bull;',     '&hellip;',
        '&permil;',   '&prime;',    '&Prime;',    '&lsaquo;',   '&rsaquo;',
        '&oline;',    '&frasl;',    '&euro;',     '&image;',    '&weierp;',
        '&real;',     '&trade;',    '&alefsym;',  '&larr;',     '&uarr;',
        '&rarr;',     '&darr;',     '&harr;',     '&crarr;',    '&lArr;',
        '&uArr;',     '&rArr;',     '&dArr;',     '&hArr;',     '&forall;',
        '&part;',     '&exist;',    '&empty;',    '&nabla;',    '&isin;',
        '&notin;',    '&ni;',       '&prod;',     '&sum;',      '&minus;',
        '&lowast;',   '&radic;',    '&prop;',     '&infin;',    '&ang;',
        '&and;',      '&or;',       '&cap;',      '&cup;',      '&int;',
        '&there4;',   '&sim;',      '&cong;',     '&asymp;',    '&ne;',
        '&equiv;',    '&le;',       '&ge;',       '&sub;',      '&sup;',
        '&nsub;',     '&sube;',     '&supe;',     '&oplus;',    '&otimes;',
        '&perp;',     '&sdot;',     '&lceil;',    '&rceil;',    '&lfloor;',
        '&rfloor;',   '&lang;',     '&rang;',     '&loz;',      '&spades;',
        '&clubs;',    '&hearts;',   '&diams;',
    ];

    /**
     * html numeric character references.
     *
     * @var array strings of html numeric character reference
     */
    public $_html_numeric_char_ref = [
        '&#34;',   '&#38;',   '&#39;',   '&#60;',   '&#62;',
        '&#160;',  '&#161;',  '&#162;',  '&#163;',  '&#164;',
        '&#165;',  '&#166;',  '&#167;',  '&#168;',  '&#169;',
        '&#170;',  '&#171;',  '&#172;',  '&#173;',  '&#174;',
        '&#175;',  '&#176;',  '&#177;',  '&#178;',  '&#179;',
        '&#180;',  '&#181;',  '&#182;',  '&#183;',  '&#184;',
        '&#185;',  '&#186;',  '&#187;',  '&#188;',  '&#189;',
        '&#190;',  '&#191;',  '&#192;',  '&#193;',  '&#194;',
        '&#195;',  '&#196;',  '&#197;',  '&#198;',  '&#199;',
        '&#200;',  '&#201;',  '&#202;',  '&#203;',  '&#204;',
        '&#205;',  '&#206;',  '&#207;',  '&#208;',  '&#209;',
        '&#210;',  '&#211;',  '&#212;',  '&#213;',  '&#214;',
        '&#215;',  '&#216;',  '&#217;',  '&#218;',  '&#219;',
        '&#220;',  '&#221;',  '&#222;',  '&#223;',  '&#224;',
        '&#225;',  '&#226;',  '&#227;',  '&#228;',  '&#229;',
        '&#230;',  '&#231;',  '&#232;',  '&#233;',  '&#234;',
        '&#235;',  '&#236;',  '&#237;',  '&#238;',  '&#239;',
        '&#240;',  '&#241;',  '&#242;',  '&#243;',  '&#244;',
        '&#245;',  '&#246;',  '&#247;',  '&#248;',  '&#249;',
        '&#250;',  '&#251;',  '&#252;',  '&#253;',  '&#254;',
        '&#255;',  '&#338;',  '&#339;',  '&#352;',  '&#353;',
        '&#376;',  '&#402;',  '&#710;',  '&#732;',  '&#913;',
        '&#914;',  '&#915;',  '&#916;',  '&#917;',  '&#918;',
        '&#919;',  '&#920;',  '&#921;',  '&#922;',  '&#923;',
        '&#924;',  '&#925;',  '&#926;',  '&#927;',  '&#928;',
        '&#929;',  '&#931;',  '&#932;',  '&#933;',  '&#934;',
        '&#935;',  '&#936;',  '&#937;',  '&#945;',  '&#946;',
        '&#947;',  '&#948;',  '&#949;',  '&#950;',  '&#951;',
        '&#952;',  '&#953;',  '&#954;',  '&#955;',  '&#956;',
        '&#957;',  '&#958;',  '&#959;',  '&#960;',  '&#961;',
        '&#962;',  '&#963;',  '&#964;',  '&#965;',  '&#966;',
        '&#967;',  '&#968;',  '&#969;',  '&#977;',  '&#978;',
        '&#982;',  '&#8194;', '&#8195;', '&#8201;', '&#8204;',
        '&#8205;', '&#8206;', '&#8207;', '&#8211;', '&#8212;',
        '&#8216;', '&#8217;', '&#8218;', '&#8220;', '&#8221;',
        '&#8222;', '&#8224;', '&#8225;', '&#8226;', '&#8230;',
        '&#8240;', '&#8242;', '&#8243;', '&#8249;', '&#8250;',
        '&#8254;', '&#8260;', '&#8364;', '&#8465;', '&#8472;',
        '&#8476;', '&#8482;', '&#8501;', '&#8592;', '&#8593;',
        '&#8594;', '&#8595;', '&#8596;', '&#8629;', '&#8656;',
        '&#8657;', '&#8658;', '&#8659;', '&#8660;', '&#8704;',
        '&#8706;', '&#8707;', '&#8709;', '&#8711;', '&#8712;',
        '&#8713;', '&#8715;', '&#8719;', '&#8721;', '&#8722;',
        '&#8727;', '&#8730;', '&#8733;', '&#8734;', '&#8736;',
        '&#8743;', '&#8744;', '&#8745;', '&#8746;', '&#8747;',
        '&#8756;', '&#8764;', '&#8773;', '&#8776;', '&#8800;',
        '&#8801;', '&#8804;', '&#8805;', '&#8834;', '&#8835;',
        '&#8836;', '&#8838;', '&#8839;', '&#8853;', '&#8855;',
        '&#8869;', '&#8901;', '&#8968;', '&#8969;', '&#8970;',
        '&#8971;', '&#9001;', '&#9002;', '&#9674;', '&#9824;',
        '&#9827;', '&#9829;', '&#9830;',
    ];

    /**
     * constructor.
     *
     * @return class instance
     */
    public function __construct()
    {
    }

    /**
     * get utility instance.
     *
     * @return class instance
     */
    public static function &getInstance()
    {
        static $instance = false;
        if (false !== $instance) {
            return $instance;
        }
        $instance = new self();

        return $instance;
    }

    /**
     * get value type by tag name.
     *
     * @return int value type
     */
    public function get_value_type($name)
    {
        static $types = [
            'array' => XMLRPC_VALUE_TYPE_ARRAY,
            'base64' => XMLRPC_VALUE_TYPE_BASE64,
            'boolean' => XMLRPC_VALUE_TYPE_BOOLEAN,
            'dateTime.iso8601' => XMLRPC_VALUE_TYPE_DATETIME,
            'double' => XMLRPC_VALUE_TYPE_DOUBLE,
            'i4' => XMLRPC_VALUE_TYPE_INTEGER,
            'int' => XMLRPC_VALUE_TYPE_INTEGER,
            'string' => XMLRPC_VALUE_TYPE_STRING,
            'struct' => XMLRPC_VALUE_TYPE_STRUCT,
            'nil' => XMLRPC_VALUE_TYPE_NIL,
        ];

        return $types[$name];
    }

    /**
     * get tag name by value type.
     *
     * @return string tag name
     */
    public function get_tag_name($value)
    {
        static $names = [
            XMLRPC_VALUE_TYPE_ARRAY => 'array',
            XMLRPC_VALUE_TYPE_BASE64 => 'base64',
            XMLRPC_VALUE_TYPE_BOOLEAN => 'boolean',
            XMLRPC_VALUE_TYPE_DATETIME => 'dateTime.iso8601',
            XMLRPC_VALUE_TYPE_DOUBLE => 'dobule',
            XMLRPC_VALUE_TYPE_INTEGER => 'int',
            XMLRPC_VALUE_TYPE_STRING => 'string',
            XMLRPC_VALUE_TYPE_STRUCT => 'struct',
            XMLRPC_VALUE_TYPE_NIL => 'nil',
        ];

        return $names[$value];
    }

    /**
     * encode date/time value for xml.
     *
     * @param int $value input date/time value
     *
     * @return encoded string for xml
     */
    public function encode_datetime($value)
    {
        return gmstrftime('%Y%m%dT%H:%M:%S', $value);
    }

    /**
     * decode date/time value.
     *
     * @param string $value date/time value
     *
     * @return int decoded date/time timestamp
     */
    public function decode_datetime($value)
    {
        $tm = false;
        if (preg_match('/^(-?\\d{4}|[+-]\\d{6})(?:-?(?:(\\d{2})(?:-?(\\d{2})?)?|W([0-5]\\d)-?([1-7])|([0-3]\\d\\d)))?(?:T(\\d{2})(?::(\\d{2})(?::(\\d{2}))?)?(?:Z|([-+])(\\d{2})(?::?(\\d{2}))?)?)?$/', $value, $matches)) {
            $year = (int)$matches[1];
            if ($year < 1970 || $year > 2038) {
                // unsupported year
                return false;
            }
            $month = (int)(isset($matches[2]) ? $matches[2] : 1);
            $mday = (int)(isset($matches[3]) ? $matches[3] : 1);
            $week = (int)(isset($matches[4]) ? $matches[4] : 0);
            $wday = (int)(isset($matches[5]) ? $matches[5] : 0);
            $oday = (int)(isset($matches[6]) ? $matches[6] : 0);
            $hour = (int)(isset($matches[7]) ? $matches[7] : 0);
            $min = (int)(isset($matches[8]) ? $matches[8] : 0);
            $sec = (int)(isset($matches[9]) ? $matches[9] : 0);
            $pm = (int)(isset($matches[10]) ? $matches[10] . '1' : 1);
            $tz_hour = (int)(isset($matches[11]) ? $matches[11] : 0);
            $tz_min = (int)(isset($matches[12]) ? $matches[12] : 0);
            $tz_offset = $pm * ($tz_hour * 3600 + $tz_min * 60);
            if (0 == $week && 0 == $wday && 0 == $oday) {
                // calendar dates
                $tm = (int)gmmktime($hour, $min, $sec, $month, $mday, $year);
            } else {
                $tsm = (int)gmmktime(0, 0, 0, 1, 1, $year);
                if (0 != $week && 0 != $wday) {
                    // week dates
                    $days = ($week - 1) * 7 - (int)gmdate('w', $tsm) + $wday;
                } else {
                    // ordinal dates
                    $days = $oday;
                }
                $tm = $tsm + $days * 86400 + $hour * 3600 + $min * 60 + $sec;
            }
            $tm = (int)$tm + $tz_offset;
        }

        return $tm;
    }

    /**
     * encode string value for xml.
     *
     * @param string $value input string value
     *
     * @return encoded string for xml
     */
    public function encode_string($value)
    {
        static $search = ['&', '\'', '"', '<', '>'];
        static $replace = ['&amp;', '&apos;', '&quot;', '&lt;', '&gt;'];

        return str_replace($search, $replace, $value);
    }

    /**
     * decode string value.
     *
     * @param string $str input string
     *
     * @return decoded string
     */
    public function decode_string($str)
    {
        $value = str_replace($this->_html_char_entity_ref, $this->_html_numeric_char_ref, $str);
        // convert '&' to '&amp;' for mb_decode_numericentity()
        $value = preg_replace('/&/', '&amp;', $value);
        // convert numeric entity of hex type to dec type
        $value = preg_replace_callback(
            '/&amp;#[xX]([0-9a-fA-F]+);/', function ($m) {
                return '&#'.hexdec($m[1]).';';
            }, $value
        );
        $value = preg_replace('/&amp;#([0-9]+);/', '&#$1;', $value);
        // decode numeric entity
        $value = mb_decode_numericentity($value, [0x0, 0x10000, 0, 0xfffff], 'UTF-8');
        // convert '&amp;' to '&'
        $value = str_replace('&amp;', '&', $value);

        return $value;
    }
}

/**
 * xmlrpc data holder class.
 *
 * @copyright copyright &copy; 2009 RIKEN BSI Neuroinformatics Japan Center
 */
class XMLRPC_Value
{
    /**
     * data type.
     *
     * @var int
     */
    public $_type;

    /**
     * value.
     *
     * @var mixed
     */
    public $_value;

    /**
     * constructor.
     *
     * @param int   $type  data type 'XMLRPC_VALUE_TYPE_XXXXX'
     * @param mixed $value value
     */
    public function __construct($type, $value)
    {
        $this->_type = $type;
        $this->_value = $value;
    }

    /**
     * get value type.
     *
     * @return int data type 'XMLRPC_VALUE_TYPE_XXXXX'
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * get value.
     *
     * @return mixed value
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * render xml.
     *
     * @return string rendered XML
     */
    public function renderXML()
    {
        $util = &XMLRPC_Utility::getInstance();
        $xml = '<value>';
        if (XMLRPC_VALUE_TYPE_NIL == $this->_type) {
            $xml .= '<'.$util->get_tag_name($this->_type).' />';
        } else {
            $xml .= '<'.$util->get_tag_name($this->_type).'>';
            switch ($this->_type) {
            case XMLRPC_VALUE_TYPE_ARRAY:
                  $xml .= '<data>';
                foreach ($this->_value as $value) {
                    $xml .= $value->renderXML();
                }
                  $xml .= '</data>';
                break;
            case XMLRPC_VALUE_TYPE_BASE64:
                  $xml .= base64_encode($this->_value);
                break;
            case XMLRPC_VALUE_TYPE_BOOLEAN:
                  $xml .= ($this->_value ? 1 : 0);
                break;
            case XMLRPC_VALUE_TYPE_DATETIME:
                  $xml .= $util->encode_datetime($this->_value);
                break;
            case XMLRPC_VALUE_TYPE_DOUBLE:
            case XMLRPC_VALUE_TYPE_INTEGER:
                  $xml .= $this->_value;
                break;
            case XMLRPC_VALUE_TYPE_STRING:
                  $xml .= $util->encode_string($this->_value);
                break;
            case XMLRPC_VALUE_TYPE_STRUCT:
                foreach ($this->_value as $key => $value) {
                    $xml .= '<member>';
                    $xml .= '<name>'.$util->encode_string($key).'</name>';
                    $xml .= $value->renderXML();
                    $xml .= '</member>';
                }
                break;
            }
            $xml .= '</'.$util->get_tag_name($this->_type).'>';
        }
        $xml .= '</value>';

        return $xml;
    }
}

/**
 * xmlrpc method holder class.
 *
 * @copyright copyright &copy; 2009 RIKEN BSI Neuroinformatics Japan Center
 */
class XMLRPC_Method
{
    /**
     * method name.
     *
     * @var int
     */
    public $_name;

    /**
     * parameters.
     *
     * @var array
     */
    public $_params;

    /**
     * constructor.
     *
     * @param string $name   method name
     * @param array  $params array of XMLRPC_Value class instances
     */
    public function __construct($name, $params)
    {
        $this->_name = $name;
        $this->_params = $params;
    }

    /**
     * render xml.
     *
     * @return string rendered XML
     */
    public function renderXML()
    {
        $util = &XMLRPC_Utility::getInstance();
        $xml = '<?xml version="1.0"?>'."\n";
        $xml .= '<methodCall>';
        $xml .= '<methodName>'.$util->encode_string($this->_name).'</methodName>';
        $xml .= '<params>';
        foreach ($this->_params as $param) {
            $xml .= '<param>'.$param->renderXML().'</param>';
        }
        $xml .= '</params>';
        $xml .= '</methodCall>';

        return $xml;
    }
}

/**
 * xmlrpc response holder class.
 *
 * @copyright copyright &copy; 2009 RIKEN BSI Neuroinformatics Japan Center
 */
class XMLRPC_Response
{
    /**
     * error messages.
     *
     * @var array
     */
    public $_errors;

    /**
     * response parameter.
     *
     * @var array
     */
    public $_param;

    /**
     * fault code.
     *
     * @var int
     */
    public $_fault_code;

    /**
     * fault string.
     *
     * @var string
     */
    public $_fault_string;

    /**
     * runtime parser condition.
     *
     * @var array
     */
    public $_condition;

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->_errors = [];
        $this->_param = false;
        $this->_fault_code = 0;
        $this->_fault_string = '';
    }

    /**
     * parse XML.
     *
     * @param string $xml response xml string
     */
    public function parseXML($xml)
    {
        $this->_condition_initialize();
        $parser = xml_parser_create('UTF-8');
        xml_set_object($parser, $this);
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_set_element_handler($parser, '_parser_start_element_handler', '_parser_end_element_handler');
        xml_set_character_data_handler($parser, '_parser_character_data_handler');
        if (!xml_parse($parser, $xml)) {
            $msg = xml_error_string(xml_get_error_code($parser));
            $this->_set_parser_error($parser, $msg);
        }
        xml_parser_free($parser);

        return !$this->hasError();
    }

    /**
     * set param.
     *
     * @param object $param XMLRPC_Value class instance
     */
    public function setParam(&$param)
    {
        $this->_fault_code = 0;
        $this->_fault_string = '';
        $this->_param = &$param;
    }

    /**
     * set fault.
     *
     * @param int    $code fault code
     * @param string $str  fault string
     */
    public function setFault($code, $str)
    {
        $this->_fault_code = $code;
        $this->_fault_string = $str;
        $this->_param = false;
    }

    /**
     * get param.
     *
     * @return object XMLRPC_Value class instance
     */
    public function &getParam()
    {
        return $this->_param;
    }

    /**
     * render xml.
     *
     * @return string rendered XML
     */
    public function renderXML()
    {
        $util = &XMLRPC_Utility::getInstance();
        $xml = '<?xml version="1.0"?>'."\n";
        $xml .= '<methodResponse>';
        if ($this->isFault()) {
            $val = [
                'faultCode' => new XMLRPC_Value(XMLRPC_VALUE_TYPE_INTEGER, $this->_fault_code),
                'faultString' => new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $this->_fault_string),
            ];
            $obj = new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRUCT, $val);
            $xml .= '<fault>';
            $xml .= $obj->renderXML();
            $xml .= '</fault>';
        } else {
            $xml .= '<params>';
            $xml .= '<param>'.$this->_param->renderXML().'</param>';
            $xml .= '</params>';
        }
        $xml .= '</methodResponse>';

        return $xml;
    }

    /**
     * has system error ?
     *
     * @return bool true if system has error(s)
     */
    public function hasError()
    {
        return !empty($this->_errors);
    }

    /**
     * get error messages.
     *
     * @return array error messages
     */
    public function getError()
    {
        return $this->_errors;
    }

    /**
     * is fault ?
     *
     * @return bool true if response is fault
     */
    public function isFault()
    {
        return 0 != $this->_fault_code;
    }

    /**
     * get fault code.
     *
     * @return int fault code
     */
    public function getFaultCode()
    {
        return $this->_fault_code;
    }

    /**
     * get fault string.
     *
     * @return string fault string
     */
    public function getFaultString()
    {
        return $this->_fault_string;
    }

    /**
     * set parser error message.
     *
     * @param resource $parser resource of xml parser
     * @param string   $msg    error message
     */
    public function _set_parser_error($parser, $msg)
    {
        $li = xml_get_current_line_number($parser);
        $co = xml_get_current_column_number($parser);
        $this->_errors[] = 'XML Parse Error : '.$msg.' at line '.$li.' column '.$co;
    }

    /**
     * callback handler of start element of xml data.
     *
     * @param resource $parser  parser resource
     * @param string   $name    xml element name
     * @param string   $attribs xml attributes
     */
    public function _parser_start_element_handler($parser, $name, $attribs)
    {
        // error check
        if ($this->hasError()) {
            return;
        }

        // update element name stack
        $pname = $this->_condition_get_name();
        $this->_condition_push_name($name);

        // structure check
        if (!$this->_condition_check_xml($parser, $pname, $name)) {
            // invalid xml data found
            return;
        }

        $this->_condition_push_cdata();
        switch ($name) {
        case 'array':
        case 'struct':
            $this->_condition_push_array();
            break;
        case 'param':
            if (false !== $this->_param) {
                // params have to contain a single param
                $msg = 'Multiple "'.$name.'" tag found';
                $this->_set_parser_error($parser, $msg);
            }
            break;
        }
    }

    /**
     * callback handler of end element of xml data.
     *
     * @param resource $parser parser resource
     * @param string   $name   xml element name
     */
    public function _parser_end_element_handler($parser, $name)
    {
        // error check
        if ($this->hasError()) {
            return;
        }

        // update element name stack
        $this->_condition_pop_name();
        $pname = $this->_condition_get_name();

        $util = &XMLRPC_Utility::getInstance();
        $cdata = $this->_condition_get_cdata();
        switch ($name) {
        case 'base64':
            $value = base64_decode($cdata);
            $this->_condition_push_value($util->get_value_type($name), $value);
            break;
        case 'boolean':
            $value = (1 == (int)$cdata);
            $this->_condition_push_value($util->get_value_type($name), $value);
            break;
        case 'dateTime.iso8601':
            $value = $util->decode_datetime($cdata);
            $this->_condition_push_value($util->get_value_type($name), $value);
            break;
        case 'double':
            $value = (float)$cdata;
            $this->_condition_push_value($util->get_value_type($name), $value);
            break;
        case 'i4':
        case 'int':
            $value = (int)$cdata;
            $this->_condition_push_value($util->get_value_type($name), $value);
            break;
        case 'string':
            $value = $util->decode_string($cdata);
            $this->_condition_push_value($util->get_value_type($name), $value);
            break;
        case 'nil':
            $this->_condition_push_value($util->get_value_type($name), null);
            break;
        case 'array':
        case 'struct':
            $value = &$this->_condition_pop_array();
            $this->_condition_push_value($util->get_value_type($name), $value);
            break;
        case 'param':
            $this->_param = &$this->_condition_pop_value();
            break;
        case 'name':
            $value = $util->decode_string($cdata);
            $this->_condition_set_array_name($value);
            break;
        case 'member':
            $obj = &$this->_condition_pop_value();
            $this->_condition_append_array($obj);
            break;
        case 'value':
            switch ($pname) {
            case 'data':
                $obj = &$this->_condition_pop_value();
                $this->_condition_append_array($obj);
                break;
            case 'fault':
                $obj = &$this->_condition_pop_value();
                $value = $obj->getValue();
                $this->_fault_code = $value['faultCode']->getValue();
                $this->_fault_string = $value['faultString']->getValue();
                break;
            }
            break;
        }
        $this->_condition_pop_cdata();
    }

    /**
     * callback handler of character data handler of xml data.
     *
     * @param resource $parser parser resource
     * @param string   $cdata  character data
     */
    public function _parser_character_data_handler($parser, $cdata)
    {
        // error check
        if ($this->hasError()) {
            return;
        }

        // get element name
        $name = $this->_condition_get_name();

        $this->_condition_append_cdata($cdata);
    }

    /**
     * initialize parser conditions.
     */
    public function _condition_initialize()
    {
        $this->_condition = [];
        $this->_condition['values'] = [];
        $this->_condition['arrays'] = [];
        $this->_condition['cdata'] = [''];
        $this->_condition['names'] = [''];
    }

    /**
     * check xml structure.
     *
     * @param resource $parser resource of xml parser
     * @param string   $pname  parent element name
     * @param string   $name   current element name
     *
     * @return bool false if an error occured
     */
    public function _condition_check_xml($parser, $pname, $name)
    {
        static $xml_struct = [
            'methodResponse' => [''],
            'params' => ['methodResponse'],
            'fault' => ['methodResponse'],
            'param' => ['params'],
            'value' => ['param', 'data', 'member', 'fault'],
            'array' => ['value'],
            'base64' => ['value'],
            'boolean' => ['value'],
            'dateTime.iso8601' => ['value'],
            'double' => ['value'],
            'i4' => ['value'],
            'int' => ['value'],
            'string' => ['value'],
            'struct' => ['value'],
            'data' => ['array'],
            'member' => ['struct'],
            'name' => ['member'],
        ];
        // check known name name
        if (!isset($xml_struct[$name])) {
            $msg = 'Unknown tag "'.$name.'" found under "'.$pname.'"';
            $this->_set_parser_error($parser, $msg);

            return false;
        }
        // parent name name
        if (!in_array($pname, $xml_struct[$name])) {
            $msg = 'Unexpected tag "'.$name.'" found under "'.$pname.'"';
            $this->_set_parser_error($parser, $msg);

            return false;
        }

        return true;
    }

    /**
     * push element name stack for runtime parser condition.
     *
     * @param string $name name name
     */
    public function _condition_push_name($name)
    {
        array_unshift($this->_condition['names'], $name);
    }

    /**
     * pop element name stack for runtime parser condition.
     */
    public function _condition_pop_name()
    {
        array_shift($this->_condition['names']);
    }

    /**
     * get current element name for runtime parser condition.
     *
     * @return string current name name
     */
    public function _condition_get_name()
    {
        return $this->_condition['names'][0];
    }

    /**
     * push character data stack for runtime parser condition.
     *
     * @param string $cdata character data of current element
     */
    public function _condition_push_cdata()
    {
        array_unshift($this->_condition['cdata'], '');
    }

    /**
     * pop character data stack for runtime parser condition.
     *
     * @param string $cdata character data of current element
     */
    public function _condition_pop_cdata()
    {
        array_shift($this->_condition['cdata']);
    }

    /**
     * get character data of current element for runtime parser condition.
     *
     * @return string character data of current element
     */
    public function _condition_get_cdata()
    {
        return $this->_condition['cdata'][0];
    }

    /**
     * append character data of current element for runtime parser condition.
     *
     * @param string $cdata character data of current element
     */
    public function _condition_append_cdata($cdata)
    {
        return $this->_condition['cdata'][0] .= $cdata;
    }

    /**
     * push values stack for runtime parser condition.
     *
     * @param int   $type   value type
     * @param mixed &$value value
     */
    public function _condition_push_value($type, $value)
    {
        $this->_condition['values'][] = new XMLRPC_Value($type, $value);
    }

    /**
     * pop values stack for runtime parser condition.
     *
     * @return class instance of XMLRPC_Value
     */
    public function &_condition_pop_value()
    {
        $value = array_pop($this->_condition['values']);

        return $value;
    }

    /**
     * push arrays stack for runtime parser condition.
     */
    public function _condition_push_array()
    {
        $struct = [
            'name' => null,
            'values' => [],
        ];
        array_unshift($this->_condition['arrays'], $struct);
    }

    /**
     * pop arrays stack for runtime parser condition.
     *
     * @return array XMLRPC_Value class instances
     */
    public function &_condition_pop_array()
    {
        $struct = &$this->_condition['arrays'][0];
        array_shift($this->_condition['arrays']);

        return $struct['values'];
    }

    /**
     * set current array name for runtime parser condition.
     *
     * @param string $name string member name
     */
    public function _condition_set_array_name($name)
    {
        $struct = &$this->_condition['arrays'][0];
        $struct['name'] = $name;
    }

    /**
     * append value to current array for runtime parser condition.
     *
     * @param object &$obj XMLRPC_Value instance
     */
    public function _condition_append_array(&$obj)
    {
        $struct = &$this->_condition['arrays'][0];
        if (null !== $struct['name']) {
            // struct
            $struct['values'][$struct['name']] = &$obj;
            $struct['name'] = null;
        } else {
            // array
            $struct['values'][] = &$obj;
        }
    }
}

/**
 * xmlrpc client class.
 *
 * @copyright copyright &copy; 2009 RIKEN BSI Neuroinformatics Japan Center
 */
class XMLRPC_Client
{
    /**
     * uri string of XMLRPC entry point.
     *
     * @var string
     */
    public $_uri = '';

    /**
     * server protocol.
     *
     * @var string
     */
    public $_protocol = '';

    /**
     * server host.
     *
     * @var string
     */
    public $_host = '';

    /**
     * server path string.
     *
     * @var string
     */
    public $_path = '';

    /**
     * server port.
     *
     * @var int
     */
    public $_port = 80;

    /**
     * connection time out.
     *
     * @var int
     */
    public $_timeout = 0;

    /**
     * user agent.
     *
     * @var string
     */
    public $_user_agent = 'PHP XML-RPC Client';

    /**
     * error string.
     *
     * @var array
     */
    public $_errors = [];

    /**
     * flag for using http authentication.
     *
     * @var bool
     */
    public $_use_auth = false;

    /**
     * http authentication user name.
     *
     * @var string
     */
    public $_http_user = '';

    /**
     * http authentication password.
     *
     * @var string
     */
    public $_http_pass = '';

    /**
     * flag for using proxy.
     *
     * @var bool
     */
    public $_use_proxy = false;

    /**
     * proxy server host.
     *
     * @var string
     */
    public $_proxy_host = '';

    /**
     * proxy server port.
     *
     * @var int
     */
    public $_proxy_port = 8080;

    /**
     * proxy authentication user name.
     *
     * @var string
     */
    public $_proxy_user = '';

    /**
     * proxy authentication password.
     *
     * @var string
     */
    public $_proxy_pass = '';

    /**
     * proxy authentication method.
     *
     * @var string
     */
    public $_proxy_auth = '';

    /**
     * constructor.
     *
     * @param string $uri uri string of XMLRPC entry point
     */
    public function __construct($uri)
    {
        // patterns
        $_numalpha = '[a-zA-Z0-9]';
        $_hex = '[a-fA-F0-9]';
        $_escape = '%'.$_hex.'{2}';
        $_safe = '[\\$\\-_\\.+]';
        $_extra = '[!\\*\'\\(\\),]';
        // $_unreserved = '('.$_numalpha.'|'.$_safe.'|'.$_extra.')';
        $_unreserved = '(?:'.$_numalpha.'|'.$_safe.')';
        $_uchar = '(?:'.$_unreserved.'|'.$_escape.')';
        $_hsegment = '(?:'.$_uchar.'|[;:@&=])*';
        $_search = '(?:'.$_uchar.'|[;:@&=])*';
        $_hpath = $_hsegment.'(\\/'.$_hsegment.')*';
        $_domain = '(?:'.$_numalpha.'+(?:[\\.\\-]'.$_numalpha.'+)*)+\\.[a-zA-Z]{2,}';
        $_hostport = '('.$_domain.')(?::([0-9]+))?';
        $uri_pattern = '/(http|https):\\/\\/'.$_hostport.'(?:(\\/'.$_hpath.'(?:\\?'.$_search.')?))?/';
        $this->_uri = $uri;
        if (preg_match($uri_pattern, $uri, $matches)) {
            $this->_protocol = $matches[1];
            $this->_host = $matches[2];
            $this->_port = ('' != $matches[3]) ? (int)$matches[3] : ('https' == $this->_protocol ? 443 : 80);
            $this->_path = ('' != $matches[4]) ? $matches[4] : '/';
        }
    }

    /**
     * set user agent.
     *
     * @param string $ua user agent
     */
    public function setUserAgent($ua)
    {
        $this->_user_agent = $ua;
    }

    /**
     * set time out.
     *
     * @param int $to timeout
     */
    public function setTimeout($to)
    {
        $this->_timeout = $to;
    }

    /**
     * set authentication connection.
     *
     * @param string $user user name
     * @param string $pass password
     * @param string $auth authentication method
     */
    public function setAuthentication($user = '', $pass = '', $auth = '')
    {
        if ('' == $auth) {
            $this->_use_auth = false;
            $this->_http_user = '';
            $this->_http_pass = '';
            $this->_http_auth = '';
        } else {
            $this->_use_auth = true;
            $this->_http_user = $user;
            $this->_http_pass = $pass;
            $this->_http_auth = $auth;
        }
    }

    /**
     * set proxy connection.
     *
     * @param string $host proxy host
     * @param int    $port proxy port
     * @param string $user proxy user name
     * @param string $pass proxy password
     * @param string $auth proxy authentication method
     */
    public function setProxy($host, $port = 8080, $user = '', $pass = '', $auth = '')
    {
        if ('' == $host) {
            $this->_use_proxy = false;
            $this->_proxy_host = '';
            $this->_proxy_port = 8080;
            $this->_proxy_user = '';
            $this->_proxy_pass = '';
            $this->_proxy_auth = '';
        } else {
            $this->_use_proxy = true;
            $this->_proxy_host = $host;
            $this->_proxy_port = $port;
            $this->_proxy_user = $user;
            $this->_proxy_pass = $pass;
            $this->_proxy_auth = $auth;
        }
    }

    /**
     * send message.
     *
     * @param objce &$method XMLRPC_Method class instance
     *
     * @return object XMLRPC_Response class instance
     */
    public function &sendMessage($method)
    {
        $ret = false;
        $this->_errors = [];
        if ('' == $this->_host) {
            $this->_errors[] = 'invalid uri string : '.$this->_uri;

            return $ret;
        }
        if ($this->_use_proxy) {
            $host = $this->_proxy_host;
            $port = $this->_proxy_port;
            if (('http' == $this->_protocol && 80 != $this->_port) || ('https' == $this->_protocol && 443 != $this->_port)) {
                $path = $this->_protocol.'://'.$this->_host.':'.$this->_port.$this->_path;
            } else {
                $path = $this->_protocol.'://'.$this->_host.$this->_path;
            }
            switch ($this->_proxy_auth) {
            case 'basic':
                  $proxy_auth = 'Proxy-Authorization: Basic '.base64_encode($this->_proxy_user.':'.$this->_proxy_pass)."\r\n";
                break;
            case '':
                  $proxy_auth = '';
                break;
            default:
                  $this->_errors[] = 'unsupported proxy auth type : '.$this->_proxy_auth;

                return $ret;
            }
        } else {
            if ('https' == $this->_protocol) {
                if (!extension_loaded('openssl')) {
                    $this->_errors[] = 'https protocol not supported';

                    return $ret;
                }
                $host = 'ssl://'.$this->_host;
            } else {
                $host = $this->_host;
            }
            $port = $this->_port;
            $path = $this->_path;
            $proxy_auth = '';
        }

        if ($this->_use_auth) {
            switch ($this->_http_auth) {
            case 'basic':
                $http_auth = 'Authorization: Basic '.base64_encode($this->_http_user.':'.$this->_http_pass)."\r\n";
                break;
            default:
                $this->_errors[] = 'unsupported http auth type : '.$this->_http_auth;

                return $ret;
            }
        } else {
            $http_auth = '';
        }

        if ($this->_timeout > 0) {
            $fp = @fsockopen($host, $port, $errno, $errstr, $this->_timeout);
        } else {
            $fp = @fsockopen($host, $port, $errno, $errstr);
        }

        if (false === $fp) {
            $this->_errors[] = 'failed to open socket : '.$errno.' - '.$errstr;

            return $ret;
        }

        if ($this->_timeout > 0 && function_exits('stream_set_timeout')) {
            stream_set_timeout($fp, $this->_timeout);
        }

        $xml = $method->renderXML();
        $output = 'POST '.$path.' HTTP/1.0'."\r\n";
        $output .= 'User-Agent: '.$this->_user_agent."\r\n";
        $output .= 'HOST: '.$this->_host."\r\n";
        $output .= $http_auth;
        $output .= $proxy_auth;
        $output .= 'Accept-Charset: UTF-8, US-ASCII'."\r\n";
        $output .= 'Content-Type: text/xml'."\r\n";
        $output .= 'Content-Length: '.strlen($xml)."\r\n";
        $output .= "\r\n";
        $output .= $xml;
        if (!fwrite($fp, $output, strlen($output))) {
            $this->_errors[] = 'failed to write request';

            return $ret;
        }
        $input = '';
        while ($dat = fread($fp, 32768)) {
            $input .= $dat;
        }
        fclose($fp);

        if (!preg_match('/^HTTP\\/1.[01] (\\d+)/', $input, $matches)) {
            $this->_errors[] = 'failed to get http response';
            $this->_errors[] = $input;

            return $ret;
        }
        if (200 != $matches[1]) {
            $this->_errors[] = 'invalid http response : '.$matches[1];
            $this->_errors[] = $input;

            return $ret;
        }
        $pos = strpos($input, "\r\n\r\n");
        if (false === $pos) {
            $this->_errors[] = 'response body not found';
            $this->_errors[] = $input;

            return $ret;
        }
        $xml = substr($input, $pos + 4);
        $response = new XMLRPC_Response();
        if (!$response->parseXML($xml)) {
            $this->_errors = array_merge($this->_errors, $response->getError());
            $this->_errors[] = $xml;

            return $ret;
        }

        return $response;
    }

    /**
     * has system error ?
     *
     * @return bool true if system has error(s)
     */
    public function hasError()
    {
        return !empty($this->_errors);
    }

    /**
     * get error messages.
     *
     * @return array error messages
     */
    public function getError()
    {
        return $this->_errors;
    }
}
