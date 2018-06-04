<?php

namespace SivarajD\LetterAvatar;

use Intervention\Image\ImageManager;

class LetterAvatar
{
    /**
     * @var string
     */
    protected $name;


    /**
     * @var string
     */
    protected $name_initials;


    /**
     * @var string
     */
    protected $shape;


    /**
     * @var int
     */
    protected $size;

    /**
     * @var int
     */
    protected $saturation;

    /**
     * @var int
     */
    protected $luminosity;

    /**
     * @var ImageManager
     */
    protected $image_manager;


    public function __construct($name, $shape = 'circle', $size = '48',$saturation = '50', $luminosity = '75')
    {
        $this->setName($name);
        $this->setImageManager(new ImageManager());
        $this->setShape($shape);
        $this->setSize($size);
        $this->setSaturation($saturation);
        $this->setLuminosity($luminosity);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return ImageManager
     */
    public function getImageManager()
    {
        return $this->image_manager;
    }

    /**
     * @param ImageManager $image_manager
     */
    public function setImageManager(ImageManager $image_manager)
    {
        $this->image_manager = $image_manager;
    }

    /**
     * @return string
     */
    public function getShape()
    {
        return $this->shape;
    }

    /**
     * @param string $shape
     */
    public function setShape($shape)
    {
        $this->shape = $shape;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getSaturation()
    {
        return $this->saturation;
    }

    /**
     * @param int $saturation
     */
    public function setSaturation($saturation)
    {
        $this->saturation = $saturation;
    }
    
    /**
     * @return int
     */
    public function getLuminosity()
    {
        return $this->luminosity;
    }

    /**
     * @param int $luminosity
     */
    public function setLuminosity($luminosity)
    {
        $this->luminosity = $luminosity;
    }
    

    /**
     * @return \Intervention\Image\Image
     */
    public function generate()
    {
        $words = $this->break_words($this->name);

        $number_of_word = 1;
        $this->name_initials = '';
        foreach ($words as $word) {

            if ($number_of_word > 2)
                break;

            $this->name_initials .= mb_strtoupper(trim(mb_substr($word, 0, 1, 'UTF-8')));

            $number_of_word++;
        }

        $color = $this->stringToColor($this->name);

        if ($this->shape == 'circle') {
            $canvas = $this->image_manager->canvas(480, 480);

            $canvas->circle(480, 240, 240, function ($draw) use ($color) {
                $draw->background($color);
            });

        } else {

            $canvas = $this->image_manager->canvas(480, 480, $color);
        }

        $canvas->text($this->name_initials, 240, 240, function ($font) {
            $font->file(__DIR__ . '/fonts/unicode.ttf');
            $font->size(220);
            $font->color('#ffffff');
            $font->valign('middle');
            $font->align('center');
        });

        return $canvas->resize($this->size, $this->size);
    }

    public function saveAs($path, $mimetype = 'image/png', $quality = 90)
    {
        if(empty($path) || empty($mimetype) || $mimetype != "image/png" && $mimetype != 'image/jpeg'){
            return false;
        }

        return @file_put_contents($path, $this->generate()->encode($mimetype, $quality));
    }

    public function __toString()
    {
        return (string) $this->generate()->encode('data-url');
    }

    public function break_words($name) {
        $temp_word_arr = explode(' ', $name);
        $final_word_arr = array();
        foreach ($temp_word_arr as $key => $word) {
            if( $word != "" && $word != ",") {
                $final_word_arr[] = $word;
            }
        }
        return $final_word_arr;
    }

    protected function hsl2rgb ($h, $s, $l) {

        $h /= 60;
        if ($h < 0) $h = 6 - fmod(-$h, 6);
        $h = fmod($h, 6);
    
        $s = max(0, min(1, $s / 100));
        $l = max(0, min(1, $l / 100));
    
        $c = (1 - abs((2 * $l) - 1)) * $s;
        $x = $c * (1 - abs(fmod($h, 2) - 1));
    
        if ($h < 1) {
            $r = $c;
            $g = $x;
            $b = 0;
        } elseif ($h < 2) {
            $r = $x;
            $g = $c;
            $b = 0;
        } elseif ($h < 3) {
            $r = 0;
            $g = $c;
            $b = $x;
        } elseif ($h < 4) {
            $r = 0;
            $g = $x;
            $b = $c;
        } elseif ($h < 5) {
            $r = $x;
            $g = 0;
            $b = $c;
        } else {
            $r = $c;
            $g = 0;
            $b = $x;
        }
    
        $m = $l - $c / 2;
        $r = round(($r + $m) * 255);
        $g = round(($g + $m) * 255);
        $b = round(($b + $m) * 255);
    
        return ['r' => $r, 'g' => $g, 'b' => $b];    
    }


    protected function stringToColor($string)
    {
        // 0xFFF = 4095; divided by 360 (max for hue) gives 11.375
        // This helps in getting a full range of hues from 0 to 360
        $hue = round(hexdec(substr(md5($string),0,3))/11.375);
        $rgb = $this->hsl2rgb($hue,$this->saturation,$this->luminosity);
        return sprintf("#%02X%02X%02X",$rgb['r'],$rgb['g'],$rgb['b']);
    }
    
}
