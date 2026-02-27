<?php
/**
 * Default Avatar Generator - Sert une image par défaut quand l'avatar n'existe pas
 */

// Cache pour 30 jours
header('Cache-Control: public, max-age=2592000');
header('Content-Type: image/svg+xml');

echo '<?xml version="1.0" encoding="UTF-8"?>
<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:#0052A3;stop-opacity:1" />
      <stop offset="100%" style="stop-color:#004080;stop-opacity:1" />
    </linearGradient>
  </defs>
  <circle cx="100" cy="100" r="100" fill="url(#grad)"/>
  <circle cx="100" cy="85" r="35" fill="#fff"/>
  <path d="M 50 160 Q 100 120 150 160" stroke="#fff" stroke-width="20" fill="none" stroke-linecap="round"/>
  <text x="100" y="115" font-family="Arial" font-size="36" fill="#0052A3" text-anchor="middle" font-weight="bold">IE</text>
</svg>';

