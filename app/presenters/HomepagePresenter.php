<?php

namespace App\Presenters;

use Nette;
use App\Model;
use \Tracy\Debugger;


class HomepagePresenter extends BasePresenter
{
    /** @var \Nette\Database\Context @inject */
    public $database;
    
	public function renderDefault()
	{
	    Debugger::timer("delete");
	    $this->database->query("DROP TABLE IF EXISTS `benchtest`");
	    Debugger::barDump(Debugger::timer("delete")*1000, "delete");

        Debugger::timer("create");
		$this->database->query("
		CREATE TABLE IF NOT EXISTS `benchtest` (
		    `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
		    `time` int(11) NOT NULL COMMENT 'Time of the record',
		    INDEX `time` (`time`)
		    ) ENGINE=myISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='gpx data'");
        $this->database->getStructure()->rebuild();
		Debugger::barDump(Debugger::timer("create")*1000, "create");
		
        $array = array();
        for($i=0;$i<10000;$i++)
        {
            $item = array();
            $item["time"] = $i;
            $array[] = $item;
        }
        
        
        $items = $array;
        Debugger::timer("foreach array");
        foreach($array as $row)
        {
            $items[] = $row;
        }
        Debugger::barDump(Debugger::timer("foreach array")*1000, "foreach array");
        
        
		Debugger::timer("insert");
        $this->database->table("benchtest")->insert($array);
		Debugger::barDump(Debugger::timer("insert")*1000, "insert");
		
		
		Debugger::timer("select+foreach");
		$totalcount = 10000;
		
		$psize = 3333;
		$pcount = $totalcount/$psize;
		$items = array();
		$item = array();
		
		for($part=0; $part<$pcount; $part++)
		{
            $rows = $this->database->table("benchtest")->select("*")->limit($psize, $part*$psize)->fetchAll();
            $pit = array();
            foreach($rows as $row)
            {
                $item = $row->toArray();
                //$item["id"] = $row["id"];
                //$item["time"] = $row["time"];
                $pit[] = $item;
            }
            
            $items = array_merge($items, $pit);
		}
		Debugger::barDump(Debugger::timer("select+foreach")*1000, "select+foreach");
		Debugger::barDump(count($items));

		
		Debugger::timer("query+foreach");
		$rows = $this->database->query("select * from `benchtest`")->fetchAll();
		$pit = array();
		$item = array();
		foreach($rows as $row)
		{
		    $item["id"] = $row["id"];
		    $item["time"] = $row["time"];
		    $pit[] = $item;
		}
		Debugger::barDump(Debugger::timer("query+foreach")*1000, "query+foreach");
		Debugger::barDump(count($pit));
		
		
	}

}
