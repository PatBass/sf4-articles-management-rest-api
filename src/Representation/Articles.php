<?php


namespace App\Representation;


use JMS\Serializer\Annotation as Serializer;
use Pagerfanta\Pagerfanta;

class Articles
{
    /**
     * @var Pagerfanta
     * @Serializer\Type("array<App\Entity\Article>")
     */
    public $data;

    public $meta;

    public function __construct(Pagerfanta $data)
    {
        $this->data = $data;

        $this->addMeta('limit', $data->getMaxPerPage());
        $this->addMeta('current_items', count($data->getCurrentPageResults()));
        $this->addMeta('total_items', $data->getNbResults());
        $this->addMeta('offset', $data->getCurrentPageOffsetStart());
    }

    public function addMeta($name, $value)
    {
        if (isset($meta[$name])) {
            throw new \LogicException(sprintf('This meta already exists. You are trying to override this meta, use setMeta instead for %s meta', $name));
        }

        $this->setMeta($name, $value);
    }

    public function setMeta($name, $value)
    {
        $this->meta[$name] = $value;
    }
}