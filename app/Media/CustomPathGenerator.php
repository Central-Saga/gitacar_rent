<?php

namespace App\Media;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    /**
     * Get the path for the given media, relative to the root storage path.
     * Format: {collectionName}/{mediaId}/
     */
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media) . '/';
    }

    /**
     * Get the path for conversions of the given media, relative to the root storage path.
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media) . '/conversions/';
    }

    /**
     * Get the path for responsive images of the given media, relative to the root storage path.
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media) . '/responsive-images/';
    }

    /**
     * Build the base storage path using the collection name and media ID.
     * Example: bukti_pembayaran/5
     */
    protected function getBasePath(Media $media): string
    {
        $collectionName = $media->collection_name ?: 'media';

        return $collectionName . '/' . $media->id;
    }
}
