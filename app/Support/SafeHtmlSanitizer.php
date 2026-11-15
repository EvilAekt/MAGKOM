<?php

declare(strict_types=1);

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

class SafeHtmlSanitizer
{
    /**
     * Tag HTML yang diizinkan untuk formatting teks petunjuk tugas.
     * Seluruh atribut (seperti event handler on*, style, class, href javascript, dll.) akan dibersihkan.
     */
    private const ALLOWED_TAGS = [
        'p', 'br', 'b', 'strong', 'i', 'em', 'u', 'ul', 'ol', 'li',
    ];

    /**
     * Tag yang wajib dihapus beserta seluruh konten anak di dalamnya karena berbahaya.
     */
    private const DANGEROUS_TAGS = [
        'script', 'style', 'iframe', 'object', 'embed', 'applet', 'svg', 'canvas',
        'form', 'input', 'button', 'select', 'textarea', 'frame', 'frameset',
    ];

    /**
     * Sanitasi string HTML: hanya mempertahankan tag aman tanpa atribut apa pun.
     */
    public static function sanitize(?string $html): string
    {
        if ($html === null) {
            return '';
        }

        $trimmed = trim($html);
        if ($trimmed === '') {
            return '';
        }

        // Jika tidak mengandung karakter '<', berarti teks polos biasa
        if (! str_contains($trimmed, '<')) {
            return $trimmed;
        }

        $dom = new DOMDocument;
        libxml_use_internal_errors(true);

        // Gunakan encoding HTML-ENTITIES agar karakter multibyte tidak rusak
        $encoded = mb_convert_encoding($trimmed, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML('<div>'.$encoded.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $root = $dom->getElementsByTagName('div')->item(0);
        if (! $root) {
            return strip_tags($trimmed);
        }

        self::cleanNode($root);

        $cleanHtml = '';
        foreach ($root->childNodes as $child) {
            $saved = $dom->saveHTML($child);
            if ($saved !== false) {
                $cleanHtml .= $saved;
            }
        }

        return trim($cleanHtml);
    }

    private static function cleanNode(DOMNode $node): void
    {
        $children = [];
        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            if ($child instanceof DOMElement) {
                $tagName = strtolower($child->tagName);

                if (in_array($tagName, self::DANGEROUS_TAGS, true)) {
                    $node->removeChild($child);

                    continue;
                }

                if (! in_array($tagName, self::ALLOWED_TAGS, true)) {
                    // Tag tidak dalam allowlist (misal <h1>, <div>, <span>, <a>, <img>):
                    // Buka (unwrap) konten di dalamnya ke parent, lalu hapus tag tersebut
                    while ($child->hasChildNodes()) {
                        $firstChild = $child->firstChild;
                        if ($firstChild) {
                            $node->insertBefore($firstChild, $child);
                        }
                    }
                    $node->removeChild($child);

                    continue;
                }

                // Tag aman: bersihkan SEMUA atribut (onclick, class, style, src, dll.)
                while ($child->attributes->length > 0) {
                    $attr = $child->attributes->item(0);
                    if ($attr) {
                        $child->removeAttribute($attr->nodeName);
                    }
                }

                self::cleanNode($child);
            }
        }
    }
}
