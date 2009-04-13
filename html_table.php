<?
class HTMLTable extends MicroObject{
	
	function __construct($columns, $rows=array()){
		$this->columns = array_flatten($columns);
		$this->rows = $rows;
		
		$this->render_prefix();
		$this->render_cols();
		$this->render_head();
		$this->render_body();
		$this->render_suffix();
	}
	
	function render_prefix(){echo '<table>';}
	function render_cols(){
		foreach($this->columns as $column){
			if(!($this->do_callback("render_{$column}_col") 
			|| $this->do_callback("render_col", $column)))
				echo '<col/>';
		}
	}
	function render_head(){
		echo '<thead><tr>';
		foreach($this->columns as $column){
			if(!($this->do_callback("render_{$column}_header") 
			|| $this->do_callback("render_header", $column)))
				echo "<th>$column</th>";
		}
		echo '</tr></thead>';
	}
	function render_body(){
		echo '<tbody>';
		foreach($this->rows as $row){
			if(!$this->do_callback("render_row_prefix") echo '<tr>';
			foreach($this->columns as $column){
				if(!($this->do_callback("render_{$column}_cell" $row) 
				|| $this->do_callback("render_cell", $column, $row)))
					echo "<td>{$row[$column]}</td>";
			}
			if(!$this->do_callback("render_row_suffix") echo '</tr>';
		}
		echo '</tr></thead>';
	}
	function render_suffix(){echo '</table>';}
}