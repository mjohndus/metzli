<?php

/*
 * Copyright 2019 Metzli authors
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Metzli\Renderer;

use Metzli\Encoder\AztecCode;

class SvgRenderer implements RendererInterface
{
/*
    private $factor;
    private $border = array();
    //private $padding = array();
    private $padding = array('T' => 0, 'R' => 0, 'B' => 0, 'L' => 0);
    private $fgColor;
    private $bgColor;
    private $fsColor;
    private $bdColor;
*/
    public function __construct($factor = 4, $border = array(0, 0), $padding = array(0, 0, 0, 0), $fgColor = '#000000', $bgColor = null, $fsColor = null, $bdColor = null)
    {
        $this->factor = $factor;
        $this->border = $border;
        $this->padding = $this->setpadding($padding);
        $this->fgColor = $fgColor;
        $this->bgColor = $bgColor;
        $this->fsColor = $fsColor;
        $this->bdColor = $bdColor;
    }

    private function setpadding($padding)
    {
        $par = ['T', 'R', 'B', 'L'];

        foreach ($padding as $key => $val) {
            $val = intval($val);
            $pattern[$par[$key][0]] = $val;
        }
        return $pattern;
        //return $this;
    }

    public function render(AztecCode $code)
    {
        $f = $this->factor;
        $border = $this->border;
        $pattern = $this->padding;
        $fg = $this->fgColor;
        $bg = $this->bgColor;
        $fs = $this->fsColor;
        $bd = $this->bdColor;
        //$pattern = 40;

        $matrix = $code->getMatrix();
        $width = ($matrix->getWidth() * $f) + ($pattern['R']+$pattern['L']);
        $height = ($matrix->getHeight() * $f) + ($pattern['T']+$pattern['B']);

        if (array_sum($this->padding) <= 35) {
            $bw = 0;
            $br = 0;
          } else {
            $bw = $border[1];
            $br = 15;
        }

        $svg = '<?xml version="1.0" standalone="no"?>'
              .'<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd"> '
              .'<svg'
              .' width="'.$width.'"'
              .' height="'.$height.'"'
//              .' viewBox="0 0 '.($matrix->getWidth() * $f + 20).' '.($matrix->getHeight() * $f + 20).'"'
              .' version="1.1"'
              .' xmlns="http://www.w3.org/2000/svg">'

//              .'<g id="barcode" fill="#' . ltrim($this->fgColor, '#') . '">'
              .'<rect'
              .' x="'.($bw/2).'"'
              .' y="'.($bw/2).'"'
              .' rx="'.$br.'"'
              .' ry="'.$br.'"'
              .' width="'.$width-$bw.'"'
              .' height="'.$height-$bw.'"';
        if (!empty($this->bgColor)) {
              $svg .= ' fill="'.$bg.'"';
              } else {
              $svg .= ' fill=none';
        }
        if (!empty($this->bdColor) AND $border[0] != 0) {
              $svg .= ' stroke="'.$bd.'"'
              .' stroke-width="'.$bw.'"'
              .' stroke-linecap="square"';
        }
              $svg .= ' />'
              .'<g id="barcode">';

        for ($x = 0; $x < $matrix->getWidth(); $x++) {
            for ($y = 0; $y < $matrix->getHeight(); $y++) {
                if ($matrix->get($x, $y)) {
                    $svg .= '<rect x="'.(($x*$f)+$pattern['L']).'" y="'.(($y*$f)+$pattern['T']).'" width="'.$f.'" height="'.$f.'"  fill="'.$fg.'" />';
                }
                  else {
                    $svg .= '<rect x="'.(($x*$f)+$pattern['L']).'" y="'.(($y*$f)+$pattern['T']).'" width="'.$f.'" height="'.$f.'" fill="'.$fs.'" />';
                }
            }
        }

        $svg .= '</g></svg>';

        return $svg;
    }
}
