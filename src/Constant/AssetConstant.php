<?php

namespace VeeZions\BuilderEngine\Constant;

class AssetConstant
{
    // LiipImagine filters
    public const FILTER_THUMBNAIL = 'thumbnail';
    public const FILTER_LOW_QUALITY = 'low_quality';
    // Directories
    public const MEDIA = 'uploads/veezions-builder/media/';
    public const DOCUMENT = 'uploads/veezions-builder/documents/';
    public const CHAT = 'uploads/veezions-builder/chat/';
    public const ACCOUNT = 'uploads/veezions-builder/accounts/';

    // Library per page items
    public const ITEMS_PER_LOAD = 50;
    // Library filetypes accepted
    public const IMAGE_TYPE = 'image/jpeg,image/jpg,image/pjpeg,image/png,image/gif';
    public const VIDEO_TYPE = 'video/mp4,video/mpeg,video/quicktime,video/x-msvideo,video/x-ms-wmv';
    public const DOCUMENT_TYPE = 'application/pdf,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/csv';

    /**
     * @var array<string, array<int, string>>
     */
    public const IMAGE_EXTENSIONS = [
        'jpg' => ['image/jpeg', 'image/jpg', 'image/pjpeg'],
        'jpeg' => ['image/jpeg', 'image/jpg', 'image/pjpeg'],
        'png' => ['image/png'],
        'gif' => ['image/gif'],
    ];

    /**
     * @var array<string, array<int, string>>
     */
    public const VIDEO_EXTENSIONS = [
        'mp4' => ['video/mp4'],
        'mpeg' => ['video/mpeg'],
        'mov' => ['video/quicktime'],
        'avi' => ['video/x-msvideo'],
        'wmv' => ['video/x-ms-wmv'],
    ];

    /**
     * @var array<string, array<int, string>>
     */
    public const DOCUMENT_EXTENSIONS = [
        'pdf' => ['application/pdf'],
        'xls' => ['application/vnd.ms-excel'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        'doc' => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'csv' => ['text/csv', 'text/plain', 'application/csv'],
    ];

    /**
     * @param array<int, string> $liipFilters
     */
    public function __construct(private readonly array $liipFilters)
    {
    }

    /**
     * @return array<int, string>
     */
    public function getFilters(): array
    {
        return $this->liipFilters;
    }

    public static function convertToBytes(string $from): float
    {
        $value = trim($from);
        $lastChar = strtolower($value[strlen($value) - 1]);

        $value = (float) str_replace([',', ' '], '', substr($value, 0, -1));

        return match ($lastChar) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => $value,
        };
    }
}
