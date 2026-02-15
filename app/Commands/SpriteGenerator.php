<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SpriteGenerator extends BaseCommand
{
    protected $group = 'Development';
    protected $name = 'generate:sprite';
    protected $description = 'Generate SVG sprite from icon_helper.php';
    
    public function run(array $params): void
    {
        $helperPath = APPPATH . 'Helpers/icon_helper.php';
        
        if (!file_exists($helperPath)) {
            CLI::error('icon_helper.php not found!');
            return;
        }
        
        $source = file_get_contents($helperPath);
        
        $start = strpos($source, '$icons = [');
        $end = strrpos($source, '];');
        
        if ($start === false || $end === false) {
            CLI::error('Could not find icons array!');
            return;
        }
        
        $arrayContent = substr($source, $start + 10, $end - $start - 10);
        
        $icons = [];
        $lines = explode("\n", $arrayContent);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '//') === 0) continue;
            
            if (preg_match("/'([^']+)'\s*=>\s*'([^']+)'/", $line, $matches)) {
                $icons[$matches[1]] = $matches[2];
            } elseif (preg_match("/'([^']+)'\s*=>/", $line, $matches)) {
                $name = $matches[1];
                $pathStart = strpos($line, "=> '") + 4;
                $pathEnd = strrpos($line, "'");
                if ($pathStart && $pathEnd && $pathEnd > $pathStart) {
                    $path = substr($line, $pathStart, $pathEnd - $pathStart);
                    $icons[$name] = $path;
                }
            }
        }
        
        CLI::write('Found ' . count($icons) . ' icons', 'green');
        
        $spriteContent = $this->buildSprite($icons);
        
        $outputPath = ROOTPATH . 'public/assets/icons/sprite.svg';
        $dir = dirname($outputPath);
        
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        file_put_contents($outputPath, $spriteContent);
        
        CLI::write('Sprite saved to: ' . $outputPath, 'green');
        CLI::write('File size: ' . round(strlen($spriteContent) / 1024, 2) . ' KB', 'green');
    }
    
    private function buildSprite(array $icons): string
    {
        $symbols = [];
        
        foreach ($icons as $name => $paths) {
            $symbols[] = '<symbol id="icon-' . $name . '" viewBox="0 0 24 24">
        ' . $paths . '
    </symbol>';
        }
        
        return '<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="display: none;">
    <defs>
' . implode("\n", $symbols) . '
    </defs>
</svg>';
    }
}
