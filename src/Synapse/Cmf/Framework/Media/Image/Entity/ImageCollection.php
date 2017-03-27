<?php

namespace Synapse\Cmf\Framework\Media\Image\Entity;

use Majora\Framework\Model\CollectionableInterface;
use Synapse\Cmf\Framework\Media\Media\Entity\MediaCollection;

/**
 * Image entity collection class.
 */
class ImageCollection extends MediaCollection
{
    /**
     * @see EntityCollection::getEntityClass()
     */
    public function getEntityClass()
    {
        return Image::class;
    }

    /**
     * Sort by placing $ids at the top of the collection (if exists).
     *
     * @param array $ids
     *
     * @return $this
     */
    public function sortIdsAtTheTop(array $ids)
    {
        $newCollection = [];
        $currentCollection = $this->toArray();

        // Push ids from current collection if exists
        foreach ($ids as $id) {
            foreach ($currentCollection as $index => $collection) {
                /** @var CollectionableInterface $collection */
                if ($collection->getId() == $id) {
                    $newCollection[] = $collection;
                    unset($currentCollection[$index]);
                    break;
                }
            }
        }

        // Push rest of current collection to new collection
        foreach ($currentCollection as $collection) {
            $newCollection[] = $collection;
        }

        return new static(array_values($newCollection));
    }
}
