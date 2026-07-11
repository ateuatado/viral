<?php

if (!function_exists('generate_uuid')) {
    function generate_uuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

if (!function_exists('generate_token')) {
    function generate_token(int $length = 6): string
    {
        return bin2hex(random_bytes($length));
    }
}

if (!function_exists('generate_slug')) {
    function generate_slug(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/[áàãâä]/u', 'a', $text);
        $text = preg_replace('/[éèêë]/u', 'e', $text);
        $text = preg_replace('/[íìîï]/u', 'i', $text);
        $text = preg_replace('/[óòõôö]/u', 'o', $text);
        $text = preg_replace('/[úùûü]/u', 'u', $text);
        $text = preg_replace('/[ç]/u', 'c', $text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        return trim($text, '-');
    }
}

if (!function_exists('format_datetime_br')) {
    function format_datetime_br(?string $datetime): string
    {
        if (empty($datetime)) return '-';
        return date('d/m/Y H:i', strtotime($datetime));
    }
}

if (!function_exists('optimize_image')) {
    /**
     * Redimensiona e comprime uma imagem para OG/thumbnail.
     * Retorna o nome do novo arquivo ou false em caso de erro.
     */
    function optimize_image(string $sourcePath, string $destDir, string $prefix = 'opt_', int $maxWidth = 1200, int $maxHeight = 628, int $quality = 80): ?string
    {
        if (!file_exists($sourcePath)) return null;

        $info = getimagesize($sourcePath);
        if (!$info) return null;

        [$origW, $origH, $type] = $info;

        // Supported types
        $srcImg = null;
        switch ($type) {
            case IMAGETYPE_JPEG:
                $srcImg = @imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $srcImg = @imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_WEBP:
                $srcImg = @imagecreatefromwebp($sourcePath);
                break;
            default:
                return null; // unsupported format, keep original
        }

        if (!$srcImg) return null;

        // Calculate new dimensions (fit within bounds, preserve aspect ratio)
        $ratio = min($maxWidth / $origW, $maxHeight / $origH, 1);
        $newW = (int)round($origW * $ratio);
        $newH = (int)round($origH * $ratio);

        $dstImg = imagecreatetruecolor($newW, $newH);
        imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

        // Generate unique filename
        $ext = 'jpg';
        $newName = $prefix . bin2hex(random_bytes(8)) . '.' . $ext;
        $destPath = rtrim($destDir, '/\\') . DIRECTORY_SEPARATOR . $newName;

        // Save as JPEG (universal, smaller)
        $success = imagejpeg($dstImg, $destPath, $quality);

        imagedestroy($srcImg);
        imagedestroy($dstImg);

        if (!$success) return null;

        // Delete original if it's not the same as destination
        if (realpath($sourcePath) !== realpath($destPath)) {
            @unlink($sourcePath);
        }

        return $newName;
    }
}
