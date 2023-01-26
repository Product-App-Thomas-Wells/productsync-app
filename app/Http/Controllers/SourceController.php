<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Source;

class SourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$sproduct_fields = Product::getShopifyProductFields();	
		$params = compact('sproduct_fields');
		//echo "<pre>".print_r($params,true)."</pre>"; die();
        return view('sources.index',$params);
    }
	
	public function getMappingRecords(Request $request){
		$data = $_REQUEST;
		$json = file_get_contents('php://input');
		$data2 = json_decode($json,true);
		if(is_array($data2)){
			$data = array_merge($data,$data2);
		}
		$function = __FUNCTION__;
		//echo "<pre>".print_r(compact('function','data'),true)."</pre>"; die();

		$ret = array('source_id' => $data['id']);
		$records = array();
		$product = Product::where('source',$data['id'])->orderBy('id','desc')->first();
		if($product){
			$tmp = $product->product_data;
			if($tmp){
				$tmp2 = json_decode($tmp,true);
				if(is_array($tmp2)){
					$records[] = array(
						'id' => $product->id,
						'data' => json_decode(json_encode($tmp2),true)
					);
					$ret['records'] = $records;
					$ret['status'] = 'success';
				}
			}
		}
		
		$ret['values'] = array();
		if(!isset($ret['records'])){
			$ret['status'] = 'error';
			$ret['message'] = 'data not found';
		} else {
			$ret['rvalues'] = Product::getRecordValues($ret);
			$source = Source::where('id',$data['id'])->first();
			$tmp = $source->field_mapping;
			if($tmp){
				$tmp2 = json_decode($tmp,true);
				if(is_array($tmp2)){
					$ret['values'] = $tmp2;
					$ret['cvalues'] = Product::getComputedValues($ret);
				}
			}
		}
		
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($ret); die();
	}
	
	public function saveMappingValues(Request $request){
		$data = $_REQUEST;
		$json = file_get_contents('php://input');
		$data2 = json_decode($json,true);
		if(is_array($data2)){
			$data = array_merge($data,$data2);
		}
		$function = __FUNCTION__;
		//echo "<pre>".print_r(compact('function','data'),true)."</pre>"; die();
		$id = $data['id'];
		unset($data['id']);
		$tmp = Source::where('id',$id)->update(['field_mapping' => json_encode($data)]);
		
		header('Content-Type: application/json; charset=utf-8');
		$status = "success";
		$message = "Successfully saved field mapping.";
		$ret = compact('status','message');
		echo json_encode($ret); die();
	}
	
    public function api_search()
    {
        $request = $_REQUEST;
        $json = file_get_contents('php://input');
        $data = json_decode($json,true);
        //echo "<pre>".print_r($request,true)."</pre>"; die();
        $where = '1';
        $messages = Source::whereRaw($where);
        $draw = isset($request['draw']) ? $request['draw'] : 1;
        $recordsTotal = $messages->count();
        $where2 = $where." && 1";
        $where3 = '';
        $search_value2 = isset($request['search']) && isset($request['search']['value']) ? $request['search']['value'] : '';
        $cols = $request['columns'];
        foreach($cols as $col){
            $search_value = isset($col['search']) && isset($col['search']['value']) ? $col['search']['value'] : '';
            $col_name = isset($col['name']) ? $col['name'] : '';
			if($col_name == "actions" || $col_name == "default"){
				continue;
			}
            if($search_value){
                $where2 .= " && ".$col_name." LIKE ('".addslashes($search_value)."%')";
            }
            if($search_value2){
                if($where3) $where3 .= " || ";
                $where3 .= $col_name." LIKE ('%".addslashes($search_value2)."%')";
            }
        }
        if($where3) $where2 .= " && (".$where3.")";
        $messages = Source::whereRaw($where2);
        $recordsFiltered = $messages->count();
        $offset = isset($request['start']) ? $request['start'] : 0;
        $limit = isset($request['length']) ? $request['length'] : 10;
        $messages = Source::select('name','products_updated','stocks_updated','prices_updated','id');
        $messages = $messages->whereRaw($where2);
        $order = isset($request['order']) ? $request['order'] : array();
        foreach($order as $o){
            $oIndex = isset($o['column']) ? $o['column'] : '';
            if($oIndex){
                $oColumn = isset($cols[$oIndex]) ? $cols[$oIndex]['name'] : '';
                $oSort = isset($o['dir']) ? $o['dir'] : '';
                if($oColumn && $oSort){
                    $messages->orderBy($oColumn,$oSort);
                }
            }
        }
        $messages = $messages->skip($offset)->take($limit)->get();
        $data = json_decode(json_encode($messages),true);
        foreach($data as $i => $row){
            //$row['created_at'] = date("Y-m-d H:i:s",strtotime($row['created_at']));
			//$row['source'] = Source::find($row['source'])->name;
			//$statuses = array("New","Pending","Synced");
			//$row['status'] = $statuses[$row['status']];
			$row['default'] = ($i == 0 ? 'Yes' : 'No');
			$row['actions'] = '<a href="" class="map-btn" data-id="'.$row['id'].'">Map Product Fields</a>';
            $data[$i] = $row;
        }
        $ret = compact('draw','recordsTotal','recordsFiltered','data');
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($ret); die();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
		$data2 = $_REQUEST;
		$json = file_get_contents('php://input');
		$data = json_decode($json,true);
		if(is_array($data)){
			$data = array_merge($data,$data2);
		}
		$function = __FUNCTION__;
		echo "<pre>".print_r(compact('function','data'),true)."</pre>"; die();
    }
	
    public function search(Request $request)
    {
        //
		$data2 = $_REQUEST;
		$json = file_get_contents('php://input');
		$data = json_decode($json,true);
		if(is_array($data)){
			$data = array_merge($data,$data2);
		}
		$function = __FUNCTION__;
		echo "<pre>".print_r(compact('function','data'),true)."</pre>"; die();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
