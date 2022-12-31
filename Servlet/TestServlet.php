<?php

class TestServlet extends StdServlet
{

    public function test(Map $in): void
    {
        $this->setData('toto', $in->get('toto'));
    }

}
?>