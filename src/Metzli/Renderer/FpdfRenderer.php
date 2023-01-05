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
    private $pattern;
    private $fgColor;
    private $bgColor;

    public function __construct(FPDF $pdf, $x, $y, $size, $border = 0, $pattern = 0, $fgColor = array(0, 0, 0), $bgColor = null)
    {
        $this->pdf = $pdf;
        $this->x = $x;
        $this->y = $y;
        $this->size = $size;
        $this->border = $border;
        $this->pattern = $pattern;
        $this->fgColor = $fgColor;
        $this->bgColor = $bgColor;
    }

    public function render(AztecCode $code)
    {
        $matrix = $code->getMatrix();

        $border = $this->border == 0 ? 'F' : 'FD';

        $cellWidth = $this->size / $matrix->getWidth();
        $cellHeight = $this->size / $matrix->getHeight();

        if (!empty($this->bgColor)) {
            $this->pdf->SetFillColor($this->bgColor[0], $this->bgColor[1], $this->bgColor[2]);
            $this->pdf->SetDrawColor($this->fgColor[0], $this->fgColor[1], $this->fgColor[2]);
            if ($this->pattern == 0) {
                $this->pdf->Rect($this->x-0.4, $this->y-0.4, $this->size+0.2, $this->size+0.2, $border);
                }
                elseif ($this->pattern != 0) {
                $this->pdf->Rect($this->x-$this->pattern, $this->y-$this->pattern, $this->size+($this->pattern*2)-0.6, $this->size+($this->pattern*2)-0.6, $border);
            }
        }

        if (empty($this->bgColor) AND $this->border == 1) {
            $this->pdf->SetDrawColor($this->fgColor[0], $this->fgColor[1], $this->fgColor[2]);
            if ($this->pattern == 0) {
                $this->pdf->Rect($this->x-0.3, $this->y-0.3, $this->size+0.1, $this->size+0.1, 'D');
                }
                elseif ($this->pattern != 0) {
                    $this->pdf->Rect($this->x-$this->pattern, $this->y-$this->pattern, $this->size+($this->pattern*2)-0.6, $this->size+($this->pattern*2)-0.6, 'D');
            }
        }
        
        $this->pdf->SetFillColor($this->fgColor[0], $this->fgColor[1], $this->fgColor[2]);
        $this->pdf->SetDrawColor($this->fgColor[0], $this->fgColor[1], $this->fgColor[2]);
        
        for ($x = 0; $x < $matrix->getWidth(); $x++) {
            for ($y = 0; $y < $matrix->getHeight(); $y++) {
                if ($matrix->get($x, $y)) {
                    $this->pdf->Rect($this->x+($x-0.15)*$cellWidth, $this->y+($y-0.15)*$cellHeight, $cellWidth-0.15, $cellHeight-0.15, 'DF');
                }
            }
        }
    }
}
