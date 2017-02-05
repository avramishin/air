<?php
function newid()
{
    return substr(bcadd(bcmul(mt_rand(), 4294967296), mt_rand()), 0, 16);
}