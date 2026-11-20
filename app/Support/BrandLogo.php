<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/**
 * Marca del panel: icono + wordmark en HTML, para que la tipografía salga de la
 * página y no de un SVG con fuentes que el navegador no puede cargar.
 */
class BrandLogo
{
    public static function html(): HtmlString
    {
        return new HtmlString(
            '<span style="display:inline-flex;align-items:center;gap:.55rem;height:100%">'
            .'<svg viewBox="0 0 48 54" style="height:1.9rem;width:auto" aria-hidden="true" focusable="false">'
            .'<g transform="rotate(-6 21 25)">'
            .'<rect x="2" y="4" width="38" height="42" rx="8" fill="#1f9e8f"/>'
            .'<rect x="7" y="11" width="13" height="13" rx="3.5" fill="#ffffff" opacity="0.92"/>'
            .'<rect x="22" y="11" width="13" height="13" rx="3.5" fill="#ffffff" opacity="0.92"/>'
            .'<rect x="7" y="28" width="13" height="13" rx="3.5" fill="#ffffff" opacity="0.72"/>'
            .'<rect x="22" y="28" width="13" height="13" rx="3.5" fill="#ffffff" opacity="0.72"/>'
            .'</g></svg>'
            .'<span style="font-family:Outfit,ui-sans-serif,system-ui,sans-serif;font-weight:800;'
            .'font-size:1.2rem;letter-spacing:-.025em;line-height:1;white-space:nowrap">'
            .'Dental<span style="color:#1f9e8f">Flow</span></span>'
            .'</span>'
        );
    }
}
