<?php
class lavaTablePage extends lavaPage
{
    function dataSource( $dataSource )
    {
        //$this->dataSource = $this->_tables()->fetchTable( $dataSource );
        return $this;
    }
}
?>