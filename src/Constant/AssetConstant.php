<?php

namespace Xenolabs\XenoEngine\Constant;

class AssetConstant
{
// Directories
    public const MEDIA = 'uploads/media/';
    public const DOCUMENT = 'uploads/documents/';
    public const CHAT = 'uploads/chat/';
    public const ACCOUNT = 'uploads/accounts/';

    // Filters
    public const THUMBNAILS = 'thumbnails';
    public const LOW_QUALITY = 'low_quality';

    // GED per page items
    public const ITEMS_PER_LOAD = 15;
    // GED max file size in octets
    public const MAX_FILE_SIZE = 2000000;
    // GED filetypes accepted
    public const IMAGE_TYPE = 'image/jpeg,image/png,image/gif';
    public const VIDEO_TYPE = 'video/mp4,video/mpeg,video/quicktime,video/x-msvideo,video/x-ms-wmv';
    public const DOCUMENT_TYPE = 'application/pdf,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/csv';

    /**
     * @return array<string>
     */
    public function getFilters(): array
    {
        return [
            self::THUMBNAILS,
            self::LOW_QUALITY,
        ];
    }
}
