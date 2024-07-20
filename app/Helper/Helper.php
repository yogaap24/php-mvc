<?php

namespace Yogaap\PHP\MVC\APP {
    function header(string $value)
    {
        echo $value;
    }
}

namespace Yogaap\PHP\MVC\Services\Session {
    function setcookie(string $name, string $value) 
    {
        echo "$name: $value";
    }
}

namespace Yogaap\PHP\MVC\Helper {
    function setcookie(string $name, string $value) 
    {
        echo "$name: $value";
    }
}