<?php

use Phinx\Seed\AbstractSeed;


class Books extends AbstractSeed
{
    public function run()
    {
        $data = [
            [
                'name' => 'Peopleware',
                'author' => 'Tom DeMarco, Tim Lister',
                'link' => 'https://www.amazon.co.uk/Peopleware-Productive-Projects-Teams-3rd/dp/0321934113/ref=sr_1_1'
            ],
            [
                'name' => 'Systemantics / The Systems Bible',
                'author' => 'John Gall',
                'link' => 'https://www.amazon.co.uk/Systems-Bible-Beginners-Guide-Large/dp/0961825170/ref=pd_sbs_14_1'
            ],
        ];

        $components = $this->table('book');
        $components->insert($data)
            ->save();
    }
}
