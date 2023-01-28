<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Source;
use App\Models\User;

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
	
	public static function getStatuses(){
		$ret = array("New","Pending","Synced");
		$ret[-1] = "Duplicate Barcode";
		$ret[-2] = "Duplicate Title";
		$ret[-3] = "Fields Not Mapped";
		return($ret);
	}
	
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
			variant_inventory_policy
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
			if(substr($val,0,1) == '='){
				//echo "<pre>".print_r(compact('key'),true)."</pre>";
				$tmp = substr($val,1,strlen($val)-1);
				//echo "<pre>".print_r(compact('tmp'),true)."</pre>";
				$tmp = eval("return ".$tmp.";");
				//echo "<pre>".print_r(compact('tmp'),true)."</pre>";
				$val = $tmp;
			}
			$ret[$key] = $val;
		}
		return($ret);
	}
	
	public static function getMappingData($data){
		$ret = array('product_id' => $data['id']);
		$records = array();
		$product = Product::where('id',$data['id'])->first();
		if($product){
			$tmp = $product->product_data;
			if($tmp){
				$tmp2 = json_decode($tmp,true);
				if(is_array($tmp2)){
					$tmp2 = array_merge(['record_id' => $product->id],$tmp2);
					$tmp2['stock'] = $product->stock;
					$records[] = array(
						'id' => $product->id,
						'data' => json_decode(json_encode($tmp2),true)
					);
					$ret['records'] = $records;
					$ret['status'] = 'success';
					$ret['product_id'] = $product->id;
				}
			}
			
			$barcode = $product->barcode;
			if($barcode){
				$rows = Product::where('barcode',$barcode)->get();
				if(count($rows) > 1){
					//$records = array();
					foreach($rows as $product){
						if($product->id == $data['id']) continue;
						$tmp = $product->product_data;
						if($tmp){
							$tmp2 = json_decode($tmp,true);
							if(is_array($tmp2)){
								$tmp2 = array_merge(['record_id' => $product->id],$tmp2);
								$tmp2['stock'] = $product->stock;
								$records[] = array(
									'id' => $product->id,
									'data' => json_decode(json_encode($tmp2),true)
								);
								$ret['records'] = $records;
								$ret['status'] = 'success';
							}
						}
					}
				}
			}
		}
		
		$product = Product::where('id',$data['id'])->first();
		$ret['values'] = array(
			"handle" => $product->handle ? $product->handle : '',
			"shopify_product_id" => $product->product_id ? $product->product_id : '',
			"shopify_variant_id" => $product->variant_id ? $product->variant_id : ''
		);
		if(!isset($ret['records'])){
			$ret['status'] = 'error';
			$ret['message'] = 'data not found';
		} else {
			$ret['rvalues'] = Product::getRecordValues($ret);
			$ret['source'] = $product->source;
			$ret['ivalues'] = Source::getFieldMapping($ret);
			$tmp = $product->field_mapping;
			$values = array();
			if($tmp){
				$tmp2 = json_decode($tmp,true);
				if(is_array($tmp2)){
					$ret['values'] = array_merge($tmp2,$ret['values']);
				}
			}
			$ret['cvalues'] = Product::getComputedValues($ret);
		}
		return($ret);
	}
	
	public static function getComputedMappingData($data){
		$data = Product::getMappingData($data);
		//echo "<pre>".print_r(compact('data'),true)."</pre>";
		$ret = $data['cvalues'];
		return($ret);
	}
	
	public static function updateShopifyStock($user_id,$id){
		$product = Product::where('id',$id)->first();
		$stock = $product->stock;
		$variant_id = $product->variant_id;
		
		$shop = User::where('id',$user_id)->first();
		
		// get variant details.
		$request = $shop->api()->rest('GET', '/admin/api/2023-01/variants/'.$variant_id.'.json');
		$tmp = $request['body'];
		$tmp = json_decode(json_encode($tmp),true);
		//echo "<pre>".print_r(compact('tmp'),true)."</pre>"; die();
		$variant = isset($tmp['variant']) ? $tmp['variant'] : [];
		if(empty($variant)){
			$title = "Something went wrong!";
			$status = "error";
			$message = "Variant not found.";
			$ret = compact('title','status','message');
			return($ret);
		}
		
		// get variant details.
		$request = $shop->api()->rest('GET', 'admin/api/2023-01/locations.json');
		$tmp = $request['body'];
		$tmp = json_decode(json_encode($tmp),true);
		//echo "<pre>".print_r(compact('tmp'),true)."</pre>"; die();
		$locations = isset($tmp['locations']) ? $tmp['locations'] : [];
		if(empty($locations)){
			$title = "Something went wrong!";
			$status = "error";
			$message = "Locations not found.";
			$ret = compact('title','status','message');
			return($ret);
		}
		
		// update variant stock.
		$json = '{
		   "location_id":'.$locations[0]['id'].',
		   "inventory_item_id":'.$variant['inventory_item_id'].',
		   "available":'.$stock.'
		}';
		$params = json_decode($json,true);
		$request = $shop->api()->rest('POST', '/admin/api/2023-01/inventory_levels/set.json', $params);
		$tmp = $request['body'];
		$tmp = json_decode(json_encode($tmp),true);
		//echo "<pre>".print_r(compact('params','tmp'),true)."</pre>"; die();
		$inventory_level = isset($tmp['inventory_level']) ? $tmp['inventory_level'] : [];
		if(empty($inventory_level)){
			$title = "Something went wrong!";
			$status = "error";
			$message = "Inventory update failed.";
			$ret = compact('title','status','message');
			return($ret);
		}
		
		$title = "Success!";
		$status = "success";
		$message = "Successfully updated stock.";
		$ret = compact('title','status','message');
		return($ret);
	}
	
	public static function newShopifyProduct($user_id,$id){
		
		$product = Product::where('id',$id)->first();
		if($product->handle){
			// error: product already exists in store.
			$title = "Something went wrong!";
			$status = "error";
			$message = "Barcode already exists in store. Please assign a parent id and create a variant instead.";
			$ret = compact('title','status','message');
			return($ret);
		}
		
		// get mapping data. 
		$cvalues = Product::getComputedMappingData(compact('id'));
		//echo "<pre>".print_r(compact('cvalues'),true)."</pre>";
		
		$shop = User::where('id',$user_id)->first();
		
		// check if barcode exists.
		$exists = false;
		if($cvalues['variant_barcode']){
			$query = 'query {
			  products(first: 10, query: "barcode:'.$cvalues['variant_barcode'].'") {
			    edges {
			      node {
					id
			        title
			      }
			    }
			  }
			}';
			$request = $shop->api()->graph($query);
			$tmp = $request['body'];
			$tmp = json_decode(json_encode($tmp),true);
			//echo "<pre>".print_r(compact('query','tmp'),true)."</pre>"; die();
			$exists = !empty($tmp['data']['products']['edges']);
		}
		if($exists){
			// error: product already exists in store.
			$title = "Something went wrong!";
			$status = "error";
			$message = "Barcode already exists in store. Please assign a parent id and create a variant instead.";
			$ret = compact('title','status','message');
			return($ret);
		}
		
		// check if title exists.
		$exists = false;
		if($cvalues['title']){
			$query = 'query {
			  products(first: 10, query: "title:\"'.$cvalues['title'].'\"") {
			    edges {
			      node {
					id
			        title
			      }
			    }
			  }
			}';
			$request = $shop->api()->graph($query);
			$tmp = $request['body'];
			$tmp = json_decode(json_encode($tmp),true);
			//echo "<pre>".print_r(compact('query','tmp'),true)."</pre>"; die();
			$exists = !empty($tmp['data']['products']['edges']);
		}
		if($exists){
			// error: product already exists in store.
			$title = "Something went wrong!";
			$status = "error";
			$message = "Title already exists in store. Please assign a parent id and create a variant instead.";
			$ret = compact('title','status','message');
			return($ret);
		}
		
		// create new product.
		$options = array();
		$variant = array();
		if($cvalues['option1_name'] && $cvalues['option1_value']){
			$options[] = [
				'name' => $cvalues['option1_name'],
				'values' => [
					$cvalues['option1_value']
				] 
			];
			$variant['option'.count($options)] = $cvalues['option1_value'];
		}
		if($cvalues['option2_name'] && $cvalues['option2_value']){
			$options[] = [
				'name' => $cvalues['option2_name'],
				'values' => [
					$cvalues['option2_value']
				] 
			];
			$variant['option'.count($options)] = $cvalues['option2_value'];
		}
		if($cvalues['option3_name'] && $cvalues['option3_value']){
			$options[] = [
				'name' => $cvalues['option3_name'],
				'values' => [
					$cvalues['option3_value']
				] 
			];
			$variant['option'.count($options)] = $cvalues['option3_value'];
		}
		$json = '{
		   "product":{
		      "title":"'.$cvalues['title'].'",
		      "body_html":"'.$cvalues['body_html'].'",
		      "vendor":"'.$cvalues['vendor'].'",
		      "product_type":"'.$cvalues['custom_product_type'].'",
		      "images":[
		         {
		            "src":"'.$cvalues['image_src'].'"
		         }
		      ]
		   }
		}';
		$params = json_decode($json,true);
		if(!empty($options)){
			$params['product']['options'] = $options;
		}
		$variant['barcode'] = $cvalues['variant_barcode'];
		$variant['compare_at_price'] = $cvalues['variant_compare_at_price'];
		$variant['fulfillment_service'] = $cvalues['variant_fulfillment_service'];
		$variant['grams'] = $cvalues['variant_grams'];
		$variant['inventory_policy'] = $cvalues['variant_inventory_policy_'];
		$variant['inventory_management'] = $cvalues['variant_inventory_tracker'];
		$variant['price'] = $cvalues['variant_price'];
		$variant['sku'] = $cvalues['variant_sku'];
		if(!empty($variant)){
			$params['product']['variants'] = [$variant];
		}
		//echo "<pre>".print_r(compact('params'),true)."</pre>"; die();
		$request = $shop->api()->rest('POST', '/admin/api/2023-01/products.json', $params);
		$tmp = $request['body'];
		$tmp = json_decode(json_encode($tmp),true);
		//echo "<pre>".print_r(compact('params','tmp'),true)."</pre>"; die();
		
		$success = false;
		if(isset($tmp['product']) && !empty($tmp['product'])){
			$success = true;
			$product = $tmp['product'];
			$tmp = Product::where('id',$id)->update([
				'handle' => $product['handle'],
				'product_id' => $product['variants'][0]['product_id'],
				'variant_id' => $product['variants'][0]['id']
			]);
		}
		
		// success.
		if($success){
			
			$ret = Product::updateShopifyStock($user_id,$id);
			if($ret['status'] != 'success') return($ret);
			
			$title = "Success!";
			$status = "success";
			$message = "Successfully created shopify product.";
		} else {
			$title = "Something went wrong!";
			$status = "error";
			$message = "Please check your field mapping and try again.";
		}
		
		$ret = compact('title','status','message');
		return($ret);
	}
	
	public static function newShopifyVariant($user_id,$id){
		
		$product = Product::where('id',$id)->first();
		if($product->handle){
			// error: variant already exists in store.
			$title = "Something went wrong!";
			$status = "error";
			$message = "Variant already exists in store.";
			$ret = compact('title','status','message');
			return($ret);
		}
		
		// get mapping data. 
		$cvalues = Product::getComputedMappingData(compact('id'));
		//echo "<pre>".print_r(compact('cvalues'),true)."</pre>";
		
		// check if parent_id has been mapped.
		if(!$cvalues['parent_id']){
			// error: variant already exists in store.
			$title = "Something went wrong!";
			$status = "error";
			$message = "No parent_id specified. Please check your field mapping and try again.";
			$ret = compact('title','status','message');
			return($ret);
		}
		
		// check if parent exists.
		$parent_product = Product::where('id',$cvalues['parent_id'])->first();
		$parent_product_id = '';
		if($parent_product){
			$parent_product_id = $parent_product->product_id;
		}
		if(!$parent_product || $parent_product_id == ''){
			// error: parent product does not exist.
			$title = "Something went wrong!";
			$status = "error";
			$message = "Parent does not exist. Please check your field mapping and try again. ";
			$ret = compact('title','status','message');
			return($ret);
		}
		
		$shop = User::where('id',$user_id)->first();
		
		// get parent product.
		//echo "<pre>".print_r(compact('parent_product_id'),true)."</pre>";
		$request = $shop->api()->rest('GET', '/admin/api/2023-01/products/'.$parent_product_id.'.json');
		$tmp = $request['body'];
		$tmp = json_decode(json_encode($tmp),true);
		//echo "<pre>".print_r(compact('tmp'),true)."</pre>"; die();
		$product = isset($tmp['product']) ? $tmp['product'] : [];
		if(empty($product)){
			$title = "Something went wrong!";
			$status = "error";
			$message = "Product not found.";
			$ret = compact('title','status','message');
			return($ret);
		}
		
		// check if barcode exists.
		$exists = false;
		if($cvalues['variant_barcode']){
			$query = 'query {
			  products(first: 10, query: "sku:'.$cvalues['variant_sku'].' AND barcode:'.$cvalues['variant_barcode'].'") {
			    edges {
			      node {
					id
			        title
			      }
			    }
			  }
			}';
			$request = $shop->api()->graph($query);
			$tmp = $request['body'];
			$tmp = json_decode(json_encode($tmp),true);
			//echo "<pre>".print_r(compact('query','tmp'),true)."</pre>"; die();
			$exists = !empty($tmp['data']['products']['edges']);
		}
		if($exists){
			// error: variant already exists in store.
			$title = "Something went wrong!";
			$status = "error";
			$message = "Variant already exists in store.";
			$ret = compact('title','status','message');
			return($ret);
		}
		
		// check existing product options and variants first.
		$options = isset($product['options']) ? $product['options'] : [];
		$variants = isset($product['variants']) ? $product['variants'] : [];
		//echo "<pre>".print_r(compact('options','variants'),true)."</pre>"; die();
		
		$variant = array();
		for($i = 1; $i <= 3; $i++){
			if($cvalues['option'.$i.'_name'] && $cvalues['option'.$i.'_value']){
				$option_found = false;
				foreach($options as $opt_index => $o){
					if($o['name'] == $cvalues['option'.$i.'_name']){
						$option_found = true;
						if(!in_array($cvalues['option'.$i.'_value'],$o['values'])){
							$o['values'][] = $cvalues['option'.$i.'_value'];
						}
						unset($o['id']);
						unset($o['position']);
						unset($o['product_id']);
						$options[$opt_index] = $o;
						break;
					}
				}
				if(!$option_found){
					$options[] = [
						'name' => $cvalues['option'.$i.'_name'],
						'values' => [
							$cvalues['option'.$i.'_value']
						] 
					];
					$opt_index = count($options) - 1;
				}
				$variant['option'.($opt_index + 1)] = $cvalues['option'.$i.'_value'];
			}
		}
		
		//echo "<pre>".print_r(compact('options','variants','variant'),true)."</pre>"; //die();
		$json = '{
		   "product":{
		      "id":'.$parent_product_id.'
		   }
		}';
		$params = json_decode($json,true);
		if(!empty($options)){
			$params['product']['options'] = $options;
		}
		//echo "<pre>".print_r(compact('params'),true)."</pre>"; die();
		$request = $shop->api()->rest('PUT', '/admin/api/2023-01/products/'.$parent_product_id.'.json', $params);
		$tmp = $request['body'];
		$tmp = json_decode(json_encode($tmp),true);
		//echo "<pre>".print_r(compact('params','tmp'),true)."</pre>"; die();
		
		$success = false;
		$product = isset($tmp['product']) && !empty($tmp['product']) ? $tmp['product'] : [];
		if(!empty($product)){

			// add product variant.
			$variant['barcode'] = $cvalues['variant_barcode'];
			$variant['compare_at_price'] = $cvalues['variant_compare_at_price'];
			$variant['fulfillment_service'] = $cvalues['variant_fulfillment_service'];
			$variant['grams'] = $cvalues['variant_grams'];
			$variant['inventory_policy'] = $cvalues['variant_inventory_policy_'];
			$variant['inventory_management'] = $cvalues['variant_inventory_tracker'];
			$variant['price'] = $cvalues['variant_price'];
			$variant['sku'] = $cvalues['variant_sku'];
			$params = compact('variant');
			//echo "<pre>".print_r(compact('params'),true)."</pre>"; die();
			$request = $shop->api()->rest('POST', '/admin/api/2023-01/products/'.$parent_product_id.'/variants.json', $params);
			$tmp = $request['body'];
			$tmp = json_decode(json_encode($tmp),true);
			//echo "<pre>".print_r(compact('params','tmp'),true)."</pre>"; die();
			
			$variant = isset($tmp['variant']) && !empty($tmp['variant']) ? $tmp['variant'] : [];
			if(!empty($variant)){
				$success = true;
				$tmp = Product::where('id',$id)->update([
					'handle' => $product['handle'],
					'product_id' => $variant['product_id'],
					'variant_id' => $variant['id']
				]);
			}
		}

		if($success){
			$ret = Product::updateShopifyStock($user_id,$id);
			if($ret['status'] != 'success') return($ret);
			
			$title = "Success!";
			$status = "success";
			$message = "Successfully created shopify variant";
		} else {
			$title = "Something went wrong!";
			$status = "error";
			$message = "Please check your field mapping and try again.";
		}
		
		$ret = compact('title','status','message');
		return($ret);
	}
}
