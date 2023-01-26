<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
	
    protected $fillable = [
        'source',
        'title',
		'description',
		'image',
        'sku',
		'barcode',
		'price',
		'stock',
		'product_id',
		'variant_id',
		'status',
		'price_original',
		'product_data',
		'stock_data',
		'price_data',
		'field_mapping'
    ];
	
	public static function getShopifyProductFields(){
		$sproduct_fields = explode("\n\t\t\t","title
			body_html
			vendor
			tags
			published
			option1_name
			option1_value
			option2_name
			option2_value
			option3_name
			option3_value
			variant_sku
			variant_grams
			variant_inventory_tracker
			variant_inventory_qty
			variant_inventory_policy_
			variant_fulfillment_service
			variant_price
			variant_compare_at_price
			variant_requires_shipping
			variant_taxable
			variant_barcode
			image_src
			image_position
			image_alt_text
			gift_card
			seo_title
			seo_description
			google_shopping_google_product_category
			google_shopping_gender
			google_shoppingage_group
			google_shopping_mpn
			google_shopping_google_adwords_grouping
			google_shopping_google_adwords_labels
			google_shopping_condition
			google_shopping_custom_product
			google_shopping__custom_label_0
			google_shopping_custom_label_1
			google_shopping_custom_label_2
			google_shopping_custom_label_3
			google_shopping_custom_label_4
			variant_image
			variant__weight_unit
			variant_tax_code
			cost_per_item
			status
			standard_product_type
			custom_product_type
			rrp
			parent_id"); 
			
		return($sproduct_fields);
	}
	
	public static function getFixedFields(){
		$sproduct_fields = explode(",","handle,shopify_product_id,shopify_variant_id"); 
			
		return($sproduct_fields);
	}
	
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
		$values = $data['values'];
		if(isset($data['ivalues'])){
			$values = $data['ivalues'];
			foreach($data['values'] as $key => $val){
				if($val){
					$values[$key] = $val;
				}
			}
		}
		foreach($values as $key => $val){
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
}
