<?php
declare(strict_types=1);

function renderIcon(string $iconName): string
{
    $iconPaths = [
        'arrowUpRight' => '<path d="M7 17 17 7M6 7h11v11"/>',
        'arrowRight' => '<path d="M4 12h16m-6-6 6 6-6 6"/>',
        'arrowLeft' => '<path d="M20 12H4m6-6-6 6 6 6"/>',
        'arrowDown' => '<path d="M12 4v16m-6-6 6 6 6-6"/>',
        'arrowUp' => '<path d="M12 20V4m-6 6 6-6 6 6"/>',
        'close' => '<path d="m6 6 12 12M6 18 18 6"/>',
        'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'moon' => '<path d="M20.9 13A9 9 0 0 1 11 3.1 9 9 0 1 0 20.9 13Z"/>',
        'sun' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M2 12h2m16 0h2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4m0-14.2-1.4 1.4M6.3 17.7l-1.4 1.4"/>',
        'code' => '<path d="m7 7-5 5 5 5m10-10 5 5-5 5m-4-14-2 18"/>',
        'penTool' => '<path d="m12 3 8 8-5 10-4-8-8-4 9-6Z"/><path d="m11 13 5-6"/><circle cx="11" cy="13" r="1.5"/>',
        'sparkles' => '<path d="m12 3 2.5 6.5L21 12l-6.5 2.5L12 21l-2.5-6.5L3 12l6.5-2.5L12 3Z"/><path d="m20 2 .6 1.4L22 4l-1.4.6L20 6l-.6-1.4L18 4l1.4-.6L20 2Z"/>',
        'layout' => '<rect x="3" y="4" width="18" height="16" rx="3"/><path d="M3 9h18M9 9v11"/>',
        'palette' => '<path d="M12 3a9 9 0 1 0 0 18h1a2 2 0 0 0 1.4-3.4 1.5 1.5 0 0 1 1-2.6H18a3 3 0 0 0 3-3 9 9 0 0 0-9-9Z"/><circle cx="7.5" cy="11" r=".7"/><circle cx="10" cy="7" r=".7"/><circle cx="15" cy="7" r=".7"/><circle cx="17.5" cy="11" r=".7"/>',
        'layers' => '<path d="m12 3 10 5-10 5L2 8l10-5Zm-10 9 10 5 10-5M2 16l10 5 10-5"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="3"/><path d="m3 7 9 6 9-6"/>',
        'github' => '<path d="M9 19c-4 1-4-2-6-2m12 5v-4a3.5 3.5 0 0 0-1-3c3-.3 6-1.5 6-6a4.7 4.7 0 0 0-1.3-3.3 4.3 4.3 0 0 0-.1-3.2s-1.1-.4-3.6 1.3a12.6 12.6 0 0 0-6 0C6.5 2.1 5.4 2.5 5.4 2.5a4.3 4.3 0 0 0-.1 3.2A4.7 4.7 0 0 0 4 9c0 4.5 3 5.7 6 6a3.5 3.5 0 0 0-1 3v4"/>',
        'linkedin' => '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M7.5 10v7m4-7v7m0-4a3 3 0 0 1 6 0v4"/><path d="M7.5 7h.01"/>',
        'graduation' => '<path d="m12 3 10 5-10 5L2 8l10-5Zm-6 8v6c4 3 8 3 12 0v-6M22 8v8"/>',
        'image' => '<rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8" cy="8" r="1.5"/><path d="m21 15-6-6-12 12"/>',
        'send' => '<path d="m22 2-7 20-4-9-9-4L22 2ZM22 2 11 13"/>',
        'heart' => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z"/>',
    ];
    return '<svg class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . ($iconPaths[$iconName] ?? $iconPaths['sparkles']) . '</svg>';
}

function getTechnologyIconPath(string $technology): ?string
{
    static $technologyIcons = null;

    if ($technologyIcons === null) {
        $technologyIcons = [];
        $toolkitItems = require __DIR__ . '/../data/toolkitData.php';
        foreach ($toolkitItems as $toolkitItem) {
            $technologyIcons[$toolkitItem['name']] = $toolkitItem['icon'];
        }
    }

    $technologyAliases = [
        'Adobe Photoshop' => 'Photoshop',
        'Microsoft SQL Server' => 'MSSQL',
        'SQL Server' => 'MSSQL',
    ];
    $technologyName = $technologyAliases[$technology] ?? $technology;

    return $technologyIcons[$technologyName] ?? null;
}
