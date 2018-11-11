<?php

declare(strict_types=1);

 //to make a new password run this snippet somewhere.
 $options = [
     'cost' => 12
 ];
 echo password_hash(
    'admin',
    PASSWORD_BCRYPT,
    $options
 );