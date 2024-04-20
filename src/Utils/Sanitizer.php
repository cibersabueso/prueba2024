<?php

namespace App\Utils;

class Sanitizer {
    public static function sanitizeInput($input) {
        return htmlspecialchars(stripslashes(trim($input)));
    }
}