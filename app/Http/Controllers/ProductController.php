<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
