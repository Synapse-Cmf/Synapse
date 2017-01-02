<?php

namespace Synapse\Page\Bundle\Action\Page;

use Majora\Framework\Repository\RepositoryInterface;
use Synapse\Cmf\Framework\Engine\Exception\InvalidTemplateException;
use Synapse\Page\Bundle\Entity\Page;
use Synapse\Page\Bundle\Event\Page\Event as PageEvent;
use Synapse\Page\Bundle\Event\Page\Events as PageEvents;
use Synapse\Page\Bundle\Generator\PathGeneratorInterface;

/**
 * Page creation action representation.
 */
class CreateAction extends AbstractAction
{
    /**
     * @var Page
     */
    protected $parent;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var PathGeneratorInterface
     */
    protected $pathGenerator;

    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * Construct.
     *
     * @param PathGeneratorInterface $pathGenerator
     */
    public function __construct(PathGeneratorInterface $pathGenerator)
    {
        $this->pathGenerator = $pathGenerator;
    }

    /**
     * Page creation method.
     *
     * @return Page
     */
    public function resolve()
    {
        $this->page = new Page();
        $this->page
            ->setName($this->name)
            ->setParent($this->parent)
            ->setOnline(!empty($this->online))
            ->setTitle($this->title)
            ->setMeta(array('title' => $this->title))
            ->setPath($this->pathGenerator
                ->generatePath($this->page, $this->path ?: '')
            )
        ;

        if ($this->repository->findByPath($this->page->getPath())) {
            throw new InvalidTemplateException(sprintf('Path "%s"already exists.', $this->page->getPath()));
        }

        $this->assertEntityIsValid($this->page, array('Page', 'creation'));

        $this->fireEvent(
            PageEvents::PAGE_CREATED,
            new PageEvent($this->page, $this)
        );

        return $this->page;
    }

    /**
     * Returns action parent.
     *
     * @return Page|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Define action parent.
     *
     * @param Page $parent
     *
     * @return self
     */
    public function setParent(Page $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Returns action path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Define action path.
     *
     * @param string $path
     *
     * @return self
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @param RepositoryInterface $repository
     *
     * @return $this
     */
    public function setRepository(RepositoryInterface $repository)
    {
        $this->repository = $repository;

        return $this;
    }
}
