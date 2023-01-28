<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Source;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ScriptController extends Controller
{
    public function post_products_trilanco(Request $request)
    {
        Log::info(__FUNCTION__);
		$data2 = $_REQUEST;
		$json = file_get_contents('php://input');
		$data = json_decode($json,true);
		if(is_array($data)){
			$data = array_merge($data,$data2);
		}
		$function = __FUNCTION__;
		echo "<pre>".print_r(compact('function','data'),true)."</pre>"; //die();
		
		// get source.
		$source = Source::where('name','trilanco')->first();
		$source_id = $source->id;
		//echo "<pre>".print_r(compact('source_id'),true)."</pre>"; //die();
		
		// get unposted records.
		$processed = 0;
		$limit = 5;
		while($processed < $limit){
			$product = Product::where('source',$source_id)->where('status',0)->first();
			//$product = Product::where('source',$source_id)->where('barcode','5019948108001')->first();
			//$product = Product::where('source',$source_id)->where('barcode','5051274002455')->first();
			if($product){
				// check for duplicate barcodes.
				$product_id = $product->id;
				$barcode = $product->barcode;
				echo "<pre>".print_r(compact('product_id','barcode'),true)."</pre>"; 
				$processed++;
				
				// check dups by barcode.
				$rows = Product::where('id','!=',$product->id)->where('barcode',$product->barcode)->get();
				$dups = json_decode(json_encode($rows),true);
				if(!empty($dups)){
					Product::where('id',$product_id)->update(['status' => -1]);
					foreach($dups as $dup){
						Product::where('id',$dup['id'])->update(['status' => -1]);
					}
					continue;
				}
				
				// check dups by title.
				$rows = Product::where('id','!=',$product->id)->where('title',$product->title)->get();
				$dups = json_decode(json_encode($rows),true);
				if(!empty($dups)){
					Product::where('id',$product_id)->update(['status' => -2]);
					foreach($dups as $dup){
						Product::where('id',$dup['id'])->update(['status' => -2]);
					}
					continue;
				}
				
				// check if fields are mapped.
				$cvalues = Product::getComputedMappingData(['id' => $product_id]);
				if(isset($cvalues['title']) && $cvalues['title'] == ''){
					Product::where('id',$product_id)->update(['status' => -3]);
					continue;
				}
				
				// create shopify product.
				Product::where('id',$product_id)->update(['status' => 1]);
				$shop = User::where('name',env('SHOPIFY_DOMAIN'))->first();
				$ret = Product::newShopifyProduct($shop->id,$product_id);
				if($ret['status'] == 'success'){
					$tmp = Product::where('id',$product_id)->update(['status' => 2]);
				}
			}
		}

	}
	
    public function pull_products_trilanco(Request $request)
    {
        Log::info(__FUNCTION__);
		$data2 = $_REQUEST;
		$json = file_get_contents('php://input');
		$data = json_decode($json,true);
		if(is_array($data)){
			$data = array_merge($data,$data2);
		}
		$function = __FUNCTION__;
		echo "<pre>".print_r(compact('function','data'),true)."</pre>"; //die();
		
		$ftp_host = "trilanco.com";
		$ftp_user = "F00507";
		$ftp_password = "F00507#@";

		//Connect
		echo "<br />Connecting to $ftp_host via FTP...";
		$conn = ftp_connect($ftp_host);
		$login = ftp_login($conn, $ftp_user, $ftp_password);
		$mode = ftp_pasv($conn, TRUE);
		if ((!$conn) || (!$login) || (!$mode)) {
		   die("FTP connection has failed !");
		}
		echo "<br />Login Ok.<br />";

		//$file_list = ftp_nlist($conn, "");
		//foreach ($file_list as $file)
		//{
		  	//if(strpos($file,".csv") !== FALSE){

				// process the following files.
		  		$pfiles = array(
		  			"trilancoProduct_F00507.csv" => array(
		  				"type" => "product",
		  				"barcode" => "BARCODE",
						"sku" => "CODE",
		  				"title" => "DESCRIPTION",
		  				"description" => "FULL DESCRIPTION",
		  				"weight" => "Weight",
		  				"width" => "Width",
		  				"height" => "Height",
		  				"length" => "Length",
		  				"tags" => "Group1,Group2,Brand",
		  				"image" => "Image"
		  			),
		  			"trilancoPrice_F00507.csv" => array(
		  				"type" => "price",
						"title" => "DESCRIPTION",
		  				"barcode" => "BARCODE",
						"sku" => "CODE",
						"price" => "RRP",
		  			),
		  			"trilancoStock_F00507.csv" => array(
		  				"type" => "stock",
						"title" => "DESCRIPTION",
		  				"barcode" => "BARCODE",
						"sku" => "CODE",
		  				"stock" => "Available Stock"
		  			)
		  		);
				
				// check if source exists.
				$source = Source::where('name','trilanco')->first();
				if(empty($source)){
					$irow = array('name' => 'trilanco');
					$tmp = Source::create($irow);
					$source = Source::where('name','trilanco')->first();
				} 
				

				$limit = 10;
 		  		foreach($pfiles as $file => $fprops){
					echo "<br>$file";
					
					// check last update.
					$skip = false;
					$now = strtotime('now');
					switch($fprops['type']){
						case "product":
							// check Monthly Around 09:00
							$last = $source->products_updated;
							if($last){
								//$skip = true;
								$next = date("Y-m-01",strtotime($last." +1 month"));
								$next_unix = strtotime($next);
								//echo "<pre>".print_r(compact('now','last','next','next_unix'),true)."</pre>"; //die();
								if($next_unix > $now) $skip = true;
							}
							break;
						case "price":
						    // check Weekly Around 18:00
							$last = $source->prices_updated;
							if($last){
								$skip = true;
								/*$next = date("Y-m-01",strtotime($last." +1 month"));
								$next_unix = strtotime($next);
								//echo "<pre>".print_r(compact('now','last','next','next_unix'),true)."</pre>"; //die();
								if($next_unix > $now) $skip = true;*/
							}
							break;
						case "stock":
						    // check Hourly Around 09:00
							$last = $source->stocks_updated;
							if($last){
								$skip = true;
								/*$next = date("Y-m-01",strtotime($last." +1 month"));
								$next_unix = strtotime($next);
								//echo "<pre>".print_r(compact('now','last','next','next_unix'),true)."</pre>"; //die();
								if($next_unix > $now) $skip = true;*/
							}
							break;
						default:
							break;
					}
					if($skip) continue;
					
					$i = 0;
					$remote_path = $file;
					$tmp_handle = fopen('php://temp', 'r+');

					if (ftp_fget($conn, $tmp_handle, $remote_path, FTP_ASCII)) {
						rewind($tmp_handle);
						$cols = fgetcsv($tmp_handle);
						while ($row = fgetcsv($tmp_handle)) {
							//$i++;
							//if($i > $limit) break;
							// do stuff
							//echo "<pre>".print_r(compact('cols','row'),true)."</pre>";
							$check = !empty($row) && count($row) == count($cols);
							if(!$check) continue;
							$rdata = array_combine($cols,$row);
							//echo "<pre>".print_r($rdata,true)."</pre>";
							
							// format title.
							$val = $rdata[$fprops['title']];
							$val = ucwords(strtolower($val));
							$rdata[$fprops['title']] = $val;
							
							switch($fprops['type']){
								case "product":
								    $irow = array();
									$mapped = explode(",","title,barcode,sku,description,image");
									foreach($mapped as $col){
										$val = isset($rdata[$fprops[$col]]) && $rdata[$fprops[$col]] ? $rdata[$fprops[$col]] : '';
										$irow[$col] = addslashes($val);
									}
								    $irow['product_data'] = json_encode($rdata);
									$irow['source'] = $source->id;
									//echo "<pre>".print_r($irow,true)."</pre>";

									$where = "";
									if($rdata[$fprops['sku']]){
										if($where) $where .= " OR ";
										$where .= "sku = '".$rdata[$fprops['sku']]."'";
									}
									/*if($rdata[$fprops['barcode']]){
										if($where) $where .= " OR ";
										$where .= "barcode = '".$rdata[$fprops['barcode']]."'";
									}*/
									if($where){
										$erow = Product::where('source',$source->id)->whereRaw($where)->first();
										if(empty($erow)){
											$tmp = Product::create($irow);
										} else {
											$error = "skipped record exists.";
											//echo "<pre>".print_r(compact('rdata','where','error'),true)."</pre>"; die();
											if($erow['product_data'] != $irow['product_data']){
												$tmp = Product::where('source',$source->id)->whereRaw($where)->update($irow);
											}
										}
									} else {
										$error = "no sku or barcode provided.";
										echo "<pre>".print_r(compact('rdata','where','error'),true)."</pre>"; die();
									}
									break;
								case "price":
								    $irow = array();
									$mapped = explode(",","price");
									foreach($mapped as $col){
										$irow[$col] = isset($rdata[$fprops[$col]]) && $rdata[$fprops[$col]] ? $rdata[$fprops[$col]] : '0';
									}
								    $irow['price_data'] = json_encode($rdata);
									$irow['source'] = $source->id;
									//echo "<pre>".print_r($irow,true)."</pre>";

									$where = "";
									if($rdata[$fprops['sku']]){
										if($where) $where .= " OR ";
										$where .= "sku = '".$rdata[$fprops['sku']]."'";
									}
									/*if($rdata[$fprops['barcode']]){
										if($where) $where .= " OR ";
										$where .= "barcode = '".$rdata[$fprops['barcode']]."'";
									}*/
									if($where){
										$erow = Product::where('source',$source->id)->whereRaw($where)->first();
										if(empty($erow)){
											//$tmp = Product::create($irow);
										} else {
											$tmp = Product::where('source',$source->id)->whereRaw($where)->update($irow);
										}
									}
									break;
								case "stock":
								    $irow = array();
									$mapped = explode(",","stock");
									foreach($mapped as $col){
										$irow[$col] = isset($rdata[$fprops[$col]]) && $rdata[$fprops[$col]] ? $rdata[$fprops[$col]] : '0';
									}
								    $irow['stock_data'] = json_encode($rdata);
									$irow['source'] = $source->id;
									//echo "<pre>".print_r($irow,true)."</pre>";
									
									$where = "";
									if($rdata[$fprops['sku']]){
										if($where) $where .= " OR ";
										$where .= "sku = '".$rdata[$fprops['sku']]."'";
									}
									/*if($rdata[$fprops['barcode']]){
										if($where) $where .= " OR ";
										$where .= "barcode = '".$rdata[$fprops['barcode']]."'";
									}*/
									if($where){
										$erow = Product::where('source',$source->id)->whereRaw($where)->first();
										if(empty($erow)){
											//$tmp = Product::create($irow);
										} else {
											$tmp = Product::where('source',$source->id)->whereRaw($where)->update($irow);
										}
									}
									break;
								default:
									break;
							}

							//break;
						}
					}
					fclose($tmp_handle);	
					
					// update source timestamps.
					switch($fprops['type']){
						case "product":
							// check Monthly Around 09:00
							$tmp = Source::find($source->id)->update(['products_updated' => date("Y-m-d",$now)]);
							break;
						case "price":
						    // check Weekly Around 18:00
							$tmp = Source::find($source->id)->update(['prices_updated' => date("Y-m-d",$now)]);
							break;
						case "stock":
						    // check Hourly Around 09:00
							$tmp = Source::find($source->id)->update(['stocks_updated' => date("Y-m-d",$now)]);
							break;
						default:
							break;
					}
					
					// only process one file per run.
					break; 			
		  		}

		  	//}
		//}

		//close
		ftp_close($conn);

    }
}
