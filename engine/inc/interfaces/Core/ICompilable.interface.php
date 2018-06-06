<?php
namespace Core
{
    interface ICompilable {
        public function compile($utf8 = false, $params = S_FALSE);
    }
}