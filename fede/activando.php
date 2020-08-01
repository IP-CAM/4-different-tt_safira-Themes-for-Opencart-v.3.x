<title>ACTIVANDO...</title>
<?php
error_reporting(E_ALL & ~E_NOTICE);
require_once 'excel_reader2.php';
require_once 'db.php';

$data = new Spreadsheet_Excel_Reader("productos.xls");

echo "Listado de pedito completo xls: ".count($data->sheets)."<br /><br />";

$html="<table border='1'>";
for($i=0;$i<count($data->sheets);$i++) // Loop para obtener todas las hojas en un archivo.
{	
	if(count($data->sheets[$i][cells])>0) // Hoja de verificación no vacía
	{
		echo "Sheet $i:<br /><br />Total de filas en la hoja $i  ".count($data->sheets[$i][cells])."<br />";
		for($j=1;$j<=count($data->sheets[$i][cells]);$j++) // Loop utilizado para obtener cada fila de la hoja
		{ 
			$html.="<tr>";
			for($k=1;$k<=count($data->sheets[$i][cells][$j]);$k++) // Este bucle se crea para obtener datos en un formato de tabla.
			{
				$html.="<td>";
				$html.=$data->sheets[$i][cells][$j][$k];
				$html.="</td>";
			}
			$data->sheets[$i][cells][$j][1];
			$id = $eid = mysqli_real_escape_string($connection,$data->sheets[$i][cells][$j][1]);
			$model = mysqli_real_escape_string($connection,$data->sheets[$i][cells][$j][2]);
			$name = mysqli_real_escape_string($connection,$data->sheets[$i][cells][$j][3]);
			$price = mysqli_real_escape_string($connection,$data->sheets[$i][cells][$j][4]);
			$product_id = mysqli_real_escape_string($connection,$data->sheets[$i][cells][$j][5]);
			$quantity = mysqli_real_escape_string($connection,$data->sheets[$i][cells][$j][6]);
			$stock_status_id = mysqli_real_escape_string($connection,$data->sheets[$i][cells][$j][7]);
			$lenguaje_id = mysqli_real_escape_string($connection,$data->sheets[$i][cells][$j][8]);
			$shipping = mysqli_real_escape_string($connection,$data->sheets[$i][cells][$j][9]);
			//$query = "insert into oc_product (`product_id`, `model`, `sku`, `upc`, `ean`, `jan`, `isbn`, `mpn`, `location`, `quantity`, `stock_status_id`, `image`, `manufacturer_id`, `shipping`, `price`, `points`, `tax_class_id`, `date_available`, `weight`, `weight_class_id`, `length`, `width`, `height`, `length_class_id`, `subtract`, `minimum`, `sort_order`, `status`, `viewed`, `date_added`, `date_modified`) values('".$id."','".$model."','', '', '', '', '', '', '', ,'".$quantity."','".$price."','6', NULL, '', '1','".$price."', '0', '', '0000-00-00', '0.00000000', '0', '0.00000000', '0.00000000', '0.00000000', '0', '1', '1', '0', '0', '0', '', '');
			//insert into oc_product_description (`product_id`, `language_id`, `name`, `description`, `tag`, `meta_title`, `meta_description`, `meta_keyword`) VALUES ('0', '1', '".$name."', '".$name."', '".$name."', '".$name."', '".$name."', '".$name."')";
			
			mysqli_query($connection,$query);
			$html.="</tr>";
		}
	}
	
}

$html.="</table>";
echo $html;
echo "Error: ".mysql_error(); 
echo "Error: ".mysqli_error(); 
echo mysql_errno($connection) . ": " . mysql_error($connection) . "\n";
echo mysql_errno($query) . ": " . mysql_error($query) . "\n";
echo "<br />ACTIVANDO DESDE EXCEL";
?>