<?php

/**
 * Exception for not implemented parts
 */
class TColor_NotImplemented extends Exception {

    public function __construct($message) {
        parent::__construct("This functionality is not implemented yet.\n" . $message);
    }

}

/**
 * Exception for mishmashed input format 
 */
class TColor_MishmashedFormat extends Exception {

    public function __construct($message) {
        parent::__construct("Format is mishmashed.\n" . $message);
    }

}

/**
 * Class for Converting Numbers and Color systems
 */
class TColor_Convertor {

    /**
     * @var int float precision
     */
    private static $precision = 4;

    /**
     * Set precision of float numbers
     * @param type $precision 
     */
    public static function setPrecision($precision) {
        self::$precision = $precision;
    }

    ////////////////////////////////////////////////////////
    ////////////////// Number Conversion ///////////////////
    ////////////////////////////////////////////////////////

    /**
     * Let number in cyrcle within numbers [ 0 - 1 ]
     * @param float $float
     * @return float
     */
    public static function cyrcle($float) {
        if ($float <= 1.0 && $float >= 0.0)
            return $float;
        if ($float > 1.0) {
            return ($float - (float) intval($float));
        }
        if ($float < 0.0) {
            $float = self::cyrcle(1.0 - abs($float));
        }
        return $float;
    }

    /**
     * Convert float 0 - 1 to integer 0 - 255
     * @param float $f
     * @return int 
     */
    public static function FloatToByte($f) {
        return (int) intval((255.0 * floatval($f)));
    }

    /**
     * Convert integer 0 - 255 to float 0 - 1
     * @param integer $b
     * @return float 
     */
    public static function ByteToFloat($b) {
        return (float) round((floatval($b) / 255.0), self::$precision);
    }

    /**
     * Convert integer 0 - 255 to hex number 0 - ff
     * @param integer $b input
     * @param integer $size set zero padding (size is with number 0a - is size 2)
     * @return string 
     */
    public static function ByteToHex($b, $size = 2) {
        $val = dechex($b);
        if (strlen((string) $val) < $size) {
            while (strlen((string) $val) < $size) {
                $val = '0' . $val;
            }
        }
        return $val;
    }

    /**
     * Convert hexadecimal number 0 - ff to integer 0 - 255
     * @param string $h
     * @return integer 
     */
    public static function HexToByte($h) {
        return hexdec($h);
    }

    /**
     * Convert float 0 - 1 to hex 0 - ff
     * @param float $f
     * @return string 
     */
    public static function FloatToHex($f) {
        return self::ByteToHex(self::FloatToByte($f));
    }

    /**
     * Convert hexadecimal number 0 - ff to float 0 - 1
     * @param string $h
     * @return float 
     */
    public static function HexToFloat($h) {
        return self::ByteToFloat(self::HexToByte($h));
    }

    ////////////////////////////////////////////////////////
    ////////////////// Color system conversion /////////////
    ////////////////////////////////////////////////////////

    /**
     * Convert RGB array(r => X, g => X, b => X) to float array(r => X, g => X, b => X)
     * @param array $data
     * @return array 
     */
    public static function RGBtoFloat(array $data) {
        $result = array();
        foreach ($data as $k => $v) {
            $result[$k] = self::ByteToFloat($v);
        }
        return $result;
    }

    /**
     * Convert Float array(r => X, g => X, b => X) to RGB array(r => X, g => X, b => X)
     * @param array $data
     * @return type 
     */
    public static function FloatToRGB(array $data) {
        $result = array();
        foreach ($data as $k => $v) {
            $result[$k] = self::FloatToByte($v);
        }
        return $result;
    }

    /**
     * Convert RGB array(r => X, g => X, b => X) to HEX array(r => X, g => X, b => X)
     * @param array $data
     * @return type 
     */
    public static function RGBtoHEX(array $data) {
        $result = array();
        foreach ($data as $k => $v) {
            $result[$k] = self::ByteToHex($v);
        }
        return $result;
    }

    /**
     * Convert HEX array(r => X, g => X, b => X) to RGB array(r => X, g => X, b => X)
     * @param array $data
     * @return type 
     */
    public static function HEXtoRGB(array $data) {
        $result = array();
        foreach ($data as $k => $v) {
            $result[$k] = self::HexToByte($v);
        }
        return $result;
    }

    /**
     * Convert CMYK array(c => X, m => X, y => X, k => X) to RGB array(r => X, g => X, b => X)
     * @param array $data
     * @return type 
     */
    public static function CMYKtoRGB(array $data) {
        $result = array(
            'r' => 0,
            'g' => 0,
            'b' => 0,
        );
        if ($data['k'] < 1.0) {
            $result = array(
                'r' => intval((255.0 * ((1.0 - $data['c']) * (1.0 - $data['k'])))),
                'g' => intval((255.0 * ((1.0 - $data['m']) * (1.0 - $data['k'])))),
                'b' => intval((255.0 * ((1.0 - $data['y']) * (1.0 - $data['k'])))),
            );
        } else {
            $result = array(
                'r' => intval(255.0 * (1.0 - $data['c'])),
                'g' => intval(255.0 * (1.0 - $data['m'])),
                'b' => intval(255.0 * (1.0 - $data['y'])),
            );
        }
        if (isset($data['a']))
            $result['a'] = self::FloatToByte($data['a']);
        return $result;
    }

    /**
     * Convert RGB array(r => X, g => X, b => X) to CMYK array(c => X, m => X, y => X, k => X)
     * @param array $data
     * @return type 
     */
    public static function RGBtoCMYK(array $data) {
        $result = array(
            'c' => (float) floatval(1.0 - (self::ByteToFloat($data['r']))),
            'm' => (float) floatval(1.0 - (self::ByteToFloat($data['g']))),
            'y' => (float) floatval(1.0 - (self::ByteToFloat($data['b']))),
            'k' => (float) 1.0
        );
        if ($result['c'] < $result['k'])
            $result['k'] = $result['c'];
        if ($result['m'] < $result['k'])
            $result['k'] = $result['m'];
        if ($result['y'] < $result['k'])
            $result['k'] = $result['y'];
        if ($result['k'] >= 1) {
            $result['c'] = 0.0;
            $result['m'] = 0.0;
            $result['y'] = 0.0;
        } else {
            $result['c'] = (float) (( $result['c'] - $result['k'] ) / (1.0 - $result['k']));
            $result['m'] = (float) (( $result['m'] - $result['k'] ) / (1.0 - $result['k']));
            $result['y'] = (float) (( $result['y'] - $result['k'] ) / (1.0 - $result['k']));
        }
        if (isset($data['a']))
            $result['a'] = self::ByteToFloat($data['a']);
        return $result;
    }

    /**
     * Convert HSV array(h => X, s => X, v => X) to RGB array(r => X, g => X, b => X)
     * Used from http://stackoverflow.com/questions/3597417/php-hsv-to-rgb-formula-comprehension
     * @param array $data
     * @return type 
     */
    public static function HSVtoRGB(array $data) {
        $H = $data['h'];
        $S = $data['s'];
        $V = $data['v'];
        //1
        $H *= 6;
        //2
        $I = floor($H);
        $F = $H - $I;
        //3
        $M = $V * (1 - $S);
        $N = $V * (1 - $S * $F);
        $K = $V * (1 - $S * (1 - $F));
        //4
        switch ($I) {
            case 0:
                list($R, $G, $B) = array($V, $K, $M);
                break;
            case 1:
                list($R, $G, $B) = array($N, $V, $M);
                break;
            case 2:
                list($R, $G, $B) = array($M, $V, $K);
                break;
            case 3:
                list($R, $G, $B) = array($M, $N, $V);
                break;
            case 4:
                list($R, $G, $B) = array($K, $M, $V);
                break;
            case 5:
            case 6: //for when $H=1 is given
                list($R, $G, $B) = array($V, $M, $N);
                break;
        }
        return array(
            'r' => self::FloatToByte($R),
            'g' => self::FloatToByte($G),
            'b' => self::FloatToByte($B),
            'a' => self::FloatToByte($data['a']),
        );
    }

    /**
     * Convert RGB array(r => X, g => X, b => X) to HSV array(h => X, s => X, v => X)
     * Used from http://stackoverflow.com/questions/1773698/rgb-to-hsv-in-php
     * @param array $data
     * @return type 
     */
    public static function RGBtoHSV(array $data) {
        $R = $data['r'];
        $G = $data['g'];
        $B = $data['b'];

        $var_R = self::ByteToFloat($R);
        $var_G = self::ByteToFloat($G);
        $var_B = self::ByteToFloat($B);

        $var_Min = min($var_R, $var_G, $var_B);
        $var_Max = max($var_R, $var_G, $var_B);
        $del_Max = $var_Max - $var_Min;

        $V = $var_Max;

        if ($del_Max == 0) {
            $H = 0;
            $S = 0;
        } else {
            $S = $del_Max / $var_Max;

            $del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
            $del_G = ( ( ( $var_Max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
            $del_B = ( ( ( $var_Max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;

            if ($var_R == $var_Max)
                $H = $del_B - $del_G;
            else if ($var_G == $var_Max)
                $H = ( 1 / 3 ) + $del_R - $del_B;
            else if ($var_B == $var_Max)
                $H = ( 2 / 3 ) + $del_G - $del_R;

            if ($H < 0)
                $H++;
            if ($H > 1)
                $H--;
        }
        return array(
            'h' => $H,
            's' => $S,
            'v' => $V,
            'a' => floatval(self::ByteToFloat($data['a']))
        );
    }

    /**
     * Not yet implemented
     * Convert RGB array(r => X, g => X, b => X) to LAB array(l => X, a => X, b => X)
     * @param array $data
     * @throws TColor_NotImplemented 
     */
    public static function RGBtoLAB(array $data) {
        //TODO
        throw new TColor_NotImplemented('Using LAB format. RGB -> LAB');
    }

    /**
     * Not yet implemented
     * Convert LAB array(l => X, a => X, b => X) to RGB array(r => X, g => X, b => X)
     * @param array $data
     * @throws TColor_NotImplemented 
     */
    public static function LABtoRGB(array $data) {
        //TODO
        throw new TColor_NotImplemented('Using LAB format. LAB -> RGB');
    }

}

/**
 * Class for work with colors on web 
 */
class TColor {
    ////////////////////////////////////////////////////////
    ////////////////// Consts members //////////////////////
    ////////////////////////////////////////////////////////
    //HEX Format

    const FORMAT_COLOR_R_HEX = 'RH';
    const FORMAT_COLOR_G_HEX = 'GH';
    const FORMAT_COLOR_B_HEX = 'BH';
    const FORMAT_COLOR_A_HEX = 'AH';
    //Float Format
    const FORMAT_COLOR_R_FLOAT = 'RF';
    const FORMAT_COLOR_G_FLOAT = 'GF';
    const FORMAT_COLOR_B_FLOAT = 'BF';
    const FORMAT_COLOR_A_FLOAT = 'AF';
    //Byte Format
    const FORMAT_COLOR_R_BYTE = 'RR';
    const FORMAT_COLOR_G_BYTE = 'GR';
    const FORMAT_COLOR_B_BYTE = 'BR';
    const FORMAT_COLOR_A_BYTE = 'AR';
    //CMYK format
    const FORMAT_COLOR_C_CMYK = 'CC';
    const FORMAT_COLOR_M_CMYK = 'MC';
    const FORMAT_COLOR_Y_CMYK = 'YC';
    const FORMAT_COLOR_K_CMYK = 'KC';
    const FORMAT_COLOR_A_CMYK = 'AC';
    //HSV format
    const FORMAT_COLOR_H_HSV = 'HS';
    const FORMAT_COLOR_S_HSV = 'SS';
    const FORMAT_COLOR_V_HSV = 'VS';
    const FORMAT_COLOR_A_HSV = 'AS';
    //LAB format
    const FORMAT_COLOR_L_LAB = 'LL';
    const FORMAT_COLOR_A_LAB = 'AL';
    const FORMAT_COLOR_B_LAB = 'BL';

    // All base formats
    private static $FORMATS = array(
        self::FORMAT_COLOR_R_HEX,
        self::FORMAT_COLOR_G_HEX,
        self::FORMAT_COLOR_B_HEX,
        //Float Format
        self::FORMAT_COLOR_R_FLOAT,
        self::FORMAT_COLOR_G_FLOAT,
        self::FORMAT_COLOR_B_FLOAT,
        self::FORMAT_COLOR_A_FLOAT,
        //Byte Format
        self::FORMAT_COLOR_R_BYTE,
        self::FORMAT_COLOR_G_BYTE,
        self::FORMAT_COLOR_B_BYTE,
        self::FORMAT_COLOR_A_BYTE,
        //CMYK format
        self::FORMAT_COLOR_C_CMYK,
        self::FORMAT_COLOR_M_CMYK,
        self::FORMAT_COLOR_Y_CMYK,
        self::FORMAT_COLOR_K_CMYK,
        self::FORMAT_COLOR_A_CMYK,
        //HSV format
        self::FORMAT_COLOR_H_HSV,
        self::FORMAT_COLOR_S_HSV,
        self::FORMAT_COLOR_V_HSV,
        self::FORMAT_COLOR_A_HSV,
        //LAB format
        self::FORMAT_COLOR_L_LAB,
        self::FORMAT_COLOR_A_LAB,
        self::FORMAT_COLOR_B_LAB,
    );

    //////////////////////////////////////////////////////
    //Custom Formats

    const FORMAT_OUTPUT_HEXA = '#RHGHBH';
    const FORMAT_OUTPUT_CSS_OPACITY = 'AF';
    const FORMAT_OUTPUT_CSS_BACKGROUND_OPACITY = 'background: #RHGHBH; opacity: AF;';
    const FORMAT_OUTPUT_CSS_BACKGROUND = 'background: #RHGHBH;';
    const FORMAT_OUTPUT_CSS_COLOR_OPACITY = 'color: #RHGHBH; opacity: AF;';
    const FORMAT_OUTPUT_CSS_COLOR = 'color: #RHGHBH;';
    const FORMAT_OUTPUT_RGB = 'rgb(RR, GR, BR)';
    const FORMAT_OUTPUT_RGBA = 'rgba(RR, GR, BR, AR)';
    const FORMAT_OUTPUT_HSV = 'hsv(HS, SS, VS)';

    //Deprecated formats only for back compability
    const FORMAT_HEXA = self::FORMAT_OUTPUT_HEXA;
    const FORMAT_CSS_OPACITY = self::FORMAT_OUTPUT_CSS_OPACITY;
    const FORMAT_CSS_BACKGROUND_OPACITY = self::FORMAT_OUTPUT_CSS_BACKGROUND_OPACITY;
    const FORMAT_CSS_BACKGROUND = self::FORMAT_OUTPUT_CSS_BACKGROUND;
    const FORMAT_CSS_COLOR_OPACITY = self::FORMAT_OUTPUT_CSS_COLOR_OPACITY;
    const FORMAT_CSS_COLOR = self::FORMAT_OUTPUT_CSS_COLOR;
    const FORMAT_RGB = self::FORMAT_OUTPUT_RGB;
    const FORMAT_RGBA = self::FORMAT_OUTPUT_RGBA;
    const FORMAT_HSV = self::FORMAT_OUTPUT_HSV;

    ////////////////////////////////////////////////////////
    ////////////////// Static members //////////////////////
    ////////////////////////////////////////////////////////
    ////////////////// Factory method //////////////////////
    /**
     * Factory method to generate color
     * Takes many types:
     * - Object - only instance of TColor - clone it
     * - numeric (flot, integer), if float makes from [ 0 - 1 ] if integer makes from [ 0 - 255 ]
     * - Array with keys:
     *          r,g,b
     *          c,m,y,k
     *          h,s,v
     *          l,a,b
     *      else it takes recursivly factory on all values in array
     * - String, it match many patterns (all spaces and new lines are removed from string):
     * 
     * HTML Colors
     * - #anything
     * + goes to HEXa parser that search for #XXX or #XXXXXX (doesnt matter on letters size)
     * - XXX or XXXXXX 
     * + where X can be 0-9 or a-f (doesnt matter on letters size) - goes to HEXa parser
     * + Example: TColor::factory('#aaa'), TColor::factory('aa0'), TColor::factory('000000'), TColor::factory('#ab1200') - all will be matched as HTML HEX color
     * 
     * RGB(A) Colors
     * - RGB(X,X,X)
     * - RGBA(X,X,X,X) X can be 0% - 100% or 0 - 255
     * + Example: TColor::factory('RGB(0, 10, 100)');
     * 
     * CMYK Colors
     * - CMYK(X,X,X,X) X can be 0.0 - 1.0 or 0% - 100%
     * + Example: TColor::factory('CMYK(0, 0.2, 0.1, 0.8)');
     * 
     * Float(A) Colors
     * - F(X,X,X)
     * - FA(X,X,X,X) X can be 0.0 - 1.0
     * + Example: TColor::factory('FA(0.1, 0.1, 0.5, 1.0)');
     * 
     * HSV Colors
     * - HSV(X,X,X) X can be 0.0 - 1.0
     * + Example: TColor::factory('HSV(0.1, 0.5, 1)');
     *
     * LAB Colors
     * - LAB(X,X,X) X can be 0.0 - 1.0
     * + Example: TColor::factory('LAB(0.1, 0.2, 0.3)');
     * 
     * Random Colors
     * - random("X","X") X can be anithing above
     * + Min and Max color betwen random will be
     * + Example: TColor::factory('random("#a00","RGB(0, 100, 100)")');
     * + Example: TColor::factory('random("a00","000")');
     * 
     * - randomvar("X","X") X can be anithing above
     * + Mean and Variance of random color
     * + Example: TColor::factory('randomvar("#aaa","#a00")');
     * 
     * - randomval("X") X can be anithing above
     * + random value (brightness) of color X
     * + Example: TColor::factory('randomval("#aaa")');
     * 
     * - randomsat("X") X can be anithing above
     * + random saturation of color
     * + Example: TColor::factory('randomsat("HSV(0.5, 0.1, 1)")');
     * 
     * - randomfull
     * + generate random color with saturation and brightness set to one
     * + Example: TColor::factory('randomfull');
     * 
     * Bright of color
     * - val("X","Y") 
     * + X can be anything above and Y is number 0.0 - 1.0
     * + Example: TColor::factory('val("#a00", "0.1")');
     * 
     * 
     * @param type $string
     * @return type
     * @throws TColor_MishmashedFormat 
     */
    public static function factory($string) {
        //Instance
        if (is_object($string)) {
            if ($string instanceof TColor) {
                return self::fromInstance($string);
            }
        } else if (is_numeric($string) && (is_int($string) || is_float($string))) {
            if (is_float($string))
                return self::fromFloat($string, $string, $string, 1.0);
            if (is_int($string))
                return self::fromRGB($string, $string, $string, 1.0);
        } else if (is_array($string) && !empty($string)) {
            if (isset($string['r']) &&
                    isset($string['g']) &&
                    isset($string['b'])
            ) {
                return self::fromRGB($string['r'], $string['g'], $string['b'], (isset($string['a'])) ? $string['a'] : 1.0);
            }
            if (isset($string['c']) &&
                    isset($string['m']) &&
                    isset($string['y']) &&
                    isset($string['k'])
            ) {
                return self::fromCMYK($string['c'], $string['m'], $string['y'], $string['k'], (isset($string['a'])) ? $string['a'] : 1.0);
            }
            if (isset($string['h']) &&
                    isset($string['s']) &&
                    isset($string['v'])
            ) {
                return self::fromRGB($string['h'], $string['s'], $string['v'], (isset($string['a'])) ? $string['a'] : 1.0);
            }
            if (isset($string['l']) &&
                    isset($string['a']) &&
                    isset($string['b'])
            ) {
                return self::fromLAB($string['l'], $string['a'], $string['b']);
            }
            $result = array();
            foreach ($string as $k => $v) {
                $result[$k] = self::factory($v);
            }
            return $result;
        } else if (is_string($string)) {
            //String
            $string = str_replace(array(" ", "\n", "\t", "\r"), '', $string);
            $string = trim($string, ' ');
            $string = trim($string, '"');
            $string = trim($string, '\'');
            $data = array();
            if (preg_match('/^\#(.*)$/', $string, $data) > 0) {
                //HEX
                return self::fromHexa($string);
                ///////////////////////////////////////////////////////////////////
            } else if (preg_match('/^[0-9a-fA-F]{3,6}$/', $string, $data) > 0) {
                //HEX
                return self::fromHexa($string);
                ///////////////////////////////////////////////////////////////////
            } else if (preg_match('/^(RGB|rgb)\([0-9]+%,[0-9]+%,[0-9]+%\)$/', $string, $data) > 0) {
                //RGB %
                preg_match_all('/[0-9]+/', $string, $data);
                $data = current($data);
                return self::fromRGB(
                                255.0 * (floatval($data[0]) / 100.0), 255.0 * (floatval($data[1]) / 100.0), 255.0 * (floatval($data[2]) / 100.0), 255
                );
                ///////////////////////////////////////////////////////////////////
            } else if (preg_match('/^(RGB|rgb)\([0-9]+,[0-9]+,[0-9]+\)$/', $string, $data) > 0) {
                //RGB
                preg_match_all('/[0-9]+/', $string, $data);
                $data = current($data);
                return self::fromRGB($data[0], $data[1], $data[2], 255);
                ///////////////////////////////////////////////////////////////////
            } else if (preg_match('/^(RGBA|rgba)\([0-9]+%,[0-9]+%,[0-9]+%,[0-9]+%\)$/', $string, $data) > 0) {
                //RGBA %
                preg_match_all('/[0-9]+/', $string, $data);
                return self::fromRGB(
                                255.0 * (floatval($data[0]) / 100.0), 255.0 * (floatval($data[1]) / 100.0), 255.0 * (floatval($data[2]) / 100.0), 255.0 * (floatval($data[3]) / 100.0)
                );
                ///////////////////////////////////////////////////////////////////
            } else if (preg_match('/^(RGBA|rgba)\([0-9]+,[0-9]+,[0-9]+,[0-9]+\)$/', $string, $data) > 0) {
                //RGBA
                preg_match_all('/[0-9]+/', $string, $data);
                $data = current($data);
                return self::fromRGB($data[0], $data[1], $data[2], $data[3]);
                ///////////////////////////////////////////////////////////////////
            } else if (preg_match('/^(CMYK|cmyk)\([0-9.]+,[0-9.]+,[0-9.]+,[0-9.]+\)$/', $string, $data) > 0) {
                // CMYK
                preg_match_all('/[0-9.]+/', $string, $data);
                $data = current($data);
                return self::fromCMYK($data[0], $data[1], $data[2], $data[3], 1.0);
                ///////////////////////////////////////////////////////////////////
            } else if (preg_match('/^(CMYK|cmyk)\([0-9]+%,[0-9]+%,[0-9]+%,[0-9]+%\)$/', $string, $data) > 0) {
                // CMYK %
                preg_match_all('/[0-9]+/', $string, $data);
                $data = current($data);
                return self::fromCMYK($data[0] / 100.0, $data[1] / 100.0, $data[2] / 100.0, $data[3] / 100.0, 1.0);
                ///////////////////////////////////////////////////////////////////
            } else if (preg_match('/^(F|f)\([0-9.]+f?,[0-9.]+f?,[0-9.]+f?\)$/', $string, $data) > 0) {
                // F
                preg_match_all('/[0-9.]+/', $string, $data);
                $data = current($data);
                $data = current($data);
                return self::fromFloat(floatval($data[0]), floatval($data[1]), floatval($data[2]), 1.0);
                ///////////////////////////////////////////////////////////////////
            } else if (preg_match('/^(FA|fa)\([0-9.]+f?,[0-9.]+f?,[0-9.]+f?,[0-9.]+f?\)$/', $string, $data) > 0) {
                // FA
                preg_match_all('/[0-9.]+/', $string, $data);
                $data = current($data);
                return self::fromFloat(floatval($data[0]), floatval($data[1]), floatval($data[2]), floatval($data[3]));
                ///////////////////////////////////////////////////////////////////
            } else if (preg_match('/^(HSV|hsv)\([0-9.]+,[0-9.]+,[0-9.]+\)$/', $string, $data) > 0) {
                // HSV
                preg_match_all('/[0-9.]+/', $string, $data);
                $data = current($data);
                return self::fromHSV($data[0], $data[1], $data[2], 1.0);
                ///////////////////////////////////////////////////////////////////
            } else if (preg_match('/^(LAB|lab)\([0-9.]+,[0-9.]+,[0-9.]+\)$/', $string, $data) > 0) {
                // LAB
                preg_match_all('/[0-9.]+/', $string, $data);
                $data = current($data);
                return self::fromLAB($data[0], $data[1], $data[2], 1.0);
                ///////////////////////////////////////////////////////////////////
            } else if (preg_match('/^(random|RANDOM)\(\"(.*)\",\"(.*)\"\)$/', $string, $data) > 0) {
                // random with
                preg_match_all('/"(.*)"/', $string, $data);
                $data = explode('","', current(current($data)));
                foreach ($data as $k => $v) {
                    $data[$k] = self::factory(trim($v, '"'));
                }
                return self::fromRandom($data[0], $data[1]);
                ///////////////////////////////////////////////////////////////////
            } else if (preg_match('/^(randomvar|randomVar|RANDOMVAR)\(\"(.*)\",\"(.*)\"\)$/', $string, $data) > 0) {
                // random with var
                preg_match_all('/"(.*)"/', $string, $data);
                $data = explode('","', current(current($data)));
                foreach ($data as $k => $v) {
                    $data[$k] = self::factory(trim($v, '"'));
                }
                return self::fromRandomVar($data[0], $data[1]);
                ///////////////////////////////////////////////////////////////////
            } else if (preg_match('/^(randomval|randomVal|RANDOMVAL)\(\"(.*)\"\)$/', $string, $data) > 0) {
                // random with var
                preg_match_all('/\"(.*)\"/', $string, $data);
                $data = self::factory(trim(current(current($data)), '"'));
                return self::fromRandomVal($data);
                ///////////////////////////////////////////////////////////////////
            } else if (preg_match('/^(randomsat|randomSat|RANDOMSAT)\(\"(.*)\"\)$/', $string, $data) > 0) {
                // random with var
                preg_match_all('/\"(.*)\"/', $string, $data);
                $data = self::factory(trim(current(current($data)), '"'));
                return self::fromRandomSat($data);
                ///////////////////////////////////////////////////////////////////
            } else if (preg_match('/^(randomFull|randomfull|RANDOMFULL)$/', $string, $data) > 0) {
                // random
                return self::fromRandom()->setBrightness(1.0)->setSaturation(1.0);
                ///////////////////////////////////////////////////////////////////
            } else if (preg_match('/^(randomvar|randomVar|RANDOMVAR)\(\"(.*)\",\"(.*)\"\)$/', $string, $data) > 0) {
                // random with var
                preg_match_all('/"(.*)"/', $string, $data);
                $data = explode('","', current(current($data)));
                foreach ($data as $k => $v) {
                    $data[$k] = self::factory(trim($v, '"'));
                }
                return self::fromRandomVar($data[0], $data[1]);
            } else if (preg_match('/^(val|VAL)\(\"(.*)\",\"(.*)\"\)$/', $string, $data) > 0) {
                // random with var
                preg_match_all('/"(.*)"/', $string, $data);
                $data = explode('","', current(current($data)));
                foreach ($data as $k => $v) {
                    $data[$k] = trim($v, '"');
                }
                return self::fromVal(self::factory($data[0]), (float) $data[1]);
                ///////////////////////////////////////////////////////////////////
            } else if (preg_match('/^(random|RANDOM)$/', $string, $data) > 0) {
                // random
                return self::fromRandom();
                ///////////////////////////////////////////////////////////////////
//            } else if (preg_match('/^$/', $string, $data) > 0) {
//                //
//                ///////////////////////////////////////////////////////////////////
            }
        }
        throw new TColor_MishmashedFormat('Format was not recognized. Input:' . (string) $string . (is_object($string) ? ' Class:' . get_class($string) : ''));
    }

    ////////////////////////////////////////////////////////
    ///////////////// Static Construct  ////////////////////
    ////////////////////////////////////////////////////////

    /**
     * Static construct from hexastring, accepting XXX, XXXXXX or #XXX, #XXXXXX
     * @param type $string
     * @return \TColor
     * @throws TColor_MishmashedFormat 
     */
    public static function fromHexa($string) {
        $string = strtolower(str_replace(array(' ', '#'), '', $string));
        $r = 0;
        $g = 0;
        $b = 0;
        if (strlen($string) == 3) {
            $r = $string[0] . $string[0];
            $g = $string[1] . $string[1];
            $b = $string[2] . $string[2];
        } else if (strlen($string) == 6) {
            $r = $string[0] . $string[1];
            $g = $string[2] . $string[3];
            $b = $string[4] . $string[5];
        } else {
            throw new TColor_MishmashedFormat('Hex format must be 6 or 3 hexadecimal characters. Expected format XXX or XXXXXX. Got:' . $string);
        }
        $data = TColor_Convertor::RGBtoFloat(TColor_Convertor::HEXtoRGB(array(
                            'r' => $r,
                            'g' => $g,
                            'b' => $b
                        )));
        return new TColor($data);
    }

    /**
     * Static construct from RGBA colors
     * @param type $r
     * @param type $g
     * @param type $b
     * @param type $a
     * @return \TColor 
     */
    public static function fromRGB($r, $g, $b, $a) {
        $data = TColor_Convertor::RGBtoFloat(array(
                    'r' => $r,
                    'g' => $g,
                    'b' => $b,
                    'a' => $a,
                ));
        return new TColor($data);
    }

    /**
     * Static construct from floats
     * @param type $r
     * @param type $g
     * @param type $b
     * @param type $a
     * @return \self 
     */
    public static function fromFloat($r, $g, $b, $a) {
        return new self($r, $g, $b, $a);
    }

    /**
     * Static construct from CMYK + A colors
     * @param type $c
     * @param type $m
     * @param type $y
     * @param type $k
     * @param type $a
     * @return \TColor 
     */
    public static function fromCMYK($c, $m, $y, $k, $a) {
        $data = TColor_Convertor::RGBtoFloat(TColor_Convertor::CMYKtoRGB(array(
                            'c' => $c,
                            'm' => $m,
                            'y' => $y,
                            'k' => $k,
                            'a' => $a,
                        )));
        return new TColor($data);
    }

    /**
     * Static construct from HSV + A colors
     * @param type $h
     * @param type $s
     * @param type $v
     * @param type $a
     * @return \TColor 
     */
    public static function fromHSV($h, $s, $v, $a) {
        $data = TColor_Convertor::RGBtoFloat(TColor_Convertor::HSVtoRGB(array(
                            'h' => $h,
                            's' => $s,
                            'v' => $v,
                            'a' => $a,
                        )));
        return new TColor($data);
    }

    /**
     * Static construct from LAB colors
     * @param type $l
     * @param type $a
     * @param type $b
     * @return \TColor 
     */
    public static function fromLAB($l, $a, $b) {
        $data = TColor_Convertor::RGBtoFloat(TColor_Convertor::LABtoRGB(array(
                            'l' => $l,
                            'a' => $a,
                            'b' => $b,
                        )));
        return new TColor($data);
    }

    /**
     * Static construct from instance - instance is cloned
     * @param TColor $color
     * @return type 
     */
    public static function fromInstance(TColor $color) {
        return clone $color;
    }

    /**
     * Static construct from random color
     * @param type $color - min color
     * @param type $max - max color
     * @return type
     * @throws TColor_MishmashedFormat 
     */
    public static function fromRandom($color = null, $max = null) {
        if (is_null($color)) {
            return self::fromRGB(rand(0, 255), rand(0, 255), rand(0, 255), 255);
        } else if (!is_null($max)) {
            $color = self::factory($color)->getRGB();
            $max = self::factory($max)->getRGB();
            foreach ($max as $k => $v) {
                $color[$k] = rand($color[$k], $v);
            }
            return self::fromRGB($color['r'], $color['g'], $color['b'], $color['a']);
        }
        throw new TColor_MishmashedFormat('Random must be with max or complet random.');
    }

    /**
     * Static construct from random colors
     * @param type $color Mean
     * @param type $var Variance
     * @return type
     * @throws TColor_MishmashedFormat 
     */
    public static function fromRandomVar($color = null, $var = null) {
        if (!is_null($color) && !is_null($var)) {
            $color = self::factory($color)->getRGB();
            $var = self::factory($var)->getRGB();
            foreach ($var as $k => $v) {
                $color[$k] = rand($color[$k] - $v, $color[$k] + $v);
            }
            return self::fromRGB($color['r'], $color['g'], $color['b'], $color['a']);
        }
        throw new TColor_MishmashedFormat('Random must be with variance or complet random.');
    }

    /**
     * Static construct from random value (brightness)
     * @param type $color
     * @return type
     * @throws TColor_MishmashedFormat 
     */
    public static function fromRandomVal($color = null) {
        if (!is_null($color)) {
            $color = self::factory($color)->getHSV();
            $color['v'] = (float) (floatval(rand(0, 1000)) / 1000.0);
            return self::fromHSV($color['h'], $color['s'], $color['v'], $color['a']);
        }
        throw new TColor_MishmashedFormat('Color must be set.');
    }

    /**
     * Static construct from random saturation
     * @param type $color
     * @return type
     * @throws TColor_MishmashedFormat 
     */
    public static function fromRandomSat($color = null) {
        if (!is_null($color)) {
            $color = self::factory($color)->getHSV();
            $color['s'] = (float) (floatval(rand(0, 1000)) / 1000.0);
            return self::fromHSV($color['h'], $color['s'], $color['v'], $color['a']);
        }
        throw new TColor_MishmashedFormat('Color must be set.');
    }

    /**
     * Static construct from color and brightness
     * @param type $color
     * @param type $val
     * @return type
     * @throws TColor_MishmashedFormat 
     */
    public static function fromVal($color = null, $val = 0.0) {
        if (!is_null($color)) {
            $color = self::factory($color)->getHSV();
            $color['v'] = (float) ($val);
            return self::fromHSV($color['h'], $color['s'], $color['v'], $color['a']);
        }
        throw new TColor_MishmashedFormat('Color must be set.');
    }

    /**
     * Static construct empty instance
     * @return type
     */
    public static function newInstance() {
        return new TColor();
    }

    ////////////////////////////////////////////////////////
    ////////////////////Static Members//////////////////////

    /**
     * Static construct from TColor_Text 
     * @see TColor_Text
     */
    public static function getText($color, $text, $delimiter = ' ', $sort = false, $sorting = 'hue') {
        return TColor_Text::getText($color, $text, $delimiter, $sort, $sorting);
    }

    /**
     * Static construct from TColor_Text 
     * @see TColor_Text
     */
    public static function getRandomColorText($text, $delimiter = ' ', $sort = false, $sorting = 'hue') {
        return TColor_Text::getText('randomfull', $text, $delimiter, $sort, $sorting);
    }

    /**
     * Returns true if two colors are equals
     * @param type $a
     * @param type $b
     * @return boolean 
     */
    public static function equals($a, $b) {
        $a = self::factory($a);
        $b = self::factory($b);
        if ($a->getR() == $b->getR() &&
                $a->getG() == $b->getG() &&
                $a->getB() == $b->getB() &&
                $a->getA() == $b->getA()) {
            return true;
        }
        return false;
    }

    /**
     * Compare two colors with weight colors
     * @param type $a
     * @param type $b
     * @return int 
     */
    public static function cmpWeight($a, $b) {
        $a = self::factory($a)->weightLength();
        $b = self::factory($b)->weightLength();
        if ($a == $b)
            return 0;
        return ($a < $b) ? -1 : 1;
    }

    /**
     * Compare two colors hues
     * @param type $a
     * @param type $b
     * @return int 
     */
    public static function cmpHue($a, $b) {
        $a = self::factory($a)->getHue();
        $b = self::factory($b)->getHue();
        if ($a == $b)
            return 0;
        return ($a < $b) ? -1 : 1;
    }

    /**
     * Compare two colors value (brightness)
     * @param type $a
     * @param type $b
     * @return int 
     */
    public static function cmpValue($a, $b) {
        $a = self::factory($a)->getBrightness();
        $b = self::factory($b)->getBrightness();
        if ($a == $b)
            return 0;
        return ($a < $b) ? -1 : 1;
    }

    /**
     * Compare two colors saturations
     * @param type $a
     * @param type $b
     * @return int 
     */
    public static function cmpSaturation($a, $b) {
        $a = self::factory($a)->getSaturation();
        $b = self::factory($b)->getSaturation();
        if ($a == $b)
            return 0;
        return ($a < $b) ? -1 : 1;
    }

    /**
     * Compare two colors length and colors parts
     * @param type $a
     * @param type $b
     * @return int 
     */
    public static function cmpVal($a, $b) {
        $a = self::factory($a);
        $b = self::factory($b);
        if ($a->length() == $b->length() && self::equals($a, $b))
            return 0;
        return ($a->length() < $b->length()) ? -1 : 1;
    }

    /**
     * Compare two colors length
     * @param type $a
     * @param type $b
     * @return int 
     */
    public static function cmp($a, $b) {
        $a = self::factory($a)->length();
        $b = self::factory($b)->length();
        if ($a == $b)
            return 0;
        return ($a < $b) ? -1 : 1;
    }

    /**
     * Sort array of colors by weight colors length
     * @param array $data
     * @return type 
     */
    public static function sortWeight(array & $data) {
        return usort($data, array(__CLASS__, 'cmpWight'));
    }

    /**
     * Sort array of colors by hues
     * @param array $data
     * @return type 
     */
    public static function sortHue(array & $data) {
        return usort($data, array(__CLASS__, 'cmpHue'));
    }

    /**
     * Sort array of colors by values (brightness)
     * @param array $data
     * @return type 
     */
    public static function sortValue(array & $data) {
        return usort($data, array(__CLASS__, 'cmpValue'));
    }

    /**
     * Sort array of colors by saturations
     * @param array $data
     * @return type 
     */
    public static function sortSaturation(array & $data) {
        return usort($data, array(__CLASS__, 'cmpSaturation'));
    }

    /**
     * Sort array of colors by length
     * @param array $data
     * @return type 
     */
    public static function sort(array & $data) {
        return usort($data, array(__CLASS__, 'cmp'));
    }

    ////////////////////////////////////////////////////////
    ////////////////// Instance members ////////////////////
    ////////////////////////////////////////////////////////
    ////////////////// Private Members /////////////////////
    private $_r = 0.0, $_g = 0.0, $_b = 0.0, $_a = 1.0;

    /**
     * Tokenize output string.
     * @param type $string
     * @return type 
     */
    private function _tokenize($string) {
        $tokens = array();
        $last = 0;
        for ($i = 0; $i < strlen($string); $i++) {
            $part = substr($string, $i, 2);
            if (in_array($part, self::$FORMATS)) {
                $text = substr($string, $last, $i - $last);
                if (!empty($text) && !in_array($text, self::$FORMATS))
                    $tokens[] = $text;
                $last = $i + 2;
                $tokens[] = $part;
            }
        }
        $text = substr($string, $last, strlen($string) - $last);
        if (!empty($text) && !in_array($text, self::$FORMATS))
            $tokens[] = $text;
        return $tokens;
    }
    /**
     * Process tokens to fit color in defined format
     * @param type $tokens
     * @return null 
     */
    private function _processTokens($tokens) {
        if (empty($tokens))
            return null;
        $result = '';
        if (is_array($tokens)) {
            foreach ($tokens as $t) {
                $result .= $this->_processTokens($t);
            }
            return $result;
        }
        ///////////////////////////////////////
        switch ($tokens) {
            case self::FORMAT_COLOR_R_HEX: $result = TColor_Convertor::FloatToHex($this->getR());
                break;
            case self::FORMAT_COLOR_G_HEX: $result = TColor_Convertor::FloatToHex($this->getG());
                break;
            case self::FORMAT_COLOR_B_HEX: $result = TColor_Convertor::FloatToHex($this->getB());
                break;
            //Float Format
            case self::FORMAT_COLOR_R_FLOAT: $result = $this->getR();
                break;
            case self::FORMAT_COLOR_G_FLOAT: $result = $this->getG();
                break;
            case self::FORMAT_COLOR_B_FLOAT: $result = $this->getB();
                break;
            case self::FORMAT_COLOR_A_FLOAT: $result = $this->getA();
                break;
            //Byte Format
            case self::FORMAT_COLOR_R_BYTE: $result = TColor_Convertor::FloatToByte($this->getR());
                break;
            case self::FORMAT_COLOR_G_BYTE: $result = TColor_Convertor::FloatToByte($this->getG());
                break;
            case self::FORMAT_COLOR_B_BYTE: $result = TColor_Convertor::FloatToByte($this->getB());
                break;
            case self::FORMAT_COLOR_A_BYTE: $result = TColor_Convertor::FloatToByte($this->getA());
                break;
            //CMYK format                
            case self::FORMAT_COLOR_C_CMYK: $data = $this->getCMYK();
                $result = $data['c'];
                break;
            case self::FORMAT_COLOR_M_CMYK: $data = $this->getCMYK();
                $result = $data['m'];
                break;
            case self::FORMAT_COLOR_Y_CMYK: $data = $this->getCMYK();
                $result = $data['y'];
                break;
            case self::FORMAT_COLOR_K_CMYK: $data = $this->getCMYK();
                $result = $data['k'];
                break;
            case self::FORMAT_COLOR_A_CMYK: $data = $this->getCMYK();
                $result = $data['a'];
                break;
            //HSV format
            case self::FORMAT_COLOR_H_HSV: $data = $this->getHSV();
                $result = $data['h'];
                break;
            case self::FORMAT_COLOR_S_HSV: $data = $this->getHSV();
                $result = $data['s'];
                break;
            case self::FORMAT_COLOR_V_HSV: $data = $this->getHSV();
                $result = $data['v'];
                break;
            case self::FORMAT_COLOR_A_HSV: $data = $this->getHSV();
                $result = $data['a'];
                break;
            //LAB format
            case self::FORMAT_COLOR_L_LAB: $data = $this->getLAB();
                $result = $data['h'];
                break;
            case self::FORMAT_COLOR_A_LAB: $data = $this->getLAB();
                $result = $data['s'];
                break;
            case self::FORMAT_COLOR_B_LAB: $data = $this->getLAB();
                $result = $data['v'];
                break;
            //default
            default: $result = $tokens;
        }
        return (!empty($result)) ? $result : 0;
    }

    ////////////////// Public Members //////////////////////
    /**
     * Add color to this
     * @param type $color
     * @param type $full
     * @return \TColor 
     */
    public function addColor($color, $full = true) {
        $color = self::factory($color);
        $this->setFloat(array(
            'r' => $this->getR($full) + $color->getR($full),
            'g' => $this->getG($full) + $color->getG($full),
            'b' => $this->getB($full) + $color->getB($full),
            'a' => $this->getA($full) + $color->getA($full),
        ));
        return $this;
    }
    /**
     * Sub color from this
     * @param type $color
     * @param type $full
     * @return \TColor 
     */
    public function subColor($color, $full = true) {
        $color = self::factory($color);
        $this->setFloat(array(
            'r' => $this->getR($full) - $color->getR($full),
            'g' => $this->getG($full) - $color->getG($full),
            'b' => $this->getB($full) - $color->getB($full),
            'a' => $this->getA($full) - $color->getA($full),
        ));
        return $this;
    }
    /**
     * Multiply this color by different color
     * @param type $color
     * @param type $full
     * @return \TColor 
     */
    public function multiplyColor($color, $full = true) {
        $color = self::factory($color);
        $this->setFloat(array(
            'r' => $this->getR($full) * $color->getR($full),
            'g' => $this->getG($full) * $color->getG($full),
            'b' => $this->getB($full) * $color->getB($full),
            'a' => $this->getA($full) * $color->getA($full),
        ));
        return $this;
    }
    /**
     * Divide this color by different color
     * @param type $color
     * @param type $full
     * @return \TColor 
     */
    public function divideColor($color, $full = true) {
        $color = self::factory($color);
        $this->setFloat(array(
            'r' => (!$color->getR($full)) ? 1.0 : $this->getR($full) / $color->getR($full),
            'g' => (!$color->getG($full)) ? 1.0 : $this->getG($full) / $color->getG($full),
            'b' => (!$color->getB($full)) ? 1.0 : $this->getB($full) / $color->getB($full),
            'a' => (!$color->getA($full)) ? 1.0 : $this->getA($full) / $color->getA($full),
        ));
        return $this;
    }
    /**
     * Dot product of this color and $color
     * @param type $color
     * @param type $full
     * @return type 
     */
    public function dotProduct($color, $full = false) {
        $color = self::factory($color);
        $dot = 0;
        $dot += $this->getR($full) * $color->getR($full);
        $dot += $this->getG($full) * $color->getG($full);
        $dot += $this->getB($full) * $color->getB($full);
        return $dot;
    }
    /**
     * Cross product of this color and $color
     * @param type $color
     * @param type $full
     * @return \TColor 
     */
    public function crossProduct($color, $full = false) {
        $color = self::factory($color);
        $this->setFloat(array(
            'r' => $this->getG($full) * $color->getB($full) - $this->getB($full) * $color->getG($full),
            'g' => $this->getB($full) * $color->getR($full) - $this->getR($full) * $color->getB($full),
            'b' => $this->getR($full) * $color->getG($full) - $this->getG($full) * $color->getR($full),
        ));
        return $this;
    }
    /**
     * Set min brightness if this brightness is smaller it is set to $f
     * @param type $f
     * @return \TColor 
     */
    public function setMinBrightness($f) {
        $color = TColor_Convertor::RGBtoHSV($this->getRGB());
        $color['v'] = ($color['v'] < $f) ? $f : $color['v'];
        $this->setHSV($color);
        return $this;
    }
    /**
     * Set min saturation if this saturation is smaller it is set to $f
     * @param type $f
     * @return \TColor 
     */
    public function setMinSaturation($f) {
        $color = TColor_Convertor::RGBtoHSV($this->getRGB());
        $color['s'] = ($color['s'] < $f) ? $f : $color['s'];
        $this->setHSV($color);
        return $this;
    }
    /**
     * Set max brightness if this brightness is higher it is set to $f
     * @param type $f
     * @return \TColor 
     */
    public function setMaxBrightness($f) {
        $color = TColor_Convertor::RGBtoHSV($this->getRGB());
        $color['v'] = ($color['v'] > $f) ? $f : $color['v'];
        $this->setHSV($color);
        return $this;
    }
    /**
     * Set max saturation if this saturation is higher it is set to $f
     * @param type $f
     * @return \TColor 
     */
    public function setMaxSaturation($f) {
        $color = TColor_Convertor::RGBtoHSV($this->getRGB());
        $color['s'] = ($color['s'] > $f) ? $f : $color['s'];
        $this->setHSV($color);
        return $this;
    }
    /**
     * Set brightness - value of color
     * @param type $f
     * @return \TColor 
     */
    public function setBrightness($f) {
        $color = TColor_Convertor::RGBtoHSV($this->getRGB());
        $color['v'] = $f;
        $this->setHSV($color);
        return $this;
    }
    /**
     * Set hue
     * @param type $f
     * @return \TColor 
     */
    public function setHue($f) {
        $color = TColor_Convertor::RGBtoHSV($this->getRGB());
        $color['h'] = TColor_Convertor::cyrcle($f);
        $this->setHSV($color);
        return $this;
    }
    /**
     * Set saturation
     * @param type $f
     * @return \TColor 
     */
    public function setSaturation($f) {
        $color = TColor_Convertor::RGBtoHSV($this->getRGB());
        $color['s'] = $f;
        $this->setHSV($color);
        return $this;
    }
    /**
     * Get brightness - value
     * @return type 
     */
    public function getBrightness() {
        $color = TColor_Convertor::RGBtoHSV($this->getRGB());
        return $color['v'];
    }
    /**
     * Get hue
     * @return type 
     */
    public function getHue() {
        $color = TColor_Convertor::RGBtoHSV($this->getRGB());
        return $color['h'];
    }
    /**
     * Get saturation
     * @return type 
     */
    public function getSaturation() {
        $color = TColor_Convertor::RGBtoHSV($this->getRGB());
        return $color['s'];
    }
    /**
     * Convert color to grayscale and return - it's not changing this color
     * @param type $r
     * @param type $g
     * @param type $b
     * @return type 
     */
    public function getGrayColor($r = 1, $g = 1, $b = 1) {
        $color = $r * $this->getR() + $g * $this->getG() + $b * $this->getB();
        $color = $color / ($r + $g + $b);
        return self::factory((float) $color);
    }
    /**
     * Average this color and $colors
     * @param type $color
     * @return type 
     */
    public function average($color) {
        $color = self::factory($color);
        if (is_array($color)) {
            foreach ($color as $c) {
                $this->addColor($c);
            }
            return $this->divideColor((float) count($color), true);
        } else {
            return $this->addColor($color)->divideColor((float) 2.0);
        }
    }
    /**
     * Inverse this color
     * @param type $full
     * @return \TColor 
     */
    public function inverse($full = false) {
        $this->setFloat(array(
            'r' => 1.0 - $this->getR($full),
            'g' => 1.0 - $this->getG($full),
            'b' => 1.0 - $this->getB($full),
            'a' => 1.0 - $this->getA($full),
        ));
        return $this;
    }
    /**
     * Inverse hue of this color
     * @return \TColor 
     */
    public function inverseHue() {
        $this->setHue($this->getHue() + 0.5);
        return $this;
    }
    /**
     * Get contrast color to this - it's not changing this color
     * @param type $sat - saturation
     * @param type $min - min brightness
     * @param type $hueOffset - hue offset
     * @param type $limit - contrast limit, if limit is exceeded function set brightness to zero of one depending on light or dark color, and set saturation to zero. It returns white or black color
     * @return type 
     */
    public function contrastColor($sat = 0, $min = 0, $hueOffset = 0, $limit = 140.0) {
        $color = self::factory($this)->getGrayColor(299.0, 587.0, 114.0);
        $color->setSaturation($sat);
        $color->setHue($hueOffset + $this->getHue());
        if ($color->getBrightness() > 0.5) {
            $color->setBrightness($min);
            if ($this->getContrast($color) < $limit) {
                $color->setBrightness(0.0);
            }
        } else {
            $color->setBrightness(1.0);
        }
        if ($this->getContrast($color) < $limit || $this->isGray()) {
            $color->setSaturation(0.0);
        }
        return $color;
    }
    /**
     * Get length of color vector wihout alpha channel
     * @param type $full
     * @return type 
     */
    public function length($full = false) {
        $f = 0;
        $f += $this->getR($full) * $this->getR($full);
        $f += $this->getG($full) * $this->getG($full);
        $f += $this->getB($full) * $this->getB($full);
        return sqrt($f);
    }
    /**
     * Return true if this color is gray
     * @return boolean 
     */
    public function isGray() {
        if (($this->getR() == $this->getG() && $this->getG() == $this->getB())) {
            return true;
        }
        return false;
    }
    /**
     * Return weight length of color vector wihout alpha channel
     * @param type $full
     * @return type 
     */
    public function weightLength($full = false) {
        $f = 0;
        $f += 299.0 * $this->getR($full) * $this->getR($full);
        $f += 587.0 * $this->getG($full) * $this->getG($full);
        $f += 114.0 * $this->getB($full) * $this->getB($full);
        return sqrt($f);
    }
    /**
     * Get contrast of this color and $color alias for brightnessDifference
     * @see brightnessDifference
     * @param type $color
     * @return type 
     */
    public function getContrast($color) {
        return $this->brightnessDifference($color);
    }
    /**
     * Get color difference
     * @param type $color
     * @return type 
     */
    public function colorDifference($color) {
        $c1 = self::factory($color)->getRGB();
        $c2 = $this->getRGB();
        $result = 0;
        $result += max($c1['r'], $c2['r']) - min($c1['r'], $c2['r']);
        $result += max($c1['g'], $c2['g']) - min($c1['g'], $c2['g']);
        $result += max($c1['b'], $c2['b']) - min($c1['b'], $c2['b']);
        return $result;
    }
    /**
     * Get color bright difference - contrast
     * @param type $color
     * @return type 
     */
    public function brightnessDifference($color) {
        $c1 = self::factory($color)->getRGB();
        $c2 = $this->getRGB();
        $b1 = (299.0 * $c1['r'] + 587.0 * $c1['g'] + 114.0 * $c1['b']) / 1000.0;
        $b2 = (299.0 * $c2['r'] + 587.0 * $c2['g'] + 114.0 * $c2['b']) / 1000.0;
        return abs($b1 - $b2);
    }
    /**
     * Get luminosity difference in HSL
     * @param type $color
     * @return type 
     */
    public function luminosityDifference($color) {
        $c1 = self::factory($color)->getRGB();
        $c2 = $this->getRGB();

        $L1 = 0.2126 * pow($c1['r'] / 255, 2.2) +
                0.7152 * pow($c1['g'] / 255, 2.2) +
                0.0722 * pow($c1['b'] / 255, 2.2);
        $L2 = 0.2126 * pow($c2['r'] / 255, 2.2) +
                0.7152 * pow($c2['g'] / 255, 2.2) +
                0.0722 * pow($c2['b'] / 255, 2.2);

        if ($L1 > $L2) {
            return ($L1 + 0.05) / ($L2 + 0.05);
        } else {
            return ($L2 + 0.05) / ($L1 + 0.05);
        }
    }

    ////////////////// Output Members //////////////////////
    /**
     * Convert this to string in defined format
     * @param type $format
     * @return type 
     */
    public function toString($format = TColor::FORMAT_HEXA) {
        return $this->_processTokens($this->_tokenize($format));
    }
    /**
     * Magic method to be able echo this object or convert it to string
     * @return string 
     */
    public function __toString() {
        $data = $this->toString();
        if (empty($data)) {
            $data = 'not set';
        }
        return $data;
    }

    ////////////////// Public Setters and getters //////////
    /**
     * get R
     * @param type $true
     * @return real 
     */
    public function getR($true = false) {
        if ($this->_r > 1.0 && !$true)
            return 1.0;
        if ($this->_r < 0.0 && !$true)
            return 0.0;
        return $this->_r;
    }
    /**
     * set R
     * @param type $_r 
     */
    public function setR($_r) {
        $this->_r = (float) $_r;
    }
    /**
     * get G
     * @param type $true
     * @return real 
     */
    public function getG($true = false) {
        if ($this->_g > 1.0 && !$true)
            return 1.0;
        if ($this->_g < 0.0 && !$true)
            return 0.0;
        return $this->_g;
    }
    /**
     * set G
     * @param type $_g \
     */
    public function setG($_g) {
        $this->_g = (float) $_g;
    }
    /**
     * get B
     * @param type $true
     * @return real 
     */
    public function getB($true = false) {
        if ($this->_b > 1.0 && !$true)
            return 1.0;
        if ($this->_b < 0.0 && !$true)
            return 0.0;
        return $this->_b;
    }
    /**
     * set B
     * @param type $_b 
     */
    public function setB($_b) {
        $this->_b = (float) $_b;
    }
    /**
     * get A
     * @param type $true
     * @return real 
     */
    public function getA($true = false) {
        if ($this->_a > 1.0 && !$true)
            return 1.0;
        return $this->_a;
    }
    /**
     * set A
     * @param type $_a 
     */
    public function setA($_a) {
        $this->_a = (float) $_a;
    }
    /**
     * Set All colors in float format array(r => X, g => X, b => X, a => X)
     * @param array $r
     * @return \TColor 
     */
    public function setFloat(array $r) {
        $this->_r = (float) (isset($r['r'])) ? $r['r'] : 0.0;
        $this->_g = (float) (isset($r['g'])) ? $r['g'] : 0.0;
        $this->_b = (float) (isset($r['b'])) ? $r['b'] : 0.0;
        $this->_a = (float) (isset($r['a'])) ? $r['a'] : 1.0;
        return $this;
    }
    /**
     * Set All colors in RGBA 0 - 255 format array(r => X, g => X, b => X, a => X)
     * @param array $color
     * @return type 
     */
    public function setRGB(array $color) {
        return $this->setFloat(TColor_Convertor::RGBtoFloat($color));
    }
    /**
     * Set All colors in float HSV format array(r => X, g => X, b => X, a => X)
     * @param array $color
     * @return type 
     */
    public function setHSV(array $color) {
        return $this->setFloat(TColor_Convertor::RGBtoFloat(TColor_Convertor::HSVtoRGB($color)));
    }
    /**
     * Get Array of RGB 0 - 255
     * @return type 
     */
    public function getRGB() {
        return TColor_Convertor::FloatToRGB(array(
                    'r' => $this->getR(),
                    'g' => $this->getG(),
                    'b' => $this->getB(),
                    'a' => $this->getA(),
                ));
    }
    /**
     * Get Array of CMYK
     * @return type 
     */
    public function getCMYK() {
        return TColor_Convertor::RGBtoCMYK(TColor_Convertor::FloatToRGB(array(
                            'r' => $this->getR(),
                            'g' => $this->getG(),
                            'b' => $this->getB(),
                            'a' => $this->getA(),
                        )));
    }
    /**
     * Get Array of HSV
     * @return type 
     */
    public function getHSV() {
        return TColor_Convertor::RGBtoHSV(TColor_Convertor::FloatToRGB(array(
                            'r' => $this->getR(),
                            'g' => $this->getG(),
                            'b' => $this->getB(),
                            'a' => $this->getA(),
                        )));
    }
    /**
     * Get Array of LAB
     * @return type 
     */
    public function getLAB() {
        return TColor_Convertor::RGBtoLAB(TColor_Convertor::FloatToRGB(array(
                            'r' => $this->getR(),
                            'g' => $this->getG(),
                            'b' => $this->getB(),
                            'a' => $this->getA(),
                        )));
    }
    /**
     * Get Array of HEX
     * @return type 
     */
    public function getHEX() {
        return TColor_Convertor::RGBtoHEX(TColor_Convertor::FloatToRGB(array(
                            'r' => $this->getR(),
                            'g' => $this->getG(),
                            'b' => $this->getB(),
                            'a' => $this->getA(),
                        )));
    }
    /**
     * Get Array of floats
     * @return type 
     */
    public function getFloat() {
        return array(
            'r' => $this->getR(),
            'g' => $this->getG(),
            'b' => $this->getB(),
            'a' => $this->getA(),
        );
    }

    ////////////////// Constructor /////////////////////////
    /**
     * Construct arguments must be floats in [ 0 - 1 ]
     * @param type $r
     * @param type $g
     * @param type $b
     * @param type $a
     * @throws TColor_MishmashedFormat 
     */
    public function __construct($r = 0, $g = 0, $b = 0, $a = 1) {
        if (is_array($r)) {
            $this->setFloat($r);
        } else {
            $this->_r = floatval($r);
            $this->_g = floatval($g);
            $this->_b = floatval($b);
            $this->_a = floatval($a);
        }
        if (is_float($this->_r) &&
                is_float($this->_g) &&
                is_float($this->_b) &&
                is_float($this->_a)) {
            // Do nothing
        } else if (is_int($this->_r) &&
                is_int($this->_g) &&
                is_int($this->_b) &&
                is_int($this->_a)) {
            // Maebye RGB ? Try recalculate and triger warning
            $this->_r = TColor_Convertor::ByteToFloat($this->_r);
            $this->_g = TColor_Convertor::ByteToFloat($this->_g);
            $this->_b = TColor_Convertor::ByteToFloat($this->_b);
            $this->_a = TColor_Convertor::ByteToFloat($this->_a);
            trigger_error('Input format should be float. Got Integer. Recalculating to float from RGB.', E_USER_WARNING);
        } else {
            throw new TColor_MishmashedFormat('Input format should be float. Got mishmashed formats.');
        }
    }

}

/**
 * Class for work with color text 
 */
class TColor_Text {
    //Formats
    const FORMAT_BACKGROUND = 'BG';
    const FORMAT_COLOR = 'CL';
    const FORMAT_COLOR_BACKGROUND = 'CLBG';
    const FORMAT_BACKGROUND_COLOR = 'BGCL';

    //////////////////////////////////////////////////
    /**
     * Get colorized text
     * @param type $color - color is evalued for every part
     * @param type $text
     * @param type $delimiter - delimiter where color will be evalued
     * @param type $sort - sort color for text ?
     * @param type $sorting - sorting algorythm ( hue | sat | val | wei | len )
     * @return type 
     */
    public static function getText($color, $text, $delimiter = ' ', $sort = false, $sorting = 'hue') {
        $result = new TColor_Text($color, $text, $delimiter);
        if ($sort)
            $result = $result->sortColors($sorting);
        return $result;
    }
    /**
     * Generate random colorized text
     * @param type $text
     * @param type $delimiter
     * @param type $sort
     * @param type $sorting
     * @return type 
     */
    public static function getRandomColorText($text, $delimiter = ' ', $sort = false, $sorting = 'hue') {
        return self::getText('randomfull', $text, $delimiter, $sort, $sorting);
    }

    //////////////////////////////////////////////////
    private $text = '';
    private $colors = array();
    private $delimiter = null;
    /**
     * Convert object to string in format
     * @param type $format
     * @return type 
     */
    public function toString($format = self::FORMAT_COLOR) {
        $result = '';
        $tag = '<span style="%s">%s</span>';
        if (!is_null($this->delimiter)) {
            $tag = '<span style="%s">%s' . $this->delimiter . '</span>';
        }
        if ($format == self::FORMAT_COLOR_BACKGROUND ||
                $format == self::FORMAT_BACKGROUND_COLOR) {
            $contrast = TColor::factory(0)->average($this->colors)->contrastColor(0.2, 0.2);
        }
        foreach ($this->colors as $k => $color) {
            if (!isset($this->text[$k]))
                continue;
            $letter = $this->text[$k];
            switch ($format) {
                case self::FORMAT_BACKGROUND: $result .= sprintf($tag, $color->toString(TColor::FORMAT_CSS_BACKGROUND), $letter);
                    break;
                case self::FORMAT_COLOR: $result .= sprintf($tag, $color->toString(TColor::FORMAT_CSS_COLOR), $letter);
                    break;
                case self::FORMAT_COLOR_BACKGROUND: $result .= sprintf($tag, $contrast->toString(TColor::FORMAT_CSS_COLOR) . $color->toString(TColor::FORMAT_CSS_BACKGROUND), $letter);
                    break;
                case self::FORMAT_BACKGROUND_COLOR: $result .= sprintf($tag, $color->toString(TColor::FORMAT_CSS_COLOR) . $contrast->toString(TColor::FORMAT_CSS_BACKGROUND), $letter);
                    break;
            }
        }
        return $result;
    }
    /**
     * Convert object to string magic method
     * @return string 
     */
    public function __toString() {
        $data = $this->toString();
        if (empty($data)) {
            return 'not set';
        }
        return $data;
    }
    /**
     * Manipulate with all colors, set bright etc. @see TColor
     * @param type $name
     * @param type $arguments
     * @return \TColor_Text 
     */
    public function __call($name, $arguments) {
        foreach ($this->colors as $k => $v) {
            $this->colors[$k]->$name(current($arguments));
        }
        return $this;
    }
    /**
     * Sort colors - sorting algorythm ( hue | sat | val | wei | len )
     * @param type $sorting
     * @return \TColor_Text 
     */
    public function sortColors($sorting = 'hue') {
        switch ($sorting) {
            case 'hue':
            case 'HUE':
            case 'Hue':
                TColor::sort($this->colors);
                break;
            case 'sat':
            case 'SAT':
            case 'Sat':
            case 'saturation':
                TColor::sort($this->colors);
                break;
            case 'val':
            case 'VAL':
            case 'Val':
            case 'value':
                TColor::sort($this->colors);
                break;
            case 'wei':
            case 'WEI':
            case 'Wei':
            case 'weight':
                TColor::sort($this->colors);
                break;
            case 'len':
            case 'LEN':
            case 'Len':
            case 'length':
                TColor::sort($this->colors);
                break;
            default : TColor::sortHue($this->colors);
        }
        return $this;
    }
    /**
     * Construct
     * @param type $color
     * @param type $text
     * @param type $delimiter 
     */
    public function __construct($color, $text, $delimiter = null) {
        $this->delimiter = $delimiter;
        if (!is_null($delimiter)) {
            $this->text = explode($delimiter, $text);
            for ($i = 0; $i < count($this->text); $i++) {
                $this->colors[] = TColor::factory($color);
            }
        } else {
            for ($i = 0; $i < strlen($text); $i++) {
                $this->colors[] = TColor::factory($color);
                $this->text[$i] = $text[$i];
            }
        }
    }

}