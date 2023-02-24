<?php

/*
 * Copyright 2018 Metzli authors
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

use FPDF;
use Metzli\Encoder\AztecCode;

class FpdfRenderer implements RendererInterface
{
    private $pdf;
    private $x;
    private $y;
    private $size;
    private $border;
    private $fgColor;
    private $bgColor;
    private $padding = array('T' => 0, 'R' => 0, 'B' => 0, 'L' => 0);

    public function __construct(FPDF $pdf, $x, $y, $size, $border = 0, $fgColor = array(0, 0, 0), $bgColor = null, $fsColor = null, $padding = array(0, 0, 0, 0))
    {
        $this->pdf = $pdf;
        $this->x = $x;
        $this->y = $y;
        $this->size = $size;
        $this->border = $border;
        $this->fgColor = $fgColor;
        $this->bgColor = $bgColor;
        $this->fsColor = $fsColor;
        $this->padding = $this->setpadding($padding);
    }

    private function setpadding($padding)
    {
        $par = ['T', 'R', 'B', 'L'];

        foreach ($padding as $key => $val) {
            $val = intval($val);
            $pattern[$par[$key][0]] = $val;
        }
        return $pattern;
    }

    public function render(AztecCode $code)
    {
        $this->pdf->SetLineWidth(0.5);
        $this->pdf->SetDrawColor($this->fgColor[0], $this->fgColor[1], $this->fgColor[2]);
        $border = $this->border == 0 ? 'F' : 'DF';

        $sx = $this->x-$this->padding['L']-0.1;
        $sy = $this->y-$this->padding['T']-0.1;

        $width = $this->size + $this->padding['R'] + $this->padding['L']+0.2;
        $height = $this->size + $this->padding['B'] + $this->padding['T']+0.2;

        $matrix = $code->getMatrix();

        $cellWidth = $this->size / $matrix->getWidth();
        $cellHeight = $this->size / $matrix->getHeight();

        if (!empty($this->bgColor)) {
            $this->pdf->SetFillColor($this->bgColor[0], $this->bgColor[1], $this->bgColor[2]);
            $this->pdf->Rect($sx, $sy, $width, $height, $border);
        }

        if (empty($this->bgColor) AND $this->border == 1) {
            $this->pdf->Rect($sx, $sy, $width, $height, 'D');
        }

        if (!empty($this->fsColor)) {
            $this->pdf->SetFillColor($this->fsColor[0], $this->fsColor[1], $this->fsColor[2]);
        }

        $this->pdf->SetLineWidth(0.1);

        for ($x = 0; $x < $matrix->getWidth(); $x++) {
            for ($y = 0; $y < $matrix->getHeight(); $y++) {
                if ($matrix->get($x, $y)) {
                    $this->pdf->SetFillColor($this->fgColor[0], $this->fgColor[1], $this->fgColor[2]);
                    $this->pdf->SetDrawColor($this->fgColor[0], $this->fgColor[1], $this->fgColor[2]);
                    $this->pdf->Rect($this->x+$x*$cellWidth, $this->y+$y*$cellHeight, $cellWidth, $cellHeight, 'DF');
                } elseif ($this->fsColor !== null) {
                    $this->pdf->SetFillColor($this->fsColor[0], $this->fsColor[1], $this->fsColor[2]);
                    $this->pdf->SetDrawColor(255, 255, 0);
                    $this->pdf->Rect($this->x+$x*$cellWidth, $this->y+$y*$cellHeight, $cellWidth, $cellHeight, 'DF');
                }

            }
        }
    }
}
