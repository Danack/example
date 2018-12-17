<?php

declare(strict_types=1);

namespace Example\Model;

class Invoice
{
    private $id;

    private $work;

    private $amount;

    /**
     * Invoice constructor.
     * @param $id
     */
    public function __construct($id)
    {
        $this->id = $id;
        $this->work = 'You owe me money';
        $this->amount = '500';
    }

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
    public function getWork()
    {
        return $this->work;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }
}
