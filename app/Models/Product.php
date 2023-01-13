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
		$sproduct_fields = explode("\n\t\t\t","handle
			title
			body_html
			vendor
			product_type
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
			variant_inventory_policy_
			variant_fulfillment_service
			variant_price
			variant_requires_shipping
			variant_taxable
			variant_barcode
			image_src
			image_position
			image_alt_text
			variant_inventory_qty
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
			size
			google_shopping_custom_label_3
			google_shopping_custom_label_4
			variant_image
			variant__weight_unit
			variant_tax_code
			cost_per_item
			rrp"); 
			
		return($sproduct_fields);
	}
}
