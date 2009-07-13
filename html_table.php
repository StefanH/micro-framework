<?
class HTMLTable extends MicroObject{
	/**
		* $columns contains a hash with column properties:
		* column : the name of the column
		* type [optional] default: string, supported (extensible): string, icon, link
		* title [optional] default: column
		* for type icon (is also a link by default):
		* icon_name [optional] default: column
		* link_field [optional] default: $row[id]
		* link_as [optional] default: id
		* url 
		* for type link:
		* link_field [optional] default: $row[id]
		* link_as [optional] default: id
		* url
		* rows is an array of arrays
		*/	
	function __construct($columns, $rows=array()){
		$this->columns = $columns;
		$this->rows = $rows;
	}
	
	function render_all(){
		echo '<table>';
		$this->render_cols();
		$this->render_head();
		$this->render_body();
		echo '</table>';		
	}
	
	function done_by_callback($part, $props){
		return $this->do_callback("render_{$props['column']}_{$part}", $props) 
			|| $this->do_callback("render_".array_try($props, 'type', 'string')."_{$part}", $props)
			|| $this->do_callback("render_{$part}", $props);
	}
	
	function render_cols(){
		foreach($this->columns as $props){
			if(!$this->done_by_callback("col", $props))
				echo '<col/>';
		}
	}
	function render_head(){
		echo '<thead><tr>';
		foreach($this->columns as $props){
			if(!$this->done_by_callback("header", $props))
				echo "<th>".array_try($props, 'title', $props['column'])."</th>";
		}
		echo '</tr></thead>';
	}
	function render_body(){
		echo "\n<tbody>\n";
		foreach($this->rows as $row){
			echo '<tr>';
			foreach($this->columns as $props){
				if(!($this->do_callback("render_{$props['column']}_cell", $props, $row) 
					|| $this->do_callback("render_".array_try($props, 'type', 'string')."_cell", $props, $row)
					|| $this->do_callback("render_cell", $props, $row)))
					echo "<td>".$row[$props['column']]."</td>";
			}
			echo "</tr>\n";
		}
		echo "</tbody>\n";
	}
	
	function render_icon_cell($props, $row){
		echo "<td>".linkify(
			iconify(array_try($props, 'icon_name', $props['column'])),
			$props['url'], 
			array(array_try($props, 'link_as', 'id')
			 => $row[array_try($props, 'link_field', 'id')]
		))."</td>";
	}
	
	function render_link_cell($props, $row){
		echo "<td>".linkify($row[$props['column']], $props['url'], 
			array(array_try($props, 'link_as', 'id')
			 => $row[array_try($props, 'link_field', 'id')]
		))."</td>";	
	}
}