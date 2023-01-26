<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Source extends Model
{
    use HasFactory;
	
    protected $fillable = [
        'name',
        'products_updated',
        'stocks_updated',
		'prices_updated',
		'field_mapping'
    ];
	
	public static function getRecordValues($data){
		$ret = array();
		foreach($data['records'] as $record){
			$rid = $record['id'];
			$rdata = $record['data'];
			foreach($rdata as $rkey => $rval){
				$ret['['.$rid.':'.$rkey.']'] = $rval;
			}
		}
		return($ret);
	}
	
	public static function getComputedValues($data){
		$ret = array();
		$rvalues = $data['rvalues'];
		foreach($data['values'] as $key => $val){
			$pattern = "/\[(.+):(.+)\]/i";
			$match = preg_match($pattern, $val);
			if($match){
				foreach($rvalues as $rkey => $rval){
					$val = str_replace($rkey,$rval,$val);
				}
			}
			$ret[$key] = $val;
		}
		return($ret);
	}
	
	public static function getFieldMapping($data){
		$ret = array();
		$source = Source::where('id',$data['source'])->first();
		if($source){
			$tmp = $source->field_mapping;
			if($tmp){
				$tmp2 = json_decode($tmp,true);
				if(is_array($tmp2)){
					$sproduct_id = isset($tmp2['product_id']) ? $tmp2['product_id'] : '';
					$product_id = isset($data['product_id']) ? $data['product_id'] : '';
					if($sproduct_id && $product_id){
						foreach($tmp2 as $key => $val){
							$ret[$key] = str_replace($sproduct_id,$product_id,$val);
						}
					}
				}
			}
		}
		return($ret);
	}
}
