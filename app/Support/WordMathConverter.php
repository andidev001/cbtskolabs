<?php

namespace App\Support;

class WordMathConverter
{
    /**
     * Ubah semua blok <m:oMath> di dalam XML Word menjadi ekspresi LaTeX inline \( ... \) di dalam <w:t>.
     */
    public static function convertOmmlInXml(string $xml): string
    {
        if (stripos($xml, 'oMath') === false) {
            return $xml;
        }

        // Hapus pembungkus <m:oMathPara> namun biarkan <m:oMath> di dalamnya
        $xml = preg_replace('/<\/?m:oMathPara(?:Pr)?\b[^>]*>/i', '', $xml);

        return preg_replace_callback('/<m:oMath\b[^>]*>[\s\S]*?<\/m:oMath>/i', function ($matches) {
            try {
                $latex = self::ommlBlockToLatex($matches[0]);
                $latex = trim(preg_replace('/\s+/', ' ', $latex));
                if ($latex === '') {
                    return '';
                }
                return '<w:t>\\(' . htmlspecialchars($latex, ENT_XML1 | ENT_COMPAT, 'UTF-8') . '\\)</w:t>';
            } catch (\Throwable $e) {
                // Fallback ambil teks murninya jika XML tidak standar
                $plain = trim(preg_replace('/\s+/', ' ', strip_tags($matches[0])));
                return '<w:t>' . htmlspecialchars($plain, ENT_XML1 | ENT_COMPAT, 'UTF-8') . '</w:t>';
            }
        }, $xml);
    }

    /**
     * Konversi satu blok <m:oMath>...</m:oMath> menjadi string LaTeX menggunakan DOMDocument.
     */
    public static function ommlBlockToLatex(string $ommlXml): string
    {
        // Pastikan namespace m: dan w: terdefinisi agar DOMDocument tidak warning
        if (strpos($ommlXml, 'xmlns:m=') === false) {
            $ommlXml = preg_replace(
                '/^<m:oMath\b/i',
                '<m:oMath xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math" xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"',
                $ommlXml,
                1
            );
        }

        $dom = new \DOMDocument();
        $prev = libxml_use_internal_errors(true);
        $loaded = $dom->loadXML('<?xml version="1.0" encoding="UTF-8"?>' . $ommlXml, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        if (!$loaded || !$dom->documentElement) {
            return trim(strip_tags($ommlXml));
        }

        return self::nodeToLatex($dom->documentElement);
    }

    private static function nodeToLatex(\DOMNode $node): string
    {
        if ($node->nodeType !== XML_ELEMENT_NODE) {
            return '';
        }

        $localName = $node->localName ?: preg_replace('/^.*:/', '', $node->nodeName);

        // Abaikan node properti konfigurasi internal OMML
        if (in_array($localName, [
            'rPr', 'ctrlPr', 'fPr', 'radPr', 'sSupPr', 'sSubPr', 'sSubSupPr',
            'naryPr', 'dPr', 'mPr', 'funcPr', 'limLowPr', 'limUppPr',
            'accPr', 'barPr', 'eqArrPr', 'boxPr', 'borderBoxPr', 'groupChrPr', 'sPrePr'
        ], true)) {
            return '';
        }

        switch ($localName) {
            case 't':
                return self::normalizeMathText($node->textContent);

            case 'f': // Pecahan: \frac{num}{den}
                $num = self::childLatex($node, 'num');
                $den = self::childLatex($node, 'den');
                return '\\frac{' . $num . '}{' . $den . '}';

            case 'rad': // Akar: \sqrt{e} atau \sqrt[deg]{e}
                $deg = trim(self::childLatex($node, 'deg'));
                $e = self::childLatex($node, 'e');
                return $deg !== '' ? '\\sqrt[' . $deg . ']{' . $e . '}' : '\\sqrt{' . $e . '}';

            case 'sSup': // Pangkat: {e}^{sup}
                $e = self::childLatex($node, 'e');
                $sup = self::childLatex($node, 'sup');
                return '{' . $e . '}^{' . $sup . '}';

            case 'sSub': // Indeks bawah: {e}_{sub}
                $e = self::childLatex($node, 'e');
                $sub = self::childLatex($node, 'sub');
                return '{' . $e . '}_{' . $sub . '}';

            case 'sSubSup': // Indeks bawah & pangkat: {e}_{sub}^{sup}
                $e = self::childLatex($node, 'e');
                $sub = self::childLatex($node, 'sub');
                $sup = self::childLatex($node, 'sup');
                return '{' . $e . '}_{' . $sub . '}^{' . $sup . '}';

            case 'sPre': // Pre-script
                $sub = self::childLatex($node, 'sub');
                $sup = self::childLatex($node, 'sup');
                $e = self::childLatex($node, 'e');
                return '{}_{' . $sub . '}^{' . $sup . '}{' . $e . '}';

            case 'nary': // Integral, Sigma, Product
                $chr = self::findPropVal($node, 'naryPr', 'chr') ?: '∫';
                $opMap = [
                    '∫' => '\\int',
                    '∬' => '\\iint',
                    '∭' => '\\iiint',
                    '∮' => '\\oint',
                    '∑' => '\\sum',
                    '∏' => '\\prod',
                    '⋃' => '\\bigcup',
                    '⋂' => '\\bigcap',
                ];
                $op = $opMap[$chr] ?? '\\int';
                $sub = trim(self::childLatex($node, 'sub'));
                $sup = trim(self::childLatex($node, 'sup'));
                $e = self::childLatex($node, 'e');
                $res = $op;
                if ($sub !== '') {
                    $res .= '_{' . $sub . '}';
                }
                if ($sup !== '') {
                    $res .= '^{' . $sup . '}';
                }
                return $res . ' {' . $e . '}';

            case 'd': // Delimiter / Tanda kurung ( ), [ ], { }, | |
                $beg = self::findPropVal($node, 'dPr', 'begChr');
                $end = self::findPropVal($node, 'dPr', 'endChr');
                $sep = self::findPropVal($node, 'dPr', 'sepChr');
                if ($beg === null) $beg = '(';
                if ($end === null) $end = ')';
                if ($sep === null) $sep = ',';

                $parts = [];
                foreach ($node->childNodes as $child) {
                    if ($child->nodeType === XML_ELEMENT_NODE) {
                        $cName = $child->localName ?: preg_replace('/^.*:/', '', $child->nodeName);
                        if ($cName === 'e') {
                            $parts[] = self::childrenToLatex($child);
                        }
                    }
                }
                $leftTex = self::delimiterToLatex($beg, true);
                $rightTex = self::delimiterToLatex($end, false);
                return $leftTex . implode($sep . ' ', $parts) . $rightTex;

            case 'm': // Matriks
                $rows = [];
                foreach ($node->childNodes as $child) {
                    if ($child->nodeType === XML_ELEMENT_NODE) {
                        $cName = $child->localName ?: preg_replace('/^.*:/', '', $child->nodeName);
                        if ($cName === 'mr') {
                            $cols = [];
                            foreach ($child->childNodes as $cell) {
                                if ($cell->nodeType === XML_ELEMENT_NODE) {
                                    $cellName = $cell->localName ?: preg_replace('/^.*:/', '', $cell->nodeName);
                                    if ($cellName === 'e') {
                                        $cols[] = self::childrenToLatex($cell);
                                    }
                                }
                            }
                            $rows[] = implode(' & ', $cols);
                        }
                    }
                }
                return '\\begin{matrix} ' . implode(' \\\\ ', $rows) . ' \\end{matrix}';

            case 'eqArr': // Sistem persamaan / baris persamaan
                $rows = [];
                foreach ($node->childNodes as $child) {
                    if ($child->nodeType === XML_ELEMENT_NODE) {
                        $cName = $child->localName ?: preg_replace('/^.*:/', '', $child->nodeName);
                        if ($cName === 'e') {
                            $rows[] = self::childrenToLatex($child);
                        }
                    }
                }
                return '\\begin{aligned} ' . implode(' \\\\ ', $rows) . ' \\end{aligned}';

            case 'func': // Fungsi seperti sin, cos, tan, log, lim
                $fName = trim(self::childLatex($node, 'fName'));
                $e = self::childLatex($node, 'e');
                if (preg_match('/^(sin|cos|tan|cot|sec|csc|arcsin|arccos|arctan|log|ln|lim|max|min)$/i', $fName)) {
                    $fName = '\\' . strtolower($fName);
                }
                return $fName . ' {' . $e . '}';

            case 'limLow': // Limit bawah, misal \lim_{x \to 0}
                $e = trim(self::childLatex($node, 'e'));
                $lim = self::childLatex($node, 'lim');
                if (preg_match('/^(?:\\\\)?lim$/i', $e)) {
                    return '\\lim_{' . $lim . '}';
                }
                return '\\underset{' . $lim . '}{' . $e . '}';

            case 'limUpp': // Limit atas
                $e = self::childLatex($node, 'e');
                $lim = self::childLatex($node, 'lim');
                return '\\overset{' . $lim . '}{' . $e . '}';

            case 'acc': // Aksen di atas huruf (vektor, topi, bar)
                $chr = self::findPropVal($node, 'accPr', 'chr') ?: '̂';
                $e = self::childLatex($node, 'e');
                if (in_array($chr, ['⃗', '→', "\u{20D7}"], true)) {
                    return '\\vec{' . $e . '}';
                }
                if (in_array($chr, ['¯', '‾', "\u{0304}", "\u{0305}"], true)) {
                    return '\\bar{' . $e . '}';
                }
                if (in_array($chr, ['~', '˜', "\u{0303}"], true)) {
                    return '\\tilde{' . $e . '}';
                }
                return '\\hat{' . $e . '}';

            case 'bar': // Garis atas / bawah
                $pos = self::findPropVal($node, 'barPr', 'pos');
                $e = self::childLatex($node, 'e');
                return $pos === 'bot' ? '\\underline{' . $e . '}' : '\\overline{' . $e . '}';

            case 'groupChr': // Kurung kurawal bawah/atas
                $e = self::childLatex($node, 'e');
                return '\\underbrace{' . $e . '}';

            default:
                return self::childrenToLatex($node);
        }
    }

    private static function childrenToLatex(\DOMNode $node): string
    {
        $out = '';
        foreach ($node->childNodes as $child) {
            $out .= self::nodeToLatex($child);
        }
        return $out;
    }

    private static function childLatex(\DOMNode $parent, string $targetLocalName): string
    {
        foreach ($parent->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                $cName = $child->localName ?: preg_replace('/^.*:/', '', $child->nodeName);
                if ($cName === $targetLocalName) {
                    return self::childrenToLatex($child);
                }
            }
        }
        return '';
    }

    private static function findPropVal(\DOMNode $parent, string $propTagName, string $childTagName): ?string
    {
        foreach ($parent->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                $cName = $child->localName ?: preg_replace('/^.*:/', '', $child->nodeName);
                if ($cName === $propTagName) {
                    foreach ($child->childNodes as $sub) {
                        if ($sub->nodeType === XML_ELEMENT_NODE) {
                            $sName = $sub->localName ?: preg_replace('/^.*:/', '', $sub->nodeName);
                            if ($sName === $childTagName && $sub->hasAttributes()) {
                                foreach ($sub->attributes as $attr) {
                                    $aName = $attr->localName ?: preg_replace('/^.*:/', '', $attr->nodeName);
                                    if ($aName === 'val') {
                                        return $attr->nodeValue;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return null;
    }

    private static function delimiterToLatex(string $chr, bool $isLeft): string
    {
        $chr = trim($chr);
        if ($chr === '') {
            return $isLeft ? '\\left.' : '\\right.';
        }
        $map = [
            '(' => '(',
            ')' => ')',
            '[' => '[',
            ']' => ']',
            '{' => '\\{',
            '}' => '\\}',
            '|' => '|',
            '‖' => '\\|',
            '⟨' => '\\langle ',
            '⟩' => '\\rangle ',
        ];
        $tex = $map[$chr] ?? ($isLeft ? '(' : ')');
        return ($isLeft ? '\\left' : '\\right') . $tex;
    }

    private static function normalizeMathText(string $text): string
    {
        $replacements = [
            '×' => ' \\times ',
            '÷' => ' \\div ',
            '±' => ' \\pm ',
            '∓' => ' \\mp ',
            '≤' => ' \\le ',
            '≥' => ' \\ge ',
            '≠' => ' \\ne ',
            '≈' => ' \\approx ',
            '∞' => '\\infty ',
            '→' => ' \\to ',
            '←' => ' \\leftarrow ',
            '↔' => ' \\leftrightarrow ',
            '⇒' => ' \\Rightarrow ',
            '⇔' => ' \\Leftrightarrow ',
            '°' => '^{\\circ}',
            'π' => '\\pi ',
            'α' => '\\alpha ',
            'β' => '\\beta ',
            'γ' => '\\gamma ',
            'δ' => '\\delta ',
            'Δ' => '\\Delta ',
            'θ' => '\\theta ',
            'λ' => '\\lambda ',
            'μ' => '\\mu ',
            'σ' => '\\sigma ',
            'ω' => '\\omega ',
            'Ω' => '\\Omega ',
            '⋅' => ' \\cdot ',
            '∙' => ' \\cdot ',
            '−' => '-',
        ];

        return strtr($text, $replacements);
    }
}
