<?php

namespace Oro\Bundle\SecurityBundle\Annotation;

/**
 * The annotation that can be used to reference another ACL annotation.
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 */
class AclAncestor implements \Serializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * Constructor
     *
     * @param  array $data
     * @throws \InvalidArgumentException
     */
    public function __construct(array $data = null)
    {
        if ($data === null) {
            return;
        }

        $this->id = $data['value'] ?? null;
        if (empty($this->id) || str_contains($this->id, ' ')) {
            throw new \InvalidArgumentException('ACL id must not be empty or contain blank spaces.');
        }
    }

    /**
     * Gets id of ACL annotation this ancestor is referred to
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(
            array(
                $this->id
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list(
            $this->id
            ) = unserialize($serialized);
    }

    /**
     * The __set_state handler
     *
     * @param array $data Initialization array
     * @return AclAncestor A new instance of a AclAncestor object
     */
    // @codingStandardsIgnoreStart
    public static function __set_state($data)
    {
        $result     = new AclAncestor();
        $result->id = $data['id'];

        return $result;
    }
    // @codingStandardsIgnoreEnd
}
