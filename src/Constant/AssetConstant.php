<?php

namespace Vision\BuilderEngine\Constant;

class AssetConstant
{
    // Directories
    public const MEDIA = 'uploads/vision-builder/media/';
    public const DOCUMENT = 'uploads/vision-builder/documents/';
    public const CHAT = 'uploads/vision-builder/chat/';
    public const ACCOUNT = 'uploads/vision-builder/accounts/';

    // Library per page items
    public const ITEMS_PER_LOAD = 15;
    // Library max file size in octets
    public const MAX_FILE_SIZE = 2000000;
    // Library filetypes accepted
    public const IMAGE_TYPE = 'image/jpeg,image/png,image/gif';
    public const VIDEO_TYPE = 'video/mp4,video/mpeg,video/quicktime,video/x-msvideo,video/x-ms-wmv';
    public const DOCUMENT_TYPE = 'application/pdf,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/csv';

    public function __construct(private readonly array $liipFilters)
    {

    }

    /**
     * @return array<string>
     */
    public function getFilters(): array
    {
        return $this->liipFilters;
    }
}
