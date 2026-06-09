<?php

arch()->preset()->php();

arch()->preset()->security()->ignoring('md5');

arch()
    ->expect(['dd', 'dump', 'ray', 'die', 'var_dump',  'ds'])
    ->not->toBeUsed();
