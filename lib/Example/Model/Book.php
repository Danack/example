<?php

declare(strict_types=1);

namespace Example\Model;

/**
 * @Entity @Table(name="book")
 **/
class Book
{

    /** @Id @Column(type="integer", name="id") @GeneratedValue **/
    protected $id;

    /** @Column(type="string") **/
    protected $name;

    /** @Column(type="string") **/
    protected $author;

    /** @Column(type="string") **/
    protected $link;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }
}
